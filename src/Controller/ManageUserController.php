<?php

namespace App\Controller;

use App\Entity\User;
use App\Data\UserFilterData;
use App\Data\UserSearchData;
use App\Form\UserFilterFormType;
use App\Form\UserSearchFormType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class ManageUserController extends AbstractController
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    #[Route('/export', name: 'export_user', methods: ['GET'])]
    public function export(UserRepository $userRepository): Response
    {
        $items = $userRepository->findAll();

        $handle = fopen('php://memory', 'r+');
        $titre = array('Nom', 'Email');

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
            'Content-Disposition' => 'attachment; filename="utilisateurs.csv"'
        ));
    }

    #[Route('/search', name: 'search_user', methods: ['GET', 'POST'])]
    public function searchUser(Request $request, UserRepository $userRepository): Response
    {
        $data = new UserSearchData();
        $form = $this->createForm(UserSearchFormType::class, $data);

        $form->handleRequest($request);

        $items = [];

        if ($form->isSubmitted() && $form->isValid())
        {
            $nom = $data->getName();
            if ($nom == "")
                $items = $userRepository->findAll();
            else
                $items = $userRepository->findSearch($data);
        }

        return $this->render('manage_user/search.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/', name: 'index_user', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('manage_user/index.html.twig', [
            'template_title' => 'Gestion Utilisateur',
            'meth_name' => 'index',
        ]);
    }

    #[Route('/filter', name: 'filter_users', methods: ['GET'])]
    public function filter(Request $request, UserRepository $userRepository): Response
    {
        $data = new UserFilterData();
        $form = $this->createForm(UserFilterFormType::class, $data);

        $form->handleRequest($request);

        $items = [];

        if ($form->isSubmitted() && $form->isValid())
        {
            $nom = $data->getName();
            if ($nom == "" || $nom == null)
                $items = $userRepository->findAll();
            else
                $items = $userRepository->findFiltered($data);
        } else
            $items = $userRepository->findAll();


        return $this->render('manage_user/filter.html.twig', [
            'filter_form' => $form->createView(),
            'filtered_items' => $items,
        ]);
    }

    #[Route('/list', name: 'list_users', methods: ['GET', 'POST'])]
    public function list(Request $request, UserRepository $userRepository): Response
    {
        $data = new UserSearchData();
        $form = $this->createForm(UserSearchFormType::class, $data);
        $form->handleRequest($request);

        $filtered_data = new UserFilterData();
        $filter_form = $this->createForm(UserFilterFormType::class, $filtered_data);
        $filter_form->handleRequest($request);

        $items = [];

        if ($form->isSubmitted() && $form->isValid())
        {
            $nom = $data->getNom();
            if ($nom == "" || $nom == null)
                $items = $userRepository->findAll();
            else
                $items = $userRepository->findSearch($data);

        } elseif ($filter_form->isSubmitted() && $filter_form->isValid()) {
            $nom = $filtered_data->getName();
            $email = $filtered_data->getEmail();
            if ( ($nom == "" || $nom == null) && ($email == "" || $email == null) )
                $items = $userRepository->findAll();
            else
                $items = $userRepository->findFiltered($filtered_data);
        } else
            $items = $userRepository->findAll();


        return $this->render('manage_user/list.html.twig', [
            'template_title' => 'Liste Utilisateurs',
            'meth_name' => 'list',
            'form' => $form->createView(),
            'filter_form' => $filter_form->createView(),
            'items' => $items,
        ]);
    }

    #[Route('/add', name: 'add_user', methods: ['GET', 'POST'])]
    public function add(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $listRoute = $this->urlGenerator->generate('list_users');

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            $this->addFlash('success', 'Utilisateur bien enregistrer');

            return $this->redirectToRoute('list_users');
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Email déjà existant');
        }

        return $this->render('manage_user/add.html.twig', [
            'template_title' => 'Nouvel Utilisateur',
            'meth_name' => 'up',
            'list_route' => $listRoute,
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show_user', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('manage_user/show.html.twig', [
            'template_title' => 'Compte Utilisateur',
            'meth_name' => 'view',
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit_user', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $routeBack = $this->urlGenerator->generate('show_user', array("id" => $user->getId()));

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur bien modifié');

            return $this->redirectToRoute('show_user', [
                'id' => $user->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('manage_user/edit.html.twig', [
            'template_title' => 'Modification utilisateur',
            'user' => $user,
            'route_back' => $routeBack,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete_user', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur supprimé');
        }

        return $this->redirectToRoute('list_users', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/user-logs', name: 'log')]
    public function log(): Response
    {
        return $this->render('manage_user/log.html.twig', [
            'template_title' => 'Journal',
            'meth_name' => 'journal',
        ]);
    }
}
