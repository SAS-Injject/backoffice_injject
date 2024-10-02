<?php

namespace App\Controller;

use App\Helpers\YamlFileHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ConfigurationController extends AbstractController
{
    #[Route('/configuration', name: 'app_configuration')]
    public function index(): Response
    {
        return $this->render('configuration/index.html.twig', [
          'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
        ]);
    }
}
