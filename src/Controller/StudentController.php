<?php

namespace App\Controller;

use App\Data\StudentFilterData;
use App\Data\StudentSearchData;
use App\Entity\Student;
use App\Form\StudentFilterFormType;
use App\Form\StudentSearchFormType;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/student')]
class StudentController extends AbstractController
{
    #[Route('/', name: 'student_index', methods: ['GET'])]
    public function index(StudentRepository $studentRepository): Response
    {
        return $this->render('student/index.html.twig', [
            'students' => $studentRepository->findAll(),
        ]);
    }

    #[Route('/list', name: 'student_list', methods: ['GET'])]
    public function list(Request $request, StudentRepository $studentRepository): Response
    {
        $data = new StudentSearchData();
        $form = $this->createForm(StudentSearchFormType::class, $data);
        $form->handleRequest($request);

        $filteredData = new StudentFilterData();
        $filterForm = $this->createForm(StudentFilterFormType::class, $filteredData);
        $filterForm->handleRequest($request);

        $students = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $data->getName();

            if (!isset($name)) $students = $studentRepository->findAll();
            else $students = $studentRepository->findSearch($data);

        } elseif ($filterForm->isSubmitted() && $filterForm->isValid()) 
            $students = $studentRepository->findFiltered($filteredData);
        else
            $students = $studentRepository->findAll();

        return $this->render('student/list.html.twig', [
            'template_title' => 'Liste des étudiants',
            'meth_name' => 'list',
            'form' => $form->createView(),
            'filter_form' => $filterForm->createView(),
            'students' => $students,
        ]);
    }

    #[Route('/new', name: 'student_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($student);
            $entityManager->flush();

            return $this->redirectToRoute('student_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('student/new.html.twig', [
            'template_title' => 'Ajouter',
            'student' => $student,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'student_show', methods: ['GET'])]
    public function show(Student $student): Response
    {
        return $this->render('student/show.html.twig', [
            'template_title' => 'Détails étudiant',
            'student' => $student,
        ]);
    }

    #[Route('/{id}/edit', name: 'student_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Student $student, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('student_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('student/edit.html.twig', [
            'template_title' => 'Modifier un étudiant',
            'student' => $student,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'student_delete', methods: ['POST'])]
    public function delete(Request $request, Student $student, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$student->getId(), $request->request->get('_token'))) {
            $entityManager->remove($student);
            $entityManager->flush();
        }

        return $this->redirectToRoute('student_index', [], Response::HTTP_SEE_OTHER);
    }
}
