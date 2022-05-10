<?php

namespace App\Controller;

use App\Entity\Annuite;
use App\Form\AnnuiteFormType;
use App\Data\AnnuiteFilterData;
use App\Data\AnnuiteSearchData;
use App\Form\AnnuiteFilterFormType;
use App\Form\AnnuiteSearchFormType;
use App\Repository\AnnuiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/extensions')]
class BillingController extends AbstractController
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    #[Route('/send-annuite', name: 'send_annuite', methods: ['GET'])]
    public function send(AnnuiteRepository $annuiteRepo)
    {
        $items = $annuiteRepo->findAll();

        foreach ($items as $item) {
            $data[] = $item->getExport();
        }

        return $this->json([
            $data,
        ], 200, []);
    }

    
    #[Route('/billing', name: 'billing')]
    public function index(): Response
    {
        return $this->render('billing/old_index.html.twig', [
            'template_title' => 'Annuité',
            'meth_name' => 'index',
        ]);
    }

    #[Route('/list', name: 'list_annuite', methods: ['GET', 'POST'])]
    public function list(Request $request, AnnuiteRepository $annuiteRepo): Response
    {
        $data = new AnnuiteSearchData();
        $form = $this->createForm(AnnuiteSearchFormType::class, $data);
        $form->handleRequest($request);

        $filtered_data = new AnnuiteFilterData();
        $filter_form = $this->createForm(AnnuiteFilterFormType::class, $filtered_data);
        $filter_form->handleRequest($request);

        $items = [];

        if ($form->isSubmitted() && $form->isValid())
        {
            $nom = $data->getNom();
            if ($nom == "" || $nom == null)
                $items = $annuiteRepo->findAll();
            else
                $items = $annuiteRepo->findSearch($data);
        } elseif ($filter_form->isSubmitted() && $filter_form->isValid()) {
                $items = $annuiteRepo->findFiltered($filtered_data);
        } else
            $items = $annuiteRepo->findAll();

        return $this->render('billing/index.html.twig', [
            'template_title' => 'Liste des annuités',
            'meth_name' => 'list',
            'form' => $form->createView(),
            'filter_form' => $filter_form->createView(),
            'items' => $items,
        ]);
    }

    #[Route('/import', name: 'import_annuite', methods: ['POST'])]
    public function import(Request $request, EntityManagerInterface $em, AnnuiteRepository $annuiteRepos): Response
    {
        $entete = array('pays' => 0, 'periode' => 1, 'montant' => 2, 'region' => 3);

        $path = __DIR__ . '/../../public/upload/';
        $file = $request->files->get('fileimport');
        $file->move($path, "tmp.csv");

        $row = 1;
        
        if (($handle = fopen($path . "tmp.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $data = $this->decrypteinutf8($data); // put in utf8;

                if ($row == 1) {
                        
                } else {
                    $ext = new Annuite();
                    $ext->setCodePays('AA');

                    if ($data[$entete['pays']] != '' &&
                        $data[$entete['periode']] != '' &&
                        $data[$entete['montant']] != '' &&
                        $data[$entete['region']] != ''
                    ) {
                        $ext->setPays($data[$entete['pays']]);
                        $ext->setPeriode($data[$entete['periode']]);
                        $ext->setMontants($data[$entete['montant']]);
                        $ext->setRegion($data[$entete['region']]);

                        $em->persist($ext);
                        $em->flush();
                    } else
                        $this->addFlash('error', 'Erreur à la ligne : ' . $row);

                }
                $row++;
            }

            fclose($handle);

            //delete tmp file
            unlink($path . "tmp.csv");

            $this->addFlash('success', 'Importation bien enregistré');

            return $this->redirectToRoute('list_annuite');
        } else {
            $this->addFlash('error', 'Errerur fichier non valid');
        }

        return $this->redirectToRoute('list_annuite');
    }

    #[Route('/{id}', name: 'delete_annuite', methods: ['POST'])]
    public function delete(Request $request, Annuite $annuite, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annuite->getId(), $request->request->get('_token'))) {
            $em->remove($annuite);
            $em->flush();

            $this->addFlash('success', 'Extension supprimé');
        }

        return $this->redirectToRoute('list_annuite', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/export', name: 'export_annuite', methods: ['GET'])]
    public function export(AnnuiteRepository $annuiteRepo): Response
    {
        $items = $annuiteRepo->findAll();

        $handle = fopen('php://memory', 'r+');
        $titre = array('Pays', 'Periode', 'Montant', 'Region');

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
            'Content-Disposition' => 'attachment; filename="annuite.csv"'
        ));
    }

    #[Route('/add', name: 'add_annuite', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $listRoute = $this->urlGenerator->generate('list_annuite');
        // creates a task object and initializes some data for this example
        $annuite = new Annuite();
        $annuite->setCodePays(2);

        $form = $this->createForm(AnnuiteFormType::class, $annuite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($annuite);
            $em->flush();

            $this->addFlash('success', 'Nouvelle annuite enregistrée');

            return $this->redirectToRoute('list_annuite', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('billing/add.html.twig', [
            'template_title' => 'Nouvel abonnement annuite',
            'list_route' => $listRoute,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show_annuite', methods: ['GET'])]
    public function show(Annuite $annuite): Response
    {
        return $this->render('billing/show.html.twig', [
            'template_title' => 'Annuité',
            'ext' => $annuite,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit_annuite', methods: ['GET', 'POST'])]
    public function edit(Request $request, Annuite $annuite, EntityManagerInterface $em): Response
    {
        $routeBack = $this->urlGenerator->generate('show_annuite', array("id" => $annuite->getId()));

        $form = $this->createForm(AnnuiteFormType::class, $annuite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Extension bien modifié');

            return $this->redirectToRoute('show_annuite', [
                'id' => $annuite->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('billing/edit.html.twig', [
            'template_title' => 'Modification Annuite',
            'annuite' => $annuite,
            'route_back' => $routeBack,
            'form' => $form,
        ]);
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
