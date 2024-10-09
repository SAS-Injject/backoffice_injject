<?php

namespace App\Controller;

use App\Entity\Realization;
use App\Entity\RealizationCategories;
use App\Entity\RealizationPhoto;
use App\Form\RealizationCategoriesFormType;
use App\Form\RealizationFormType;
use App\Helpers\IconRecapEnum;
use App\Helpers\TableRecap;
use App\Helpers\YamlFileHelper;
use App\Repository\RealizationCategoriesRepository;
use App\Repository\RealizationPhotoRepository;
use App\Repository\RealizationRepository;
use App\Services\PictureService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RealizationController extends AbstractController
{
  #[Route('/realizations', name: 'app_realizations')]
  public function index(
    RealizationRepository $realizationRepository,
    RealizationCategoriesRepository $realizationCategoriesRepository,
    Request $request
  ): Response
  {
    $page = $request->query->getInt('page', 1);
    $categories = $request->query->getString('categories', "");
    if( !empty($categories)) {
      $categories = json_decode($categories);
    } else {
      $categories = [];
    }

    $realizations_paginated = $realizationRepository->findAllPaginated($page);
    if($categories !== []) {
      $realizations_paginated['data'] = array_filter($realizations_paginated['data'], function  ($realization) use ($categories)  {
        foreach($realization->getCategories() as $cat) {
          if (in_array($cat->getId(), $categories)) {
            return $realization;
          }
        }
        return null;
      });
    }

    $pages = $realizations_paginated['pages']; 
    $realization_categories = $realizationCategoriesRepository->findAll();
    $columns = ["Titre", "Client", "Publié"];

    $created = TableRecap::make_recap("Créé", $realizationRepository, IconRecapEnum::CREATED_ICON, item_name:"Réalisation");
    $published = TableRecap::make_recap("Publié", $realizationRepository, IconRecapEnum::PUBLISH_ICON, ['is_published' => true], item_name:"Réalisation");

    return $this->render('realizations/index.html.twig', [
      'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
      'page' => $page,
      'pages' => $pages,
      'columns' => $columns,
      'realizations' => $realizations_paginated['data'],
      'categories' => $realization_categories,
      'selected_categories' => $categories,
      'recap' => [ $created, $published ],
    ]);
  }

  #[Route('/realizations/add', name: 'app_realizations_add')]
  public function add(
    Request $request,
    EntityManagerInterface $entityManager,
    PictureService $pictureService,
    ParameterBagInterface $params
  ): Response
  {

    $realization = new Realization();
    $realization_form = $this->createForm(RealizationFormType::class, $realization);

    $realization_form->handleRequest($request);

    if($realization_form->isSubmitted() && $realization_form->isValid()) {

      foreach($realization_form->get('photos')->getData() as $preProcessedPhoto) {
        $file = $pictureService->add($preProcessedPhoto, "realizations/".$realization->getId());

        $photo = new RealizationPhoto();
        $photo->setFile($file['origin']);
        $photo->setName($file['name']);
        $photo->setAlt("");
        $photo->setLegend("");

        $entityManager->persist($photo);

        $realization->addPhoto($photo);
      }

      if(null !== $request->request->get('saveandpublish')) {
        $realization->setIsPublished(true);
      } else {
        $realization->setIsPublished(false);
      }

      $entityManager->persist($realization);

      try {
        $entityManager->flush();
      } catch (Exception $e) {
          $this->addFlash('danger', 'Une erreur est survenu pendant l\'ajout des éléments dans la base de données.');
          return $this->redirectToRoute('app_realizations');
      }

      $this->addFlash('success', 'Réalisation créée avec succès.');

      return $this->redirectToRoute('app_realizations_edit', ['id' => $realization->getId()]);


    }

    return $this->render('realizations/add.html.twig', [
      'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
      'form' => $realization_form,
      'title_form' => 'Nouvelle Réalisation'
    ]);
  }

  #[Route('/realizations/edit/{id}', name: 'app_realizations_edit')]
  public function edit(
    Request $request,
    Realization $realization,
    EntityManagerInterface $entityManager,
    PictureService $pictureService,
  ): Response
  {

    $realization_form = $this->createForm(RealizationFormType::class, $realization);
    $realization_form->handleRequest($request);

    if($realization_form->isSubmitted() && $realization_form->isValid()) {

      foreach($realization_form->get('photos')->getData() as $preProcessedPhoto) {
        $file = $pictureService->add($preProcessedPhoto, "realizations/".$realization->getId());

        $photo = new RealizationPhoto();
        $photo->setFile($file['origin']);
        $photo->setName($file['name']);
        $photo->setAlt("");
        $photo->setLegend("");

        $entityManager->persist($photo);

        $realization->addPhoto($photo);
      }

      if(null !== $request->request->get('saveandpublish')) {
        $realization->setIsPublished(true);
      } else {
        $realization->setIsPublished(false);
      }

      $entityManager->persist($realization);

      try {
        $entityManager->flush();
      } catch (Exception $e) {
          $this->addFlash('danger', 'Une erreur est survenu pendant l\'ajout des éléments dans la base de données.');
          return $this->redirectToRoute('app_realizations');
      }

      $this->addFlash('success', 'Réalisation modifiée avec succès.');

      return $this->redirectToRoute('app_realizations_edit', ['id' => $realization->getId()]);

    }

    return $this->render('realizations/edit.html.twig', [
      'realization' => $realization,
      'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
      'form' => $realization_form,
      'title_form' => 'Modifier la Réalisation'
    ]);
  }

  #[Route('/realizations/remove/{id}', name: 'app_realizations_remove')]
  public function remove(
    Realization $realization,
    EntityManagerInterface $entityManager,
    PictureService $pictureService   
  ): Response
  {

    foreach($realization->getPhotos() as $photo) {
      $pictureService->delete($photo->getFile(), "realisations");
      $entityManager->remove($photo);
    }

    $entityManager->remove($realization);
    try {
        $entityManager->flush();
    } catch (Exception $e) {
        $this->addFlash('danger', 'Une erreur est survenu pendant la suppression des éléments dans la base de données.');
        return $this->redirectToRoute('app_realizations');
    }

    $this->addFlash('success', 'Réalisation supprimée avec succès');

    return $this->redirectToRoute('app_realizations');
  }

  #[Route('/realizations/categories', name: 'app_realizations_categories')]
  public function index_categories(
    RealizationCategoriesRepository $realizationcategoriesRepository,
    Request $request
  ): Response
  {
    $page = $request->query->getInt('page', 1);
    $articles_categories_paginated = $realizationcategoriesRepository->findAllPaginated($page);
    $pages = $articles_categories_paginated['pages']; 

    $columns = ["Titre", "Description"];

    $created = TableRecap::make_recap("Créé", $realizationcategoriesRepository, IconRecapEnum::CREATED_ICON, item_name: "Catégorie");

    return $this->render('realizations/index_categories.html.twig', [
      'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
      'page' => $page,
      'pages' => $pages,
      'columns' => $columns,
      'categories' => $articles_categories_paginated['data'],
      'recap' => [$created]
    ]);
  }

  #[Route('/realizations/categories/add', name: 'app_realizations_categories_add')]
  public function add_categories(
    Request $request,
    EntityManagerInterface $entityManager
  ): Response
  {

    $category = new RealizationCategories();
    $category_form = $this->createForm(RealizationCategoriesFormType::class, $category);

    $category_form->handleRequest($request);

    if($category_form->isSubmitted() && $category_form->isValid()) {
 
      $entityManager->persist($category);

      try {
        $entityManager->flush();
      } catch (Exception $e) {
          $this->addFlash('danger', 'Une erreur est survenu pendant l\'ajout des éléments dans la base de données.');
          return $this->redirectToRoute('app_realizations');
      }

      $this->addFlash('success', 'Catégorie créée avec succès.');

      if(null !== $request->request->get('saveandquit')) {
        return $this->redirectToRoute('app_realizations_categories');
      }

      return $this->redirectToRoute('app_realizations_categories_edit', ['id' => $category->getId()]);
    }

    return $this->render('realizations/add_categories.html.twig', [
      'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
      'form' => $category_form,
      'title_form' => 'Nouvelle Catégorie'
    ]);
  }

  #[Route('/realizations/categories/edit/{id}', name: 'app_realizations_categories_edit')]
  public function edit_categories(
    Request $request,
    RealizationCategories $category,
    EntityManagerInterface $entityManager,
  ): Response
  {

    $category_form = $this->createForm(RealizationCategoriesFormType::class, $category);
    $category_form->handleRequest($request);

    if($category_form->isSubmitted() && $category_form->isValid()) {


      $entityManager->persist($category);

      try {
        $entityManager->flush();
      } catch (Exception $e) {
          $this->addFlash('danger', 'Une erreur est survenu pendant l\'ajout des éléments dans la base de données.');
          return $this->redirectToRoute('app_realizations');
      }

      $this->addFlash('success', 'Catégorie modifiée avec succès.');

      if(null !== $request->request->get('saveandquit')) {
        return $this->redirectToRoute('app_realizations_categories');
      }

      return $this->redirectToRoute('app_realizations_categories_edit', ['id' => $category->getId()]);

    }

    return $this->render('realizations/add_categories.html.twig', [
      'category' => $category,
      'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
      'form' => $category_form,
      'title_form' => 'Modifier la Catégorie'
    ]);
  }

  #[Route('/realizations/categories/remove/{id}', name: 'app_realizations_categories_remove')]
  public function remove_categories(
    RealizationCategories $category,
    EntityManagerInterface $entityManager   
  ): Response
  {
    $entityManager->remove($category);
    try {
        $entityManager->flush();
    } catch (Exception $e) {
        $this->addFlash('danger', 'Une erreur est survenu pendant la suppression des éléments dans la base de données.');
        return $this->redirectToRoute('app_realizations_categories');
    }

    $this->addFlash('success', 'Categorie supprimée avec succès');

    return $this->redirectToRoute('app_realizations_categories');
  }

  #[Route('/realizations/photo/remove/{id}', name: 'app_realizations_photo_remove')]
  public function remove_photo(
    RealizationPhoto $realizationPhoto,
    EntityManagerInterface $entityManager,
    PictureService $pictureService,
    Request $request    
  ): Response{

    $data = json_decode($request->getContent(), true);

    if($this->isCsrfTokenValid('delete' . $realizationPhoto->getId(), $data['_token'])) {
      $pictureService->delete($realizationPhoto->getFile(), "realisations");
      $entityManager->remove($realizationPhoto);
      try {
          $entityManager->flush();
      } catch (Exception $e) {

      }

      return new JsonResponse(['success' => 'Ok'], 200);
    }

    return new JsonResponse(['error' => 'Token'], 400);
  }

}
