<?php

namespace App\Controller;

use App\Data\ExtensionsFilterData;
use App\Data\ExtensionsSearchData;
use App\Form\ExtensionsFilterFormType;
use App\Form\ExtensionsSearchFormType;
use App\Repository\ExtensionsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/extensions')]
class BillingController extends AbstractController
{
    #[Route('/billing', name: 'billing')]
    public function index(): Response
    {
        return $this->render('billing/old_index.html.twig', [
            'template_title' => 'Annuité',
            'meth_name' => 'index',
        ]);
    }

    #[Route('/list', name: 'list_extensions', methods: ['GET', 'POST'])]
    public function list(Request $request, ExtensionsRepository $extensionsRepo): Response
    {
        $data = new ExtensionsSearchData();
        $form = $this->createForm(ExtensionsSearchFormType::class, $data);
        $form->handleRequest($request);

        $filtered_data = new ExtensionsFilterData();
        $filter_form = $this->createForm(ExtensionsFilterFormType::class, $filtered_data);
        $filter_form->handleRequest($request);

        $items = [];

        if ($form->isSubmitted() && $form->isValid())
        {
            $nom = $data->getNom();
            if ($nom == "" || $nom == null)
                $items = $extensionsRepo->findAll();
            else
                $items = $extensionsRepo->findSearch($data);
        } elseif ($filter_form->isSubmitted() && $filter_form->isValid()) {
                $items = $extensionsRepo->findFiltered($filtered_data);
        } else
            $items = $extensionsRepo->findAll();

        return $this->render('billing/index.html.twig', [
            'template_title' => 'Liste des extensions',
            'meth_name' => 'list',
            'form' => $form->createView(),
            'filter_form' => $filter_form->createView(),
            'items' => $items,
        ]);
    }

}
