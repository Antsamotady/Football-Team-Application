<?php

namespace App\Controller;

use App\Entity\Annuite;
use App\Entity\AnnuiteNice;
use App\Form\AnnuiteFormType;
use App\Entity\AnnuiteLocarno;
use App\Data\AnnuiteFilterData;
use App\Data\AnnuiteSearchData;
use App\Form\AnnuiteFilterFormType;
use App\Form\AnnuiteSearchFormType;
use App\Form\AnnuiteLocarnoFormType;
use App\Repository\AnnuiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AnnuiteNiceRepository;
use App\Repository\AnnuiteLocarnoRepository;
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

    #[Route('/import', name: 'import_annuite', methods: ['POST'])]
    public function import(Request $request, EntityManagerInterface $em, AnnuiteRepository $annuiteRepo): Response
    {
        $entete = array('nom' => 0, 'pays' => 1, 'periode' => 2, 'montant' => 3, 'region' => 4);
        $path = __DIR__ . '/../../public/upload/';
        $file = $request->files->get('fileimport');
        $file->move($path, "tmp.csv");

        $row = 1;
        
        if (($handle = fopen($path . "tmp.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $data = $this->decrypteinutf8($data); // put in utf8;

                if ($row == 1) {
                        
                } else {
                    if ($data[$entete['nom']] != '' &&
                        $data[$entete['pays']] != '' &&
                        $data[$entete['periode']] != '' &&
                        $data[$entete['montant']] != '' &&
                        $data[$entete['region']] != ''
                    ) {
                        $annuite = $annuiteRepo->findOneBy(['name' => $data[$entete['nom']] ]);
                        if (!$annuite)
                            $annuite = new Annuite();
                        
                        $annuite->setName($data[$entete['nom']]);
                        $annuite->setPays($data[$entete['pays']]);
                        $annuite->setPeriode($data[$entete['periode']]);
                        $annuite->setMontants($data[$entete['montant']]);
                        $annuite->setRegion($data[$entete['region']]);

                        $em->persist($annuite);
                        $em->flush();
                    } else
                        $this->addFlash('error', 'Erreur à la ligne : ' . $row);

                }
                $row++;
            }

            fclose($handle);

            //delete tmp file
            unlink($path . "tmp.csv");

            $this->addFlash('success', 'Importation réussie');

            return $this->redirectToRoute('list_annuite');
        } else {
            $this->addFlash('error', 'Errerur fichier non valid');
        }

        return $this->redirectToRoute('list_annuite');
    }

    #[Route('/import-locarno', name: 'import_locarno', methods: ['POST'])]
    public function importLocarno(Request $request, EntityManagerInterface $em, AnnuiteRepository $annuiteRepo, AnnuiteLocarnoRepository $locarnoRepo): Response
    {
        $entete = array('annuite' => 0, 'region' => 1, 'taxRegister' => 2, 'taxRenew' => 3, 'costViewRenew' => 4, 'costViewRegister' => 5);

        $path = __DIR__ . '/../../public/upload/';
        $file = $request->files->get('locarnoimport');
        $file->move($path, "tmp.csv");

        $row = 1;
        if (($handle = fopen($path . "tmp.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $data = $this->decrypteinutf8($data); // put in utf8;

                if ($row == 1) {
                        
                } else {
                    if ($data[$entete['annuite']] != ''
                        // $data[$entete['region']] != '' &&
                        // $data[$entete['taxRegister']] != '' &&
                        // $data[$entete['taxRenew']] != '' &&
                        // $data[$entete['costViewRenew']] != '' &&
                        // $data[$entete['costViewRegister']] != ''
                    ) {
                        $annuite = $annuiteRepo->findOneBy(['name' => $data[$entete['annuite']] ]);
                        if ($annuite) {
                            $annuiteLocarno = $locarnoRepo->findOneBy(['annuite' => $annuite ]);
                            if (!$annuiteLocarno)
                                $annuiteLocarno = new AnnuiteLocarno();

                            $annuiteLocarno->setAnnuite($annuite);
                            $annuiteLocarno->setRegion($annuite);
                            $annuiteLocarno->setTaxRegister ($data[$entete['taxRegister']]);
                            $annuiteLocarno->setTaxRenew ($data[$entete['taxRenew']]);
                            $annuiteLocarno->setCostViewRenew ($data[$entete['costViewRenew']]);
                            $annuiteLocarno->setCostViewRegister ($data[$entete['costViewRegister']]);

                            $em->persist($annuiteLocarno);
                            $em->flush();
                        }
                    } else
                        $this->addFlash('error', 'Erreur à la ligne : ' . $row);

                }
                $row++;
            }

            fclose($handle);

            //delete tmp file
            unlink($path . "tmp.csv");

            $this->addFlash('success', 'Importation réussie');

            return $this->redirectToRoute('list_locarno');
        } else {
            $this->addFlash('error', 'Errerur fichier non valid');
        }

        return $this->redirectToRoute('list_annuite');
    }

    #[Route('/del-annuite/{id}', name: 'delete_annuite', methods: ['POST'])]
    public function delete(Request $request, Annuite $annuite, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annuite->getId(), $request->request->get('_token'))) {
            $em->remove($annuite);
            $em->flush();

            $this->addFlash('success', 'Extension supprimé');
        }

        return $this->redirectToRoute('list_annuite', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/del-locarno/{id}', name: 'delete_locarno', methods: ['POST'])]
    public function deleteLocarno(Request $request, AnnuiteLocarno $locarno, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$locarno->getId(), $request->request->get('_token'))) {
            $em->remove($locarno);
            $em->flush();

            $this->addFlash('success', 'Extension supprimé');
        }

        return $this->redirectToRoute('list_locarno', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/del-nice/{id}', name: 'delete_nice', methods: ['POST'])]
    public function deleteNice(Request $request, AnnuiteNice $nice, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$nice->getId(), $request->request->get('_token'))) {
            $em->remove($nice);
            $em->flush();

            $this->addFlash('success', 'Extension supprimé');
        }

        return $this->redirectToRoute('list_nice', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/show-nice/{id}', name: 'show_nice', methods: ['GET'])]
    public function showNice(AnnuiteNice $nice): Response
    {
        return $this->render('billing/show_nice.html.twig', [
            'template_title' => 'Annuité Nice',
            'item' => $nice,
        ]);
    }

    #[Route('/export-nice', name: 'export_nice', methods: ['GET'])]
    public function exportNice(AnnuiteNiceRepository $niceRepo): Response
    {
        $items = $niceRepo->findAll();

        $handle = fopen('php://memory', 'r+');
        $titre = array('ID', 'Annuite', 'Region', 'TaxRegister', 'TaxRenew', 'CostViewRenew', 'CostViewRegister');

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
            'Content-Disposition' => 'attachment; filename="annuite-nice.csv"'
        ));
    }

    #[Route('/import-nice', name: 'import_nice', methods: ['POST'])]
    public function importNice(Request $request, EntityManagerInterface $em, AnnuiteRepository $annuiteRepo, AnnuiteNiceRepository $niceRepo): Response
    {
        $entete = array('annuite' => 0, 'region' => 1, 'taxRegister' => 2, 'taxRenew' => 3, 'costClassRenew' => 4, 'costClassRegister' => 5);

        $path = __DIR__ . '/../../public/upload/';
        $file = $request->files->get('niceimport');
        $file->move($path, "tmp.csv");

        $row = 1;
        
        if (($handle = fopen($path . "tmp.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $data = $this->decrypteinutf8($data); // put in utf8;

                if ($row == 1) {
                        
                } else {
                    if ($data[$entete['annuite']] != ''
                        // $data[$entete['region']] != '' &&
                        // $data[$entete['taxRegister']] != '' &&
                        // $data[$entete['taxRenew']] != '' &&
                        // $data[$entete['costViewRenew']] != '' &&
                        // $data[$entete['costViewRegister']] != ''
                    ) {
                        $annuite = $annuiteRepo->findOneBy(['name' => $data[$entete['annuite']] ]);
                        if ($annuite) {
                            $annuiteNice = $niceRepo->findOneBy(['annuite' => $annuite ]);
                            if (!$annuiteNice)
                                $annuiteNice = new AnnuiteNice();

                            $annuiteNice->setAnnuite($annuite);
                            $annuiteNice->setRegion($annuite);
                            $annuiteNice->setTaxRegister ($data[$entete['taxRegister']]);
                            $annuiteNice->setTaxRenew ($data[$entete['taxRenew']]);
                            $annuiteNice->setCostClassRenew ($data[$entete['costClassRenew']]);
                            $annuiteNice->setCostClassRegister ($data[$entete['costClassRegister']]);

                            $em->persist($annuiteNice);
                            $em->flush();
                        }
                    } else
                        $this->addFlash('error', 'Erreur à la ligne : ' . $row);

                }
                $row++;
            }

            fclose($handle);

            //delete tmp file
            unlink($path . "tmp.csv");

            $this->addFlash('success', 'Importation réussie');

            return $this->redirectToRoute('list_nice');
        } else {
            $this->addFlash('error', 'Errerur fichier non valid');
        }

        return $this->redirectToRoute('list_annuite');
    }

    #[Route('/list-annuite-nice', name: 'list_nice', methods: ['GET', 'POST'])]
    public function listNice(Request $request, AnnuiteNiceRepository $niceRepo): Response
    {
        $data = new AnnuiteSearchData();
        $form = $this->createForm(AnnuiteSearchFormType::class, $data);
        $form->handleRequest($request);

        $items = [];

        if ($form->isSubmitted() && $form->isValid())
        {
            $nom = $data->getNom();
            if ($nom == "" || $nom == null)
                $items = $niceRepo->findAll();
            else
                $items = $niceRepo->findSearch($data);
        }

        $items = $niceRepo->findAll();
        
        return $this->render('billing/index_annuite_nice.html.twig', [
            'template_title' => 'Liste des extensions',
            'form' => $form->createView(),
            'items' => $items,
            'active_ann' => false,
            'active_loc' => false,
            'active_nic' => true,
        ]);
    }

    #[Route('/add-locarno', name: 'add_locarno', methods: ['GET', 'POST'])]
    public function addLocarno(Request $request, EntityManagerInterface $em): Response
    {
        $listRoute = $this->urlGenerator->generate('list_annuite');
        $locarno = new AnnuiteLocarno();

        $form = $this->createForm(AnnuiteLocarnoFormType::class, $locarno);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($locarno);
            $em->flush();

            $this->addFlash('success', 'Nouvelle annuite enregistrée');

            return $this->redirectToRoute('list_locarno', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('billing/add_locarno.html.twig', [
            'template_title' => 'Edition Annuite Locarno',
            'list_route' => $listRoute,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show-locarno/{id}', name: 'show_locarno', methods: ['GET'])]
    public function showLocarno(AnnuiteLocarno $locarno): Response
    {
        return $this->render('billing/show_locarno.html.twig', [
            'template_title' => 'Annuité Locarno',
            'item' => $locarno,
        ]);
    }

    #[Route('/edit-locarno/{id}', name: 'edit_locarno', methods: ['GET', 'POST'])]
    public function editLocarno(Request $request, AnnuiteLocarno $locarno, EntityManagerInterface $em): Response
    {
        $routeBack = $this->urlGenerator->generate('show_locarno', array("id" => $locarno->getId()));

        $form = $this->createForm(AnnuiteLocarnoFormType::class, $locarno);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Extension bien modifié');

            return $this->redirectToRoute('show_locarno', [
                'id' => $locarno->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('billing/edit_locarno.html.twig', [
            'template_title' => 'Modification Annuite Locarno',
            'route_back' => $routeBack,
            'form' => $form,
        ]);
    }

    #[Route('/list-annuite-locarno', name: 'list_locarno', methods: ['GET', 'POST'])]
    public function listLocarno(Request $request, AnnuiteLocarnoRepository $locarnoRepo): Response
    {
        $data = new AnnuiteSearchData();
        $form = $this->createForm(AnnuiteSearchFormType::class, $data);
        $form->handleRequest($request);

        $items = [];

        if ($form->isSubmitted() && $form->isValid())
        {
            $nom = $data->getNom();
            if ($nom == "" || $nom == null)
                $items = $locarnoRepo->findAll();
            else
                $items = $locarnoRepo->findSearch($data);
        }

        $items = $locarnoRepo->findAll();
        
        return $this->render('billing/index_annuite_locarno.html.twig', [
            'template_title' => 'Liste des extensions',
            'form' => $form->createView(),
            'items' => $items,
            'active_ann' => false,
            'active_loc' => true,
            'active_nic' => false,
        ]);
    }

    #[Route('/export-locarno', name: 'export_locarno', methods: ['GET'])]
    public function exportLocarno(AnnuiteLocarnoRepository $locarnoRepo): Response
    {
        $items = $locarnoRepo->findAll();

        $handle = fopen('php://memory', 'r+');
        $titre = array('ID', 'Annuite', 'Region', 'TaxRegister', 'TaxRenew', 'CostViewRenew', 'CostViewRegister');

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
            'Content-Disposition' => 'attachment; filename="annuite-locarno.csv"'
        ));
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

    #[Route('/send-annuite-locarno', name: 'send_annuite_locarno', methods: ['GET'])]
    public function sendLocarno(AnnuiteLocarnoRepository $locarnoRepo)
    {
        $items = $locarnoRepo->findAll();

        foreach ($items as $item) {
            $data[] = $item->getExport();
        }

        return $this->json([
            $data,
        ], 200, []);
    }

    #[Route('/send-annuite-nice', name: 'send_annuite_nice', methods: ['GET'])]
    public function sendNice(AnnuiteNiceRepository $niceRepo)
    {
        $items = $niceRepo->findAll();

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
            'template_title' => 'Liste des extensions',
            'meth_name' => 'list',
            'form' => $form->createView(),
            'filter_form' => $filter_form->createView(),
            'items' => $items,
            'active_ann' => true,
            'active_loc' => false,
            'active_nic' => false,
        ]);
    }

    #[Route('/export', name: 'export_annuite', methods: ['GET'])]
    public function export(AnnuiteRepository $annuiteRepo): Response
    {
        $items = $annuiteRepo->findAll();

        $handle = fopen('php://memory', 'r+');
        $titre = array('ID', 'Nom', 'Pays', 'Periode', 'Montant', 'Region');

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

    #[Route('/show-annuite/{id}', name: 'show_annuite', methods: ['GET'])]
    public function show(Annuite $annuite): Response
    {
        return $this->render('billing/show.html.twig', [
            'template_title' => 'Annuité',
            'item' => $annuite,
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
