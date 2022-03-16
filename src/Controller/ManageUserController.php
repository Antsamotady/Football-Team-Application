<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ManageUserController extends AbstractController
{
    #[Route('/up-user', name: 'up_user')]
    public function index(): Response
    {
        return $this->render('manage_user/index.html.twig', [
            'template_title' => 'Gestion Utilisateur',
            'meth_name' => 'index',
        ]);
    }

    #[Route('/list-users', name: 'list_users')]
    public function list(): Response
    {
        return $this->render('manage_user/list.html.twig', [
            'template_title' => 'Liste Utilisateurs',
            'meth_name' => 'list',
        ]);
    }

    #[Route('/view-user', name: 'view_user')]
    public function view(): Response
    {
        return $this->render('manage_user/view.html.twig', [
            'template_title' => 'Compte Utilisateur',
            'meth_name' => 'view',
        ]);
    }

    #[Route('/add-user', name: 'add_user')]
    public function add(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
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

            return $this->redirectToRoute('home');
        }

        return $this->render('manage_user/add.html.twig', [
            'template_title' => 'Nouvel Utilisateur',
            'meth_name' => 'up',
            'registrationForm' => $form->createView(),
        ]);
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
