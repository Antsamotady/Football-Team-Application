<?php

namespace App\Controller;

use App\Entity\StudentSubject;
use App\Form\Subject1Type;
use App\Repository\StudentSubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/subject')]
class SubjectController extends AbstractController
{
    #[Route('/', name: 'subject_index', methods: ['GET'])]
    public function index(StudentSubjectRepository $subjectRepository): Response
    {
        return $this->render('subject/index.html.twig', [
            'subjects' => $subjectRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'subject_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $subject = new StudentSubject();
        $form = $this->createForm(Subject1Type::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($subject);
            $entityManager->flush();

            return $this->redirectToRoute('subject_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('subject/new.html.twig', [
            'subject' => $subject,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'subject_show', methods: ['GET'])]
    public function show(StudentSubject $subject): Response
    {
        return $this->render('subject/show.html.twig', [
            'subject' => $subject,
        ]);
    }

    #[Route('/{id}/edit', name: 'subject_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, StudentSubject $subject, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Subject1Type::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('subject_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('subject/edit.html.twig', [
            'subject' => $subject,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'subject_delete', methods: ['POST'])]
    public function delete(Request $request, StudentSubject $subject, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subject->getId(), $request->request->get('_token'))) {
            $entityManager->remove($subject);
            $entityManager->flush();
        }

        return $this->redirectToRoute('subject_index', [], Response::HTTP_SEE_OTHER);
    }
}
