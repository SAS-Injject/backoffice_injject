<?php

namespace App\Controller;

use App\Entity\Configuration;
use App\Form\LegalsFormType;
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

  #[Route('/configuration/legals/cgu', name: 'app_configuration_legals_cgu')]
  public function cgu(
    Request $request,
    ConfigurationRepository $configurationRepository,
    EntityManagerInterface $entityManager
  ): Response
  {

    $configuration = $configurationRepository->findOneBy(['name' => 'cgu']);

    $legals_form = $this->createForm(LegalsFormType::class, $configuration);

    $legals_form->handleRequest($request);

    if($legals_form->isSubmitted() && $legals_form->isValid()) {

      $configuration->setValue($legals_form->get('data')->getData());

      $entityManager->persist($configuration);

      try {
        $entityManager->flush();
      } catch (Exception $e) {
          $this->addFlash('danger', 'Une erreur est survenu pendant la modification des éléments dans la base de données.');
          return $this->redirectToRoute('app_configuration_legals_cgu');
      }

      $this->addFlash('success', 'Contenu modifié avec succès');

      return $this->redirectToRoute('app_configuration_legals_cgu');

    }

    return $this->render('configuration/legals/index.html.twig', [
      'title_form' => 'Conditions Générales d\'Utilisation',
      'configuration' => $configuration,
      'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
      'form' => $legals_form
    ]);
  }

  #[Route('/configuration/legals/cgv', name: 'app_configuration_legals_cgv')]
  public function cgv(
    Request $request,
    ConfigurationRepository $configurationRepository,
    EntityManagerInterface $entityManager
  ): Response
  {

    $configuration = $configurationRepository->findOneBy(['name' => 'cgv']);

    $legals_form = $this->createForm(LegalsFormType::class, $configuration);

    $legals_form->handleRequest($request);

    if($legals_form->isSubmitted() && $legals_form->isValid()) {

      $configuration->setValue($legals_form->get('data')->getData());

      $entityManager->persist($configuration);

      try {
        $entityManager->flush();
      } catch (Exception $e) {
          $this->addFlash('danger', 'Une erreur est survenu pendant la modification des éléments dans la base de données.');
          return $this->redirectToRoute('app_configuration_legals_cgv');
      }

      $this->addFlash('success', 'Contenu modifié avec succès');

      return $this->redirectToRoute('app_configuration_legals_cgv');

    }

    return $this->render('configuration/legals/index.html.twig', [
      'title_form' => 'Conditions Générales de Vente',
      'configuration' => $configuration,
      'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
      'form' => $legals_form
    ]);
  }

  #[Route('/configuration/legals/notices', name: 'app_configuration_legals_notices')]
  public function notices(
    Request $request,
    ConfigurationRepository $configurationRepository,
    EntityManagerInterface $entityManager
  ): Response
  {

    $configuration = $configurationRepository->findOneBy(['name' => 'legal']);

    $legals_form = $this->createForm(LegalsFormType::class, $configuration);

    $legals_form->handleRequest($request);

    if($legals_form->isSubmitted() && $legals_form->isValid()) {

      $configuration->setValue($legals_form->get('data')->getData());

      $entityManager->persist($configuration);

      try {
        $entityManager->flush();
      } catch (Exception $e) {
          $this->addFlash('danger', 'Une erreur est survenu pendant la modification des éléments dans la base de données.');
          return $this->redirectToRoute('app_configuration_legals_notices');
      }

      $this->addFlash('success', 'Contenu modifié avec succès');

      return $this->redirectToRoute('app_configuration_legals_notices');

    }

    return $this->render('configuration/legals/index.html.twig', [
      'title_form' => 'Mentions Légales',
      'configuration' => $configuration,
      'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
      'form' => $legals_form
    ]);
  }
}
