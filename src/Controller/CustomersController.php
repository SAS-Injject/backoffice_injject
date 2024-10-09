<?php

namespace App\Controller;

use App\Entity\Customers;
use App\Form\CustomersFormType;
use App\Helpers\IconRecapEnum;
use App\Helpers\TableRecap;
use App\Helpers\YamlFileHelper;
use App\Repository\CustomersRepository;
use App\Services\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CustomersController extends AbstractController
{
  #[Route('/customers', name: 'app_customers')]
  public function index(
    CustomersRepository $customersRepository,
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

    $customers_paginated = $customersRepository->findAllPaginated($page);

    $pages = $customers_paginated['pages']; 
    $columns = ["Nom", "Logo"];

    $created = TableRecap::make_recap("Créé", $customersRepository, IconRecapEnum::CREATED_ICON, item_name: "Client");

    return $this->render('customers/index.html.twig', [
      'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
      'page' => $page,
      'pages' => $pages,
      'columns' => $columns,
      'customers' => $customers_paginated['data'],
      'selected_categories' => $categories,
      'recap' => [ $created ],
    ]);
  }

  #[Route('/customers/add', name: 'app_customers_add')]
  public function add(
    Request $request,
    EntityManagerInterface $entityManager,
    PictureService $pictureService
  ): Response
  {

    $customer = new Customers();
    $customer_form = $this->createForm(CustomersFormType::class, $customer);

    $customer_form->handleRequest($request);

    if($customer_form->isSubmitted() && $customer_form->isValid()) {

      if($customer_form->get('logo')->getData() !== null) {
        $logo = $pictureService->add($customer_form->get('logo')->getData(), "");

        $customer->setLogoFile($logo['origin']);
        $customer->setLogoName($logo['name']);

        if($customer_form->get('logo_alt')->getData() !== null) {
          $customer->setLogoAlt($customer_form->get('logo_alt')->getData());
        } else {
          $customer->setLogoAlt("");
        }

        if($customer_form->get('logo_legend')->getData() !== null) {
          $customer->setLogoLegend($customer_form->get('logo_legend')->getData());
        } else {
          $customer->setLogoLegend("");
        }
      } else {
        $this->addFlash('danger', 'Vous devez définir un logo.');
        return $this->redirectToRoute('app_customers');
      }

      $entityManager->persist($customer);

      try {
        $entityManager->flush();
      } catch (Exception $e) {
          $this->addFlash('danger', 'Une erreur est survenu pendant l\'ajout des éléments dans la base de données.');
          return $this->redirectToRoute('app_customers');
      }

      $this->addFlash('success', 'Client créé avec succès.');

      return $this->redirectToRoute('app_customers_edit', ['id' => $customer->getId()]);


    }

    return $this->render('customers/add.html.twig', [
      'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
      'form' => $customer_form,
      'title_form' => 'Nouveau Client'
    ]);
  }

  #[Route('/customers/edit/{id}', name: 'app_customers_edit')]
  public function edit(
    Request $request,
    Customers $customer,
    EntityManagerInterface $entityManager,
    PictureService $pictureService
  ): Response
  {

    $customer_form = $this->createForm(CustomersFormType::class, $customer);
    $customer_form->handleRequest($request);

    if($customer_form->isSubmitted() && $customer_form->isValid()) {

      if($customer_form->get('logo')->getData() !== null) {
        $logo = $pictureService->add($customer_form->get('logo')->getData(), "");

        $customer->setLogoFile($logo['origin']);
        $customer->setLogoName($logo['name']);

        if($customer_form->get('logo_alt')->getData() !== null) {
          $customer->setLogoAlt($customer_form->get('logo_alt')->getData());
        } else {
          $customer->setLogoAlt("");
        }

        if($customer_form->get('logo_legend')->getData() !== null) {
          $customer->setLogoLegend($customer_form->get('logo_legend')->getData());
        } else {
          $customer->setLogoLegend("");
        }
      } else {
        $this->addFlash('danger', 'Vous devez définir un logo.');
        return $this->redirectToRoute('app_customers');
      }

      $entityManager->persist($customer);

      try {
        $entityManager->flush();
      } catch (Exception $e) {
          $this->addFlash('danger', 'Une erreur est survenu pendant l\'ajout des éléments dans la base de données.');
          return $this->redirectToRoute('app_customers');
      }

      $this->addFlash('success', 'Client modifié avec succès.');

      return $this->redirectToRoute('app_customers_edit', ['id' => $customer->getId()]);

    }

    return $this->render('customers/edit.html.twig', [
      'customer' => $customer,
      'config' => YamlFileHelper::getYAMLContent($this->getParameter('controller_dir').'/config/navigation.yaml'),
      'form' => $customer_form,
      'title_form' => 'Modifier le Client'
    ]);
  }

  #[Route('/customers/remove/{id}', name: 'app_customers_remove')]
  public function remove(
    Customers $customer,
    EntityManagerInterface $entityManager   
  ): Response
  {
    $entityManager->remove($customer);
    try {
        $entityManager->flush();
    } catch (Exception $e) {
        $this->addFlash('danger', 'Une erreur est survenu pendant la suppression des éléments dans la base de données.');
        return $this->redirectToRoute('app_customers');
    }

    $this->addFlash('success', 'Client supprimé avec succès');

    return $this->redirectToRoute('app_customers');
  }
}
