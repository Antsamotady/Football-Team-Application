<?php

namespace App\Controller;

use App\Entity\Configuration;
use App\Form\ConfigurationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ConfigurationController extends AbstractController
{
    #[Route('/configuration/{id}', name: 'configuration_index')]
    public function index(Request $request, EntityManagerInterface $entityManager , Configuration $config): Response
    {
        $form = $this->createForm(ConfigurationType::class, $config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Enregistrement réussi.');
        }

        return $this->render('configuration/index.html.twig', [
            'form' => $form,
        ]);
    }
}
