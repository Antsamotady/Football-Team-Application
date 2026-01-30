<?php

namespace App\Controller;

use App\Repository\ClasseRepository;
use App\Repository\TeacherRepository;
use App\Repository\StudentRepository;
use App\Repository\SubjectRepository;
use App\Repository\LocationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        LocationRepository $locationRepository,
        ClasseRepository $classeRepository,
        TeacherRepository $teacherRepository,
        StudentRepository $studentRepository,
        SubjectRepository $subjectRepository
    ): Response {
        return $this->render('home/index.html.twig', [
            'nb_locations' => $locationRepository->count([]),
            'nb_classes'   => $classeRepository->count([]),
            'nb_teachers'  => $teacherRepository->count([]),
            'nb_students'  => $studentRepository->count([]),
            'nb_subjects'  => $subjectRepository->count([])
        ]);
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
