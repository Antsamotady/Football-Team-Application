<?php

namespace App\Controller;

use App\Entity\Score;
use App\Entity\Classe;
use App\Entity\Student;
use App\Entity\Subject;
use App\Form\StudentType;
use App\Event\StudentEvent;
use App\Service\ScoreService;
use App\Data\StudentFilterData;
use App\Data\StudentSearchData;
use App\Service\StudentExporter;
use App\Form\StudentFilterFormType;
use App\Form\StudentSearchFormType;
use App\Repository\ScoreRepository;
use App\Service\StudentCsvImporter;
use App\Repository\StudentRepository;
use App\Service\BreadcrumbService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/student')]
class StudentController extends AbstractController
{
	public function __construct(
		public EntityManagerInterface $em,
		private ScoreService $scoreService,
        private BreadcrumbService $breadcrumbs,
        private EventDispatcherInterface $eventDispatcher
	) {
	}
	
	#[Route('/', name: 'student_index', methods: ['GET'])]
	public function index(StudentRepository $studentRepo): Response
	{
		return $this->render('student/index.html.twig', [
				'students' => $studentRepo->findAll(),
		]);
	}

	#[Route('/list', name: 'student_list', methods: ['GET'])]
	public function list(
		Request $request, 
		StudentRepository $studentRepo, 
    ): Response
	{
        $session = $request->getSession();
		$data = new StudentSearchData();
		$form = $this->createForm(StudentSearchFormType::class, $data);
		$form->handleRequest($request);

		$filteredData = new StudentFilterData();
		$filterForm = $this->createForm(StudentFilterFormType::class, $filteredData);
		$filterForm->handleRequest($request);

		$students = [];

		if ($form->isSubmitted() && $form->isValid()) {
			$students = $studentRepo->findSearch($data);
			// Store search criteria in session
			$session->set('student_search_criteria', [
					'type' => 'search',
					'data' => $data
			]);
		} elseif ($filterForm->isSubmitted() && $filterForm->isValid()) {
			$students = $studentRepo->findFiltered($filteredData);
			// Store filter criteria in session
			$session->set('student_search_criteria', [
					'type' => 'filter',
					'data' => $filteredData
			]);
		} else {
			$students = $studentRepo->findAllOrderedByFirstName();
			// Clear stored criteria
			$session->remove('student_search_criteria');
		}

		$result = count($students);
		$studentsScores = $this->scoreService->buildStudentsScores($students);


        $this->breadcrumbs
            ->add('Accueil', $this->generateUrl('home'))
            ->add('Etudiants');

		return $this->render('student/list.html.twig', [
			'template_title'    => 'Liste des étudiants',
			'meth_name' 		=> 'list',
            'result'			=> $result,
			'students' 			=> $students,
			'students_scores'   => $studentsScores,
            'form' 				=> $form->createView(),
			'filter_form' 	    => $filterForm->createView(),
			'has_filters'       => $form->isSubmitted() || $filterForm->isSubmitted(),
		]);
	}

    #[Route('/export', name: 'student_export', methods: ['GET'])]
    public function exportStudentCsv(
        Request $request,
        StudentRepository $studentRepo,
        StudentExporter $studentExporter
    ): Response {
        $session = $request->getSession();
        $criteria = $session->get('student_search_criteria');

        $students = [];
        $exportCriteria = null;

        if (
            is_array($criteria)
            && array_key_exists('type', $criteria)
            && array_key_exists('data', $criteria)
        ) {
            if ($criteria['type'] === 'search' && $criteria['data'] instanceof StudentSearchData) {
                $students = $studentRepo->findSearch($criteria['data']);
                $exportCriteria = $criteria['data'];
            } elseif ($criteria['data'] instanceof StudentFilterData) {
                $students = $studentRepo->findFiltered($criteria['data']);
                $exportCriteria = $criteria['data'];
            }
        }

        if ($students === []) {
            $students = $studentRepo->findAll();
        }

        $fileName = $studentExporter->generateFilename($exportCriteria);
        $csvContent = $studentExporter->exportStudents($students);

        return new Response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => sprintf(
                'attachment; filename="%s"',
                $fileName
            ),
        ]);
    }

    #[Route('/import', name: 'student_import', methods: ['POST'])]
    public function import(
        Request $request,
        StudentCsvImporter $importer
    ): Response {
        $file = $request->files->get('student-import');

        if (!$file instanceof UploadedFile) {
            $this->addFlash('error', 'Fichier non trouvé ou invalide.');
            return $this->redirectToRoute('student_list');
        }

        try {
            $importer->import($file);
            $this->addFlash('success', 'Les étudiants ont bien été importés.');
        } catch (\Throwable $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('student_list');
    }

	#[Route('/new', name: 'student_new', methods: ['GET', 'POST'])]
	public function new(Request $request, EntityManagerInterface $em): Response
	{
		$student = new Student();
		$form = $this->createForm(StudentType::class, $student);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
            $student->setUpdatedAt(new \DateTimeImmutable());

			$em->persist($student);
			$em->flush();

            $event = new StudentEvent($student, StudentEvent::CREATED);
            $this->eventDispatcher->dispatch($event, StudentEvent::CREATED);

			$this->addFlash('success', 'Ajout étudiant réussi.');
			return $this->redirectToRoute('student_list', [], Response::HTTP_SEE_OTHER);
		}

        $this->breadcrumbs
            ->add('Accueil', $this->generateUrl('home'))
            ->add('Etudiants', $this->generateUrl('student_list'))
            ->add('Etudiant #' . $student->getId());

		return $this->render('student/new.html.twig', [
			'template_title' => 'Ajouter',
			'student' => $student,
			'form' => $form,
		]);
	}

    #[Route('/new/classe/{id}', name: 'student_new_from_classe', methods: ['GET', 'POST'])]
    public function newFromClasse(
        Classe $classe,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $student = new Student();
        $student->setClasse($classe);

        $form = $this->createForm(StudentType::class, $student, [
            'classe_locked' => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($student);
            $entityManager->flush();

            $this->addFlash('success', 'Nouvel étudiant enregistré');

            return $this->redirectToRoute('classe_show', [
                'id' => $classe->getId(),
            ]);
        }

        $this->breadcrumbs
            ->add('Accueil', $this->generateUrl('home'))
            ->add('Toutes les classes', $this->generateUrl('classe_index'))
            ->add('Classe ' . $classe->getName(), $this->generateUrl('classe_show', ['id' => $classe->getId()]))
            ->add('Ajout étudiant');

        return $this->render('classe/new.html.twig', [
            'classe'        => $classe,
            'form'          => $form,
            'student'       => $student,
            'is_from_classe' => true
        ]);
    }

	#[Route('/{id}', name: 'student_show', methods: ['GET'])]
	public function show(
		Student $student, 
		StudentRepository $studentRepo, 
		ScoreRepository $scoreRepo): Response
	{
		$previousStudent = null;
		$nextStudent = null;
		$firstStudent = $studentRepo->findOneBy([], ['id' => 'ASC']);
		$lastStudent = $studentRepo->findOneBy([], ['id' => 'DESC']);

		if ($student != $firstStudent)
			$previousStudent = $studentRepo->findOneBy(['id' => $student->getId() - 1]);

		if ($student != $lastStudent)
			$nextStudent = $studentRepo->findOneBy(['id' => $student->getId() + 1]);

		$this->ensureStudentScoresComplete($student);

		$scores = $scoreRepo->findBy(
			['student' => $student], 
			['subject' => 'ASC']
		);

		$scoreResults = $this->scoreService->processScores($scores);

        $this->breadcrumbs
            ->add('Accueil', $this->generateUrl('home'))
            ->add('Etudiants', $this->generateUrl('student_list'))
            ->add('Etudiant #' . $student->getId());

		return $this->render('student/show.html.twig', [
			'template_title' 	=> 'Détails étudiant',
			'student' 			=> $student,
			'scores'			=> $scores,
			'previous' 			=> $previousStudent ? $previousStudent->getId() : null,
			'next' 				=> $nextStudent ? $nextStudent->getId() : null,
			'best_score' 		=> $scoreResults['bestScore'],
			'total_score' 		=> $scoreResults['totalScore'],
			'average_score' 	=> $scoreResults['averageScore'],
			'score_forms' 		=> $scoreResults['formViews']
		]);
	}

    #[Route('/ajax/update-score', name: 'score_update', methods: ['POST'])]
    public function updateScore(
        Request $request,
        EntityManagerInterface $em,
        ScoreRepository $scoreRepo
    ): JsonResponse 
    {
        $content = $request->getContent();

        /** @var array<string, mixed>|null $data */
        $data = json_decode($content, true);

        if (!is_array($data) || !isset($data['scoreId'], $data['newScore'])) {
            return new JsonResponse([
                'status' => 'KO',
                'message' => 'Invalid JSON payload',
            ]);
        }

        $scoreId = $data['scoreId'];  
        $newScore = $data['newScore'];

        /** @var Score|null $score */
        $score = $scoreRepo->find($scoreId);

        if (!$score) {
            return new JsonResponse([
                'status' => 'KO',
                'message' => 'Score not found',
                'input' => $newScore,
            ]);
        }

        $score->setValue($newScore);    // @phpstan-ignore-line
        $em->persist($score);

        try {
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'KO',
                'message' => $e->getMessage(),
                'input' => $newScore,
            ]);
        }

        return new JsonResponse(['status' => 'OK']);
    }
	
	#[Route('/{id}/edit', name: 'student_edit', methods: ['GET', 'POST'])]
	public function edit(
        Request $request, 
        Student $student, 
        StudentRepository $studentRepo, 
        ScoreRepository $scoreRepo): Response
	{
		$previousStudent = null;
		$nextStudent = null;
		$firstStudent = $studentRepo->findOneBy([], ['id' => 'ASC']);
		$lastStudent = $studentRepo->findOneBy([], ['id' => 'DESC']);

        /** @var array{
         *   firstName: string|null, 
         *   lastName: string|null, 
         *   numero: int|null} 
         * $originalData 
         */
        $originalData = [
            'firstName' => $student->getFirstName(),
            'lastName' => $student->getLastName(),
            'numero' => $student->getStudentNumber(),
            // Add other fields you want to track
        ];

		if ($student != $firstStudent)
			$previousStudent = $studentRepo->findOneBy(['id' => $student->getId() - 1]);

		if ($student != $lastStudent)
			$nextStudent = $studentRepo->findOneBy(['id' => $student->getId() + 1]);
		
        
		$this->ensureStudentScoresComplete($student);

        $includeScores = true;
        $form = $this->createForm(StudentType::class, $student, [
            'classe_locked' => false, // or true if editing from class context
            'include_scores' => $includeScores,
        ]);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $student->setUpdatedAt(new \DateTimeImmutable());

            $changes = $this->getChangesForLogEvent($student, $originalData);

            $event = new StudentEvent($student, StudentEvent::UPDATED, $changes);
            $this->eventDispatcher->dispatch($event, StudentEvent::UPDATED);


            // Remove empty scores (where value is null)
            foreach ($student->getScores() as $score) {
                if ($score->getValue() === null) {
                    $student->removeScore($score);
                    $this->em->remove($score);
                }
            }
            
            $this->em->persist($student);
            $this->em->flush();
            
            $this->addFlash('success', 'L\'étudiant a été mis à jour.');
            
            return $this->redirectToRoute('student_show', ['id' => $student->getId()], Response::HTTP_SEE_OTHER);
        }
        
		$scores = $scoreRepo->findBy(
			['student' => $student], 
			['subject' => 'ASC']
		);

		$scoreResults = $this->scoreService->processScores($scores);

        $this->breadcrumbs
            ->add('Accueil', $this->generateUrl('home'))
            ->add('Etudiants', $this->generateUrl('student_list'))
            ->add('Etudiant #' . $student->getId());

		return $this->render('student/edit.html.twig', [
			'template_title'    => 'Editer un étudiant',
			'student'           => $student,
			'previous'          => $previousStudent ? $previousStudent->getId() : null,
			'next'              => $nextStudent ? $nextStudent->getId() : null,
			'form'              => $form,
			'scores'            => $scores,
			'score_forms'       => $scoreResults['formViews'],
            'include_scores'    => $includeScores
		]);
	}

    #[Route('/{id}', name: 'student_delete', methods: ['POST'])]
    public function delete(Request $request, Student $student, EntityManagerInterface $em): Response
    {
        $tokenRaw = $request->request->get('_token');
        $token = $tokenRaw !== null ? (string) $tokenRaw : null;

        if ($this->isCsrfTokenValid('delete'.$student->getId(), $token)) {
            $event = new StudentEvent($student, StudentEvent::DELETED);
            $this->eventDispatcher->dispatch($event, StudentEvent::DELETED);
            
            $em->remove($student);
            $em->flush();

            $this->addFlash('success', 'Suppression réussie.');
        }

        return $this->redirectToRoute('student_list', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Convert all strings in CSV row to UTF-8
     *
     * @param list<string|null> $datas
     * @return list<string>
     */
    protected function decrypteinutf8(array $datas): array
    {
        $datas_return = [];
        foreach ($datas as $value) {
            $datas_return[] = (preg_match('!!u', (string) $value)) ? (string) $value : utf8_encode((string) $value);
        }
        return $datas_return;
    }

    private function ensureStudentScoresComplete(Student $student): void
    {
        $subjectRepository = $this->em->getRepository(Subject::class);
        $subjects = $subjectRepository->findAll();
            
        $scoreRepository = $this->em->getRepository(Score::class);
        $scores = $scoreRepository->findBy(['student' => $student]);

        $existingScores = [];
        foreach ($scores as $score) {
            $subject = $score->getSubject();
            if (!$subject) {
                throw new \LogicException('Score without subject detected');
            }
            $existingScores[$subject->getId()] = $score;
        }

        foreach ($subjects as $subject) {
                if (!isset($existingScores[$subject->getId()])) {
                    $newScore = new Score();
                    $newScore->setStudent($student);
                    $newScore->setSubject($subject);
                    $newScore->setValue(0);
                    $this->em->persist($newScore);
                }
        }
        $this->em->flush();
	}

    /**
     * @param array{
     *     firstName: string|null,
     *     lastName: string|null,
     *     numero: int|null
     * } $originalData
     *
     * @return array<string, array{old: string|int|null, new: string|int|null}>
     */
    private function getChangesForLogEvent(Student $student, array $originalData): array
    {
        $changes = [];
        if ($originalData['firstName'] !== $student->getFirstName()) {
            $changes['firstName'] = [
                'old' => $originalData['firstName'],
                'new' => $student->getFirstName()
            ];
        }
        if ($originalData['lastName'] !== $student->getLastName()) {
            $changes['lastName'] = [
                'old' => $originalData['lastName'],
                'new' => $student->getLastName()
            ];
        }
        if ($originalData['numero'] !== $student->getStudentNumber()) {
            $changes['numero'] = [
                'old' => $originalData['numero'],
                'new' => $student->getStudentNumber()
            ];
        }

        return $changes;
    }
}