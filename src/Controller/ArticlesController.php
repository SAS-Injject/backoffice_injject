<?php

namespace App\Controller;

use App\Helpers\YamlFileHelper;
use App\Repository\ArticlesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticlesController extends AbstractController
{
  #[Route('/articles', name: 'app_articles')]
  public function index(
    ArticlesRepository $articlesRepository,
    Request $request
  ): Response
  {
    $page = $request->query->getInt('page', 1);
    $articles_paginated = $articlesRepository->findAllPaginated($page);
    $pages = $articles_paginated['pages']; 

    $columns = ["Titre", "Dates", "Publié"];
    // dd(YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'));
    return $this->render('articles/index.html.twig', [
      'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
      'page' => $page,
      'pages' => $pages,
      'columns' => $columns,
      'articles' => $articles_paginated['data'],
      'controller_name' => 'ArticlesController',
    ]);
  }

  #[Route('/articles/add', name: 'app_articles_add')]
  public function add(
    Request $request
  ): Response
  {

    return $this->render('articles/add.html.twig', [
      'controller_name' => 'ArticlesController',
    ]);
  }

  #[Route('/articles/edit', name: 'app_articles_edit')]
  public function edit(
    Request $request
  ): Response
  {

    return $this->render('articles/edit.html.twig', [
      'controller_name' => 'ArticlesController',
    ]);
  }

  #[Route('/articles/remove', name: 'app_articles_remove')]
  public function remove(
    Request $request
  ): Response
  {

    return $this->render('articles/edit.html.twig', [
      'controller_name' => 'ArticlesController',
    ]);
  }
}
