<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\ArticlesCategories;
use App\Form\ArticlesCategoriesFormType;
use App\Form\ArticlesFormType;
use App\Helpers\IconRecapEnum;
use App\Helpers\TableRecap;
use App\Helpers\YamlFileHelper;
use App\Repository\ArticlesCategoriesRepository;
use App\Repository\ArticlesRepository;
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

class ArticlesController extends AbstractController
{
  #[Route('/articles', name: 'app_articles')]
  public function index(
    ArticlesRepository $articlesRepository,
    ArticlesCategoriesRepository $articlescategoriesRepository,
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


    $articles_paginated = $articlesRepository->findAllPaginated($page, 10);
    if($categories !== []) {
      $articles_paginated['data'] = array_filter($articles_paginated['data'], function  ($article) use ($categories)  {
        foreach($article->getCategories() as $cat) {
          if (in_array($cat->getId(), $categories)) {
            return $article;
          }
        }
        return null;
      });
    }

    $articles_categories = $articlescategoriesRepository->findAll();
    $pages = $articles_paginated['pages']; 

    $columns = ["Titre", "Dates", "Publié", "Vues"];

    $created = TableRecap::make_recap("Créé", $articlesRepository, IconRecapEnum::CREATED_ICON);
    $published = TableRecap::make_recap("Publié", $articlesRepository, IconRecapEnum::PUBLISH_ICON, ['is_published' => true]);
    $important = TableRecap::make_recap("Important", $articlesRepository, IconRecapEnum::IMPORTANT_ICON, ['is_important' => true]);

    return $this->render('articles/index.html.twig', [
      'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
      'page' => $page,
      'pages' => $pages,
      'columns' => $columns,
      'articles' => $articles_paginated['data'],
      'categories' => $articles_categories,
      'selected_categories' => $categories,
      'recap' => [ $created, $published, $important ],
    ]);
  }

  #[Route('/articles/add', name: 'app_articles_add')]
  public function add(
    Request $request,
    PictureService $pictureService,
    EntityManagerInterface $entityManager
  ): Response
  {

    $article = new Articles();
    $article_form = $this->createForm(ArticlesFormType::class, $article);

    $article_form->handleRequest($request);

    if($article_form->isSubmitted() && $article_form->isValid()) {

      $now = new DateTimeImmutable();

      $article->setContent($article_form->get('data')->getData());

      $article->setWritenAt($now);
      $article->setModifiedAt($now);

      $article->setSeen(0);
      $article->addAuthor($this->getUser());

      if($article_form->get('thumbnail')->getData() !== null) {
        $thumbnail = $pictureService->add($article_form->get('thumbnail')->getData(), "thumbnails/".$article->getId());

        $article->setThumbnailFile($thumbnail['origin']);
        $article->setThumbnailName($thumbnail['name']);

        if($article_form->get('thumbnail_alt')->getData() !== null) {
          $article->setThumbnailAlt($article_form->get('thumbnail_alt')->getData());
        } else {
          $article->setThumbnailAlt("");
        }

        if($article_form->get('thumbnail_legend')->getData() !== null) {
          $article->setThumbnailLegend($article_form->get('thumbnail_legend')->getData());
        } else {
          $article->setThumbnailLegend("");
        }
      } else {
        $this->addFlash('danger', 'Vous devez définir une miniature');
        return $this->redirectToRoute('app_articles');
      }

      if(null !== $request->request->get('saveandpublish')) {
        $article->setPublishedAt($now);
        $article->setIsPublished(true);
      } else {
        $article->setIsPublished(false);
      }

      $entityManager->persist($article);

      try {
        $entityManager->flush();
      } catch (Exception $e) {
          $this->addFlash('danger', 'Une erreur est survenu pendant l\'ajout des éléments dans la base de données.');
          return $this->redirectToRoute('app_articles');
      }

      $this->addFlash('success', 'Article créé avec succès.');

      return $this->redirectToRoute('app_articles_edit', ['id' => $article->getId()]);


    }

    return $this->render('articles/add.html.twig', [
      'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
      'form' => $article_form,
      'title_form' => 'Nouvel Article'
    ]);
  }

  #[Route('/articles/edit/{id}', name: 'app_articles_edit')]
  public function edit(
    Request $request,
    Articles $article,
    EntityManagerInterface $entityManager,
    PictureService $pictureService
  ): Response
  {

    $article_form = $this->createForm(ArticlesFormType::class, $article);
    $article_form->handleRequest($request);

    if($article_form->isSubmitted() && $article_form->isValid()) {

      $now = new DateTimeImmutable();

      $article->setContent($article_form->get('data')->getData());

      $article->setModifiedAt($now);

      $article->addAuthor($this->getUser());

      if($article_form->get('thumbnail')->getData() !== null) {
        $thumbnail = $pictureService->add($article_form->get('thumbnail')->getData(), "thumbnails/".$article->getId());

        $article->setThumbnailFile($thumbnail['origin']);
        $article->setThumbnailName($thumbnail['name']);

        if($article_form->get('thumbnail_alt')->getData() !== null) {
          $article->setThumbnailAlt($article_form->get('thumbnail_alt')->getData());
        } else {
          $article->setThumbnailAlt("");
        }

        if($article_form->get('thumbnail_legend')->getData() !== null) {
          $article->setThumbnailLegend($article_form->get('thumbnail_legend')->getData());
        } else {
          $article->setThumbnailLegend("");
        }
      }

      if(null !== $request->request->get('saveandpublish')) {
        $article->setPublishedAt($now);
        $article->setIsPublished(true);
      } else {
        $article->setIsPublished(false);
      }

      $entityManager->persist($article);

      try {
        $entityManager->flush();
      } catch (Exception $e) {
          $this->addFlash('danger', 'Une erreur est survenu pendant l\'ajout des éléments dans la base de données.');
          return $this->redirectToRoute('app_articles');
      }

      $this->addFlash('success', 'Article modifié avec succès.');

      return $this->redirectToRoute('app_articles_edit', ['id' => $article->getId()]);

    }

    return $this->render('articles/edit.html.twig', [
      'article' => $article,
      'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
      'form' => $article_form,
      'title_form' => 'Modifier l\'Article'
    ]);
  }

  #[Route('/articles/remove/{id}', name: 'app_articles_remove')]
  public function remove(
    Articles $article,
    EntityManagerInterface $entityManager   
  ): Response
  {
    $entityManager->remove($article);
    try {
        $entityManager->flush();
    } catch (Exception $e) {
        $this->addFlash('danger', 'Une erreur est survenu pendant la suppression des éléments dans la base de données.');
        return $this->redirectToRoute('app_articles');
    }

    $this->addFlash('success', 'Article supprimé avec succès');

    return $this->redirectToRoute('app_articles');
  }

  #[Route('/articles/categories', name: 'app_articles_categories')]
  public function index_categories(
    ArticlesCategoriesRepository $articlescategoriesRepository,
    Request $request
  ): Response
  {
    $page = $request->query->getInt('page', 1);
    $articles_categories_paginated = $articlescategoriesRepository->findAllPaginated($page);
    $pages = $articles_categories_paginated['pages']; 

    $columns = ["Titre", "Description"];

    $created = TableRecap::make_recap("Créé", $articlescategoriesRepository, IconRecapEnum::CREATED_ICON, item_name: "Catégorie");

    return $this->render('articles/index_categories.html.twig', [
      'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
      'page' => $page,
      'pages' => $pages,
      'columns' => $columns,
      'categories' => $articles_categories_paginated['data'],
      'recap' => [$created]
    ]);
  }

  #[Route('/articles/categories/add', name: 'app_articles_categories_add')]
  public function add_categories(
    Request $request,
    EntityManagerInterface $entityManager
  ): Response
  {

    $category = new ArticlesCategories();
    $category_form = $this->createForm(ArticlesCategoriesFormType::class, $category);

    $category_form->handleRequest($request);

    if($category_form->isSubmitted() && $category_form->isValid()) {
 

      $entityManager->persist($category);

      try {
        $entityManager->flush();
      } catch (Exception $e) {
          $this->addFlash('danger', 'Une erreur est survenu pendant l\'ajout des éléments dans la base de données.');
          return $this->redirectToRoute('app_articles');
      }

      $this->addFlash('success', 'Catégorie créée avec succès.');

      if(null !== $request->request->get('saveandquit')) {
        return $this->redirectToRoute('app_articles_categories');
      }

      return $this->redirectToRoute('app_articles_categories_edit', ['id' => $category->getId()]);


    }

    return $this->render('articles/add_categories.html.twig', [
      'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
      'form' => $category_form,
      'title_form' => 'Nouvelle Catégorie'
    ]);
  }

  #[Route('/articles/categories/edit/{id}', name: 'app_articles_categories_edit')]
  public function edit_categories(
    Request $request,
    ArticlesCategories $category,
    EntityManagerInterface $entityManager,
  ): Response
  {

    $category_form = $this->createForm(ArticlesCategoriesFormType::class, $category);
    $category_form->handleRequest($request);

    if($category_form->isSubmitted() && $category_form->isValid()) {


      $entityManager->persist($category);

      try {
        $entityManager->flush();
      } catch (Exception $e) {
          $this->addFlash('danger', 'Une erreur est survenu pendant l\'ajout des éléments dans la base de données.');
          return $this->redirectToRoute('app_articles');
      }

      $this->addFlash('success', 'Catégorie modifiée avec succès.');

      if(null !== $request->request->get('saveandquit')) {
        return $this->redirectToRoute('app_articles_categories');
      }

      return $this->redirectToRoute('app_articles_categories_edit', ['id' => $category->getId()]);

    }

    return $this->render('articles/add_categories.html.twig', [
      'category' => $category,
      'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
      'form' => $category_form,
      'title_form' => 'Modifier la Catégorie'
    ]);
  }

  #[Route('/articles/categories/remove/{id}', name: 'app_articles_categories_remove')]
  public function remove_categories(
    ArticlesCategories $category,
    EntityManagerInterface $entityManager   
  ): Response
  {
    $entityManager->remove($category);
    try {
        $entityManager->flush();
    } catch (Exception $e) {
        $this->addFlash('danger', 'Une erreur est survenu pendant la suppression des éléments dans la base de données.');
        return $this->redirectToRoute('app_articles_categories');
    }

    $this->addFlash('success', 'Categorie supprimé avec succès');

    return $this->redirectToRoute('app_articles_categories');
  }


  #[Route('/marketing/saveImageFromEditor', name: 'saveImageFromEditor')]
    public function saveImageFromEditor(
      Request $request,
      ParameterBagInterface $params
    ): Response {

      $data_string = $request->request->get('data');
      $data = json_decode($data_string, true);
      foreach($data['blocks'] as $index => $block) {
        if($block['type'] === "image") {
          $file = hash("sha256", uniqid());
          $path = $params->get('article_image_directory');

          $base64 = $block['data']['url'];

          if(str_contains($base64, 'base64')) {
            $output = PictureService::base64_to_image($base64, $path, $file);
          } else {
            $output = explode('/',$base64);
            $output = array_pop($output);
          }
          $data['blocks'][$index]['data']['url'] = $request->server->get('HTTP_ORIGIN').'/assets/imgs/articles/'.$output;
        }
      }
      // $data = json_encode(htmlspecialchars($data));

      return new JsonResponse([
        'data' => $data
      ]);
    }
}
