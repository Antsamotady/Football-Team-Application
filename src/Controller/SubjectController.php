<?php

namespace App\Controller;

use App\Entity\Subject;
use App\Form\Subject1Type;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/subject')]
class SubjectController extends AbstractController
{
    #[Route('/', name: 'subject_index', methods: ['GET'])]
    public function index(SubjectRepository $subjectRepository): Response
    {
        return $this->render('subject/index.html.twig', [
            'subjects' => $subjectRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'subject_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $subject = new Subject();
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
    public function show(Subject $subject): Response
    {
        return $this->render('subject/show.html.twig', [
            'subject' => $subject,
        ]);
    }

    #[Route('/{id}/edit', name: 'subject_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Subject $subject, EntityManagerInterface $entityManager): Response
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

    #[Route('/ajax/update-score', name: 'score_update', methods: ['POST'])]
    public function updateScore(Request $request, ValidatorInterface $validator,  EntityManagerInterface $em, SubjectRepository $subjectRepo): JsonResponse
    {
        $content = $request->getContent();
        $data = json_decode($content, true);

        $subjectId = $data['subjectId'];
        $newScore = (float) $data['newScore'];

        $subject = $subjectRepo->find($subjectId);
        $subject->setScore($newScore);

        $errors = $validator->validate($subject);

        // if (count($errors) > 0) {
        //     dd($errors);
        //     return new JsonResponse(['errors' => $errors], 400);
        // }

        $em->persist($subject);
        $em->flush();

        return new JsonResponse(['message' => 'Score updated successfully']);
    }

    #[Route('/{id}', name: 'subject_delete', methods: ['POST'])]
    public function delete(Request $request, Subject $subject, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subject->getId(), $request->request->get('_token'))) {
            $entityManager->remove($subject);
            $entityManager->flush();
        }

        return $this->redirectToRoute('subject_index', [], Response::HTTP_SEE_OTHER);
    }
}
