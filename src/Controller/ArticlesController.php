<?php

namespace App\Controller;

use App\Repository\ArticlesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticlesController extends AbstractController
{
  #[Route('/articles', name: 'app_articles')]
  public function index(
    ArticlesRepository $articlesRepository
  ): Response
  {
    

    return $this->render('articles/index.html.twig', [
      'articles' => $articlesRepository->findAll(),
      'controller_name' => 'ArticlesController',
    ]);
  }
}
