<?php

namespace App\Controller;

use App\Helpers\YamlFileHelper;
use App\Repository\ConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ConfigurationController extends AbstractController
{
  #[Route('/configuration', name: 'app_configuration')]
  public function index(
    ConfigurationRepository $configurationRepository,
    Request $request,
    EntityManagerInterface $entityManager
  ): Response
  {

    $configurations = $configurationRepository->findAll();
    if(null !== $request->request->get("_csrf_token") && $this->isCsrfTokenValid('configurate', $request->request->get("_csrf_token"))) {
      $modified = false;
      $entries = $request->request->all();
      foreach($configurations as $configuration) {
        if($configuration->getType() === 'string') {
          $configuration->setValue($entries[$configuration->getId()]);
        } elseif ($configuration->getType() === 'boolean') {
          $configuration->setValue(isset($entries[$configuration->getId()]) && $entries[$configuration->getId()] === 'on'? true : false);
        }
        $entityManager->persist($configuration);
        $modified = true;

      }

      if($modified) {
        try {
          $entityManager->flush();
        } catch (Exception $e) {
            $this->addFlash('danger', 'Une erreur est survenu pendant la modification des paramètres.');
            return $this->redirectToRoute('app_configuration');
        }
  
        $this->addFlash('success', 'Parmètres modifiés avec succès');
      }

      return $this->redirectToRoute('app_configuration');
    }



    return $this->render('configuration/index.html.twig', [
      'configurations' => $configurations,
      'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
    ]);
  }
}
