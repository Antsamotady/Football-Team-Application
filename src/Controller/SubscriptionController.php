<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Form\AbonnementFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/client')]
class SubscriptionController extends AbstractController
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    #[Route('/list', name: 'subscription')]
    public function index(): Response
    {
        return $this->render('subscription/index.html.twig', [
            'template_title' => 'Liste des abonnements',
            'meth_name' => 'index',
        ]);
    }

    #[Route('/add', name: 'add_client', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $listRoute = $this->urlGenerator->generate('subscription');
        // creates a task object and initializes some data for this example
        $client = new Abonnement();
        $client->setCleAbo($this->guidv4());
        $client->setSimulation(0);

        $form = $this->createForm(AbonnementFormType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($client);
            $em->flush();

            $this->addFlash('success', 'Nouveau client enregistré');

            return $this->redirectToRoute('subscription', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('subscription/add.html.twig', [
            'template_title' => 'Nouvel abonnement client',
            'list_route' => $listRoute,
            'form' => $form->createView(),
        ]);
    }

    protected function guidv4($data = null) {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);
    
        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    
        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    

}
