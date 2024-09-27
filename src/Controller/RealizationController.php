<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RealizationController extends AbstractController
{
    #[Route('/realization', name: 'app_realization')]
    public function index(): Response
    {
        return $this->render('realization/index.html.twig', [
            'controller_name' => 'RealizationController',
        ]);
    }
}
