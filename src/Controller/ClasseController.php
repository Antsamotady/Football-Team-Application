<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Form\ClasseType;
use App\Service\ScoreService;
use App\Repository\ScoreRepository;
use App\Repository\ClasseRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/classe')]
class ClasseController extends AbstractController
{
	public function __construct(
		public EntityManagerInterface $em,
		private ScoreService $scoreService
	) {
	}
    
    #[Route('/', name: 'classe_index', methods: ['GET'])]
    public function index(ClasseRepository $classeRepository): Response
    {
        return $this->render('classe/index.html.twig', [
            'template_title' => 'Classes existantes',
            'classes' => $classeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'classe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $classe = new Classe();
        $form = $this->createForm(ClasseType::class, $classe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($classe);
            $entityManager->flush();


            $this->addFlash('success', 'Nouvelle classe enregistrée.');
            return $this->redirectToRoute('classe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('classe/new.html.twig', [
            'classe' => $classe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'classe_show', methods: ['GET'])]
    public function show(
        Classe $classe, 
        StudentRepository $studentRepo, 
		ScoreRepository $scoreRepo): Response
    {
        $students = $studentRepo->findBy(['classe' => $classe]);
		$studentsScores = []; 

		foreach ($students as $student) { 
			$scores = $scoreRepo->findBy(['student' => $student], ['subject' => 'ASC']); 
			$scoreResults = $this->scoreService->processScores($scores); 

			$studentsScores[$student->getId()] = [
				'student' => $student, 
				'best_score' => $scoreResults['bestScore'], 
				'total_score' => $scoreResults['totalScore'], 
				'average_score'=> count($scores) ? $scoreResults['totalScore'] / count($scores) : 0, 
				'score_forms' => $scoreResults['formViews'] 
			]; 
		}

        return $this->render('classe/show.html.twig', [
            'classe'    => $classe,
            'students'  => $students,
			'students_scores' => $studentsScores,
        ]);
    }

    #[Route('/{id}/edit', name: 'classe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Classe $classe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClasseType::class, $classe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Modification réussie.');
            
            return $this->redirectToRoute('classe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('classe/edit.html.twig', [
            'classe' => $classe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'classe_delete', methods: ['POST'])]
    public function delete(Request $request, Classe $classe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$classe->getId(), $request->request->get('_token'))) {
            $entityManager->remove($classe);
            $entityManager->flush();

            $this->addFlash('success', 'Supression réussie.');
        }

        return $this->redirectToRoute('classe_index', [], Response::HTTP_SEE_OTHER);
    }
}
