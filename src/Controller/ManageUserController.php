<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/user')]
class ManageUserController extends AbstractController
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    #[Route('/', name: 'index_user', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('manage_user/index.html.twig', [
            'template_title' => 'Gestion Utilisateur',
            'meth_name' => 'index',
        ]);
    }

    #[Route('/list', name: 'list_users', methods: ['GET'])]
    public function list(UserRepository $userRepository): Response
    {
        return $this->render('manage_user/list.html.twig', [
            'template_title' => 'Liste Utilisateurs',
            'meth_name' => 'list',
            'items' => $userRepository->findAll(),
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

            return $this->redirectToRoute('list_users', [], Response::HTTP_SEE_OTHER);
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
