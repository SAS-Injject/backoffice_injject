<?php

namespace App\Controller;

use App\Entity\Realization;
use App\Entity\RealizationCategories;
use App\Form\RealizationCategoriesFormType;
use App\Form\RealizationFormType;
use App\Helpers\IconRecapEnum;
use App\Helpers\TableRecap;
use App\Helpers\YamlFileHelper;
use App\Repository\RealizationCategoriesRepository;
use App\Repository\RealizationRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    $columns = ["Titre", "Publié"];

    $created = TableRecap::make_recap("Créé", $realizationRepository, IconRecapEnum::CREATED_ICON);
    $published = TableRecap::make_recap("Publié", $realizationRepository, IconRecapEnum::PUBLISH_ICON, ['is_published' => true]);

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
    EntityManagerInterface $entityManager
  ): Response
  {

    $realization = new Realization();
    $realization_form = $this->createForm(RealizationFormType::class, $realization);

    $realization_form->handleRequest($request);

    if($realization_form->isSubmitted() && $realization_form->isValid()) {

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
  ): Response
  {

    $realization_form = $this->createForm(RealizationFormType::class, $realization);
    $realization_form->handleRequest($request);

    if($realization_form->isSubmitted() && $realization_form->isValid()) {

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
      'article' => $realization,
      'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
      'form' => $realization_form,
      'title_form' => 'Modifier la Réalisation'
    ]);
  }

  #[Route('/realizations/remove/{id}', name: 'app_realizations_remove')]
  public function remove(
    Realization $realization,
    EntityManagerInterface $entityManager   
  ): Response
  {
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
}
