<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManageUserController extends AbstractController
{
    #[Route('/manage-user', name: 'manage_user')]
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

    #[Route('/up-user', name: 'up_user')]
    public function up(): Response
    {
        return $this->render('manage_user/up.html.twig', [
            'template_title' => 'Nouvel Utilisateur',
            'meth_name' => 'up',
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
