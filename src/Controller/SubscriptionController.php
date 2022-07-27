<?php

namespace App\Controller;

use App\Data\ClientFilterData;
use App\Data\ClientSearchData;
use App\Entity\Abonnement;
use App\Entity\Journal;
use App\Form\AbonnementFormType;
use App\Form\ClientFilterFormType;
use App\Form\ClientSearchFormType;
use App\Repository\AbonnementRepository;
use App\Repository\JournalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/client')]
class SubscriptionController extends AbstractController
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    #[Route('/', name: 'subscription')]
    public function index(): Response
    {
        return $this->render('subscription/index.html.twig', [
            'template_title' => 'Liste des abonnements',
            'meth_name' => 'index',
        ]);
    }

    #[Route('/subsc/post/{uuid}', name: 'receive_sub', methods: ['POST'])]
    public function receive(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, $uuid)
    {
        $receivedData = $request->getContent();

        try {
            $journal = $serializer->deserialize($receivedData, Journal::class, 'json');
            $journal->setCleAbo($uuid);
            
            $em->persist($journal);
            $em->flush();

            return $this->json([
                "status" => 201,
                "message" => "Received and stored"
            ], 201);

        } catch (NotEncodableValueException $e) {
            return $this->json([
                "status" => 400,
                "message" => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/sessionchk/{sessionId}', name: 'chk_session', methods: ['GET'])]
    public function chksession(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, $sessionId, JournalRepository $journalRepository)
    {
        $item = $journalRepository->findOneBy(['sessionId' => $sessionId]);
        if ($item)
            return $this->json([
                "toBeChecked" => false,
            ], 200);
        else
            return $this->json([
                "toBeChecked" => true,
            ], 200);
    }

    #[Route('/subsc/{uuid}', name: 'send_sub', methods: ['GET'])]
    public function send(AbonnementRepository $clientRepo, $uuid)
    {

        $item = $clientRepo->findOneBy(['cleAbo' => $uuid]);

        if (!$item)
            return $this->json([
                "status" => 400,
            ], 400);

        $clientName = $item->getNomClient();
        $dateFin = $item->getDateFin();
        $limitFamille = $item->getNbTitres();
        $limitUser = $item->getNbUsers();
        $limitEntity = $item->getNbEntities();
        $limitAnnonce = $item->getLimitAnnonce();
        
        return $this->json([
            'clientName' => $clientName,
            'dateFin' => $dateFin->format('Y-m-d'),
            'limitFamille' => $limitFamille,
            'limitUser' => $limitUser,
            'limitEntity' => $limitEntity,
            'limitAnnonce' => $limitAnnonce,
            "status" => 200,
        ], 200, []);
    }

    #[Route('/export', name: 'export_client', methods: ['GET'])]
    public function export(AbonnementRepository $clientRepo): Response
    {
        $items = $clientRepo->findAll();

        $handle = fopen('php://memory', 'r+');
        $titre = array('Nom', 'flagActif', 'cleAbo', 'Nb titres', 'Date de fin', 'Nb utilisateurs', 'Nb entités', 'Simulation', 'Limite annonces');

        fputs($handle, chr(239) . chr(187) . chr(191));
        fputcsv($handle, $titre, ';');
        foreach ($items as $item) {
            $result = $item->getExport();
            fputcsv($handle, $result, ';');
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return new Response($content, 200, array(
            'Content-Type' => 'application/force-download; ',
            'Content-Disposition' => 'attachment; filename="abonnement.csv"'
        ));
    }

    #[Route('/import', name: 'import_client', methods: ['POST'])]
    public function import(Request $request, EntityManagerInterface $em, AbonnementRepository $clientRepo): Response
    {
        $entete = array('nom' => 0, 'flagActif' => 1, 'cleAbo' => 2, 'nbTitres' => 3, 'dateFin' => 4, 'nbUtilisateur' => 5, 'nbEntites' => 6, 'simulation' => 7, 'limitAnnonces' => 8);
        $path = __DIR__ . '/../../public/upload/';
        $file = $request->files->get('clientimport');
        $file->move($path, "tmp.csv");

        $row = 1;
        
        if (($handle = fopen($path . "tmp.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $data = $this->decrypteinutf8($data); // put in utf8;

                if ($row == 1) {
                        
                } else {
                    if ($data[$entete['nom']] != '' &&
                        $data[$entete['flagActif']] != '' &&
                        $data[$entete['cleAbo']] != '' &&
                        $data[$entete['nbTitres']] != '' &&
                        $data[$entete['dateFin']] != ''
                    ) {
                        if(!preg_match('/\d{2}-\d{2}-\d{4}/',$data[$entete['dateFin']])){
                            $this->addFlash('error', 'Erreur à la ligne : ' . $row);
                            return $this->redirectToRoute('list_client');
                         }
                         
                        $client = $clientRepo->findOneBy(['nomClient' => $data[$entete['nom']] ]);
                        if (!$client)
                            $client = new Abonnement();
                        $client->setNomClient($data[$entete['nom']]);
                        $client->setFlagActif($data[$entete['flagActif']]);
                        $client->setCleAbo($data[$entete['cleAbo']]);
                        $client->setNbTitres($data[$entete['nbTitres']]);
                        $client->setDateFin(new \Datetime($data[$entete['dateFin']]));
                        $client->setNbUsers((int)$data[$entete['nbUtilisateur']]);
                        $client->setNbEntities((int)$data[$entete['nbEntites']]);
                        $client->setSimulation((int)$data[$entete['simulation']]);
                        $client->setLimitAnnonce((int)$data[$entete['limitAnnonces']]);

                        $em->persist($client);
                        $em->flush();
                    } else {
                        $this->addFlash('error', 'Erreur à la ligne : ' . $row);
                        return $this->redirectToRoute('list_client');
                    }
                }
                $row++;
            }

            fclose($handle);

            //delete tmp file
            unlink($path . "tmp.csv");

            $this->addFlash('success', 'Importation réussie');

            return $this->redirectToRoute('list_client');
        } else {
            $this->addFlash('error', 'Errerur fichier non valid');
        }

        return $this->redirectToRoute('list_client');
    }

    #[Route('/add', name: 'add_client', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $listRoute = $this->urlGenerator->generate('list_client');
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

            return $this->redirectToRoute('list_client', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('subscription/add.html.twig', [
            'template_title' => 'Nouvel abonnement client',
            'list_route' => $listRoute,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/list', name: 'list_client', methods: ['GET', 'POST'])]
    public function list(Request $request, AbonnementRepository $clientRepo): Response
    {
        $data = new ClientSearchData();
        $form = $this->createForm(ClientSearchFormType::class, $data);
        $form->handleRequest($request);

        $filtered_data = new ClientFilterData();
        $filter_form = $this->createForm(ClientFilterFormType::class, $filtered_data);
        $filter_form->handleRequest($request);

        $items = [];

        if ($form->isSubmitted() && $form->isValid())
        {
            $nom = $data->getNom();
            if ($nom == "" || $nom == null)
                $items = $clientRepo->findAll();
            else
                $items = $clientRepo->findSearch($data);
        } elseif ($filter_form->isSubmitted() && $filter_form->isValid()) {
                $items = $clientRepo->findFiltered($filtered_data);
        } else
            $items = $clientRepo->findAll();

        return $this->render('subscription/index.html.twig', [
            'template_title' => 'Liste des abonnements',
            'meth_name' => 'list',
            'form' => $form->createView(),
            'filter_form' => $filter_form->createView(),
            'items' => $items,
        ]);
    }

    #[Route('/{id}', name: 'show_client', methods: ['GET'])]
    public function show(Abonnement $client): Response
    {
        return $this->render('subscription/show.html.twig', [
            'template_title' => 'Compte Client',
            'client' => $client,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit_client', methods: ['GET', 'POST'])]
    public function edit(Request $request, Abonnement $client, EntityManagerInterface $em): Response
    {
        $routeBack = $this->urlGenerator->generate('show_client', array("id" => $client->getId()));

        $form = $this->createForm(AbonnementFormType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Abonnement client bien modifié');

            return $this->redirectToRoute('show_client', [
                'id' => $client->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('subscription/edit.html.twig', [
            'template_title' => 'Modification abonnement',
            'client' => $client,
            'route_back' => $routeBack,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete_client', methods: ['POST'])]
    public function delete(Request $request, Abonnement $client, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->request->get('_token'))) {
            $entityManager->remove($client);
            $entityManager->flush();

            $this->addFlash('success', 'Abonnement supprimé');
        }

        return $this->redirectToRoute('list_client', [], Response::HTTP_SEE_OTHER);
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

    /* Import csv */
    protected function decrypteinutf8($datas) {
        $datas_return = array();
        foreach ($datas as $value) {
            $datas_return[] = (preg_match('!!u', $value)) ? $value : utf8_encode($value);
        }
        return $datas_return;
    }
    
}
