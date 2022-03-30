<?php

namespace App\Controller;

use App\Data\ExtensionsFilterData;
use App\Data\ExtensionsSearchData;
use App\Entity\Extensions;
use App\Form\ExtensionsFilterFormType;
use App\Form\ExtensionsSearchFormType;
use App\Repository\ExtensionsRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/import', name: 'import_extensions', methods: ['POST'])]
    public function import(Request $request, EntityManagerInterface $em, ExtensionsRepository $extensionsRepos): Response
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
                    $ext = new Extensions();
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

            return $this->redirectToRoute('list_extensions');
        } else {
            $this->addFlash('error', 'Errerur fichier non valid');
        }

        return $this->redirectToRoute('list_extensions');
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
