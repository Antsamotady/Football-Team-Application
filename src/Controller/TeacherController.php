<?php

namespace App\Controller;

use App\Entity\Teacher;
use App\Form\TeacherType;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/teacher')]
class TeacherController extends AbstractController
{
    public function __construct(
        private TeacherRepository $teacherRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/', name: 'teacher_index')]
    public function index(): Response
    {
        return $this->render('teacher/index.html.twig', [
            'teachers' => $this->teacherRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'teacher_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $teacher = new Teacher();
        $form = $this->createForm(TeacherType::class, $teacher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($teacher);
            $this->entityManager->flush();

            return $this->redirectToRoute('teacher_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('teacher/new.html.twig', [
            'teacher' => $teacher,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'teacher_show', methods: ['GET'])]
    public function show(Teacher $teacher): Response
    {
        return $this->render('teacher/show.html.twig', [
            'teacher' => $teacher,
        ]);
    }

    #[Route('/{id}/edit', name: 'teacher_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Teacher $teacher): Response
    {
        $form = $this->createForm(TeacherType::class, $teacher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('teacher_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('teacher/edit.html.twig', [
            'teacher' => $teacher,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'teacher_delete', methods: ['POST'])]
    public function delete(Request $request, Teacher $teacher): Response
    {
        $tokenRaw = $request->request->get('_token');
        $token = $tokenRaw !== null ? (string) $tokenRaw : null;

        if ($this->isCsrfTokenValid('delete'.$teacher->getId(), $token)) {
            $this->entityManager->remove($teacher);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('teacher_index', [], Response::HTTP_SEE_OTHER);
    }

}
