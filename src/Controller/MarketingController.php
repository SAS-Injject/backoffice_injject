<?php

namespace App\Controller;

use App\Helpers\YamlFileHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MarketingController extends AbstractController
{
    #[Route('/marketing', name: 'app_marketing')]
    public function index(): Response
    {
        return $this->render('marketing/index.html.twig', [
            'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
        ]);
    }
}
