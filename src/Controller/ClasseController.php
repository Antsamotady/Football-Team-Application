<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Entity\Location;
use App\Form\ClasseType;
use App\Service\ScoreService;
use App\Data\ClasseFilterData;
use App\Data\GeneralSearchData;
use App\Data\StudentFilterData;
use App\Service\StudentExporter;
use App\Form\ClasseFilterFormType;
use App\Form\ClasseSearchFormType;
use App\Form\StudentFilterFormType;
use App\Repository\ScoreRepository;
use App\Service\StudentCsvImporter;
use App\Repository\ClasseRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/classe')]
class ClasseController extends AbstractController
{
	public function __construct(
        private ClasseRepository $classeRepository,
        private StudentRepository $studentRepo,
		private ScoreRepository $scoreRepo,
		private ScoreService $scoreService,
		public EntityManagerInterface $em
	) {
	}
    
    #[Route('/', name: 'classe_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $data = new GeneralSearchData();
        $form = $this->createForm(ClasseSearchFormType::class, $data);
        $form->handleRequest($request);

        $filteredData = new ClasseFilterData();
        $filterForm = $this->createForm(ClasseFilterFormType::class, $filteredData);
        $filterForm->handleRequest($request);

        $classes = [];

		if ($form->isSubmitted() && $form->isValid()) {
			$classes = $this->classeRepository->findSearch($data);
			$session->set('classe_search_criteria', [
					'type' => 'search',
					'data' => $data
			]);
		} elseif ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $classes = $this->classeRepository->findFiltered($filteredData);
            $session->set('classe_search_criteria', [
                'type'  => 'filter',
                'data'  => $filteredData
            ]);
        } else {
			$classes = $this->classeRepository->findAll();
			$session->remove('classe_search_criteria');
		}

        return $this->render('classe/index.html.twig', [
            'template_title'    => 'Classes',
            'classes'           => $classes,
			'form'              => $form->createView(),
			'filter_form' 	    => $filterForm->createView(),
			'total'             => count($this->classeRepository->findAll())
        ]);
    }

    #[Route('/new', name: 'classe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $classe = new Classe();
        $form = $this->createForm(ClasseType::class, $classe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $classe->setUpdatedAt(new \DateTimeImmutable());
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

    #[Route('/new/location/{id}', name: 'classe_new_from_location', methods: ['GET', 'POST'])]
    public function newFromLocation(
        Location $location,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $classe = new Classe();
        $classe->setLocation($location);

        $form = $this->createForm(ClasseType::class, $classe, [
            'location_locked' => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($classe);
            $entityManager->flush();

            $this->addFlash('success', 'Nouvelle classe enregistrée.');

            return $this->redirectToRoute('location_show', [
                'id' => $location->getId(),
            ]);
        }

        return $this->render('classe/new.html.twig', [
            'classe' => $classe,
            'form' => $form,
            'location' => $location,
        ]);
    }

    #[Route('/{id}', name: 'classe_show', methods: ['GET'])]
    public function show(
        Request $request,
        Classe $classe): Response
    {
        $session = $request->getSession();
		
        $filteredData = new StudentFilterData();
        $filterForm = $this->createForm(StudentFilterFormType::class, $filteredData, [
            'classe_locked' => true,
        ]);
        $filterForm->handleRequest($request);

		$students = [];

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $students = $this->studentRepo->findFilteredByClasse($classe, $filteredData);

            $session->set('classe_search_criteria', [
                'type'  => 'filter',
                'data'  => $filteredData
            ]);
        } else {
            $students = $this->studentRepo->findBy(['classe' => $classe]);
        }
        
        $studentsScores = []; 

		foreach ($students as $student) { 
			$scores = $this->scoreRepo->findBy(['student' => $student], ['subject' => 'ASC']); 
			$scoreResults = $this->scoreService->processScores($scores); 

			$studentsScores[$student->getId()] = [
				'student' => $student, 
				'best_score' => $scoreResults['bestScore'], 
				'total_score' => $scoreResults['totalScore'], 
				'average_score'=> count($scores) ? $scoreResults['totalScore'] / count($scores) : 0,    // @phpstan-ignore-line
				'score_forms' => $scoreResults['formViews'] 
			];
		}

        return $this->render('classe/show.html.twig', [
            'classe'            => $classe,
            'students'          => $students,
			'students_scores'   => $studentsScores,
			'filter_form' 	    => $filterForm->createView(),
			'has_filters'       => $filterForm->isSubmitted()
        ]);
    }

    #[Route('/{id}/edit', name: 'classe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Classe $classe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClasseType::class, $classe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $classe->setUpdatedAt(new \DateTimeImmutable());
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
        $tokenRaw = $request->request->get('_token');
        $token = $tokenRaw !== null ? (string) $tokenRaw : null;

        if ($this->isCsrfTokenValid('delete'.$classe->getId(), $token)) {
            $entityManager->remove($classe);
            $entityManager->flush();

            $this->addFlash('success', 'Supression réussie.');
        }

        return $this->redirectToRoute('classe_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/students/export', name: 'classe_student_export', methods: ['GET'])]
    public function exportClasseStudents(
        Classe $classe,
        Request $request,
        StudentRepository $studentRepo,
        StudentExporter $studentExporter
    ): Response {
        $session = $request->getSession();
        $criteria = $session->get('classe_search_criteria');
        $students = [];

        if (
            is_array($criteria) 
            && isset($criteria['data']) 
            && $criteria['data'] instanceof StudentFilterData
        ) {
            $filterData = $criteria['data'];
            $filterData->setClasse($classe);
            $students = $studentRepo->findFiltered($filterData);
        }

        if (empty($students)) {
            $filterData = new StudentFilterData();
            $filterData->setClasse($classe);
            $students = $studentRepo->findFiltered($filterData);
        }

        $fileName = $studentExporter->generateFilename($filterData ?? null, $classe->getName());
        $csvContent = $studentExporter->exportStudents($students);

        return new Response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
        ]);
    }

    #[Route('/{id}/students/import', name: 'classe_student_import', methods: ['POST'])]
    public function importClasseStudents(
        Classe $classe,
        Request $request,
        StudentCsvImporter $importer
    ): Response {
        $file = $request->files->get('student-import');

        if (!$file instanceof UploadedFile) {
            $this->addFlash('error', 'Fichier non trouvé ou invalide.');
            return $this->redirectToRoute('classe_show', ['id' => $classe->getId()]);
        }

        try {
            $importer->import($file, $classe);
            $this->addFlash('success', 'Les étudiants ont bien été importés.');
        } catch (\Throwable $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('classe_show', [
            'id' => $classe->getId(),
        ]);
    }
}
