<?php

namespace App\Controller;

use App\Helpers\YamlFileHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
          'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
        ]);
    }
}
