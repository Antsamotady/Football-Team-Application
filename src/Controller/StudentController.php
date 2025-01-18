<?php

namespace App\Controller;

use App\Entity\Score;
use App\Entity\Student;
use App\Entity\Subject;
use App\Form\StudentType;
use App\Service\ScoreService;
use App\Data\StudentFilterData;
use App\Data\StudentSearchData;
use App\Form\StudentFilterFormType;
use App\Form\StudentSearchFormType;
use App\Repository\ScoreRepository;
use App\Repository\ClasseRepository;
use App\Repository\StudentRepository;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/student')]
class StudentController extends AbstractController
{
	public function __construct(
		public EntityManagerInterface $em,
		private ScoreService $scoreService
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
		ScoreRepository $scoreRepo): Response
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

			if (!isset($name)) $students = $studentRepo->findAll();
			else $students = $studentRepo->findSearch($data);

		} elseif ($filterForm->isSubmitted() && $filterForm->isValid()) 
			$students = $studentRepo->findFiltered($filteredData);
		else
			$students = $studentRepo->findAll();

		$result = count($students);
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

		return $this->render('student/list.html.twig', [
			'template_title' => 'Liste des étudiants',
			'meth_name' 		=> 'list',
			'form' 					=> $form->createView(),
			'filter_form' 	=> $filterForm->createView(),
			'students' 			=> $students,
			'result'				=> $result,
			'students_scores' => $studentsScores,
		]);
	}

	#[Route('/import', name: 'student_import', methods: ['POST'])]
	public function import(Request $request, EntityManagerInterface $em, StudentRepository $studentRepo, ClasseRepository $classeRepo): Response
	{
		$header = array('firstname' => 0, 'lastname' => 1, 'gender' => 2, 'classe' => 3);
		$path = __DIR__ . '/../../public/upload/student/';
		$file = $request->files->get('student-import');
		$file->move($path, "tmp.csv");

		$row = 1;
		
		if (($handle = fopen($path . "tmp.csv", "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
				$data = $this->decrypteinutf8($data); // put in utf8;

				if ($row == 1) { // just skip the head title
				} else {
					if ($data[$header['firstname']] != '' && 
							$data[$header['gender']] != '' ) {
						$student = $studentRepo->findOneBy(['firstname' => $data[$header['firstname']] ]);

						if (!$student) $student = new Student();
						$student->setFirstname($data[$header['firstname']]);
						$student->setlastname($data[$header['lastname']]);
						$student->setGender($data[$header['gender']]);
						$student->setClasse($classeRepo->findOneBy(['name' => $data[$header['classe']]]));

						$em->persist($student);
					} else {
						$this->addFlash('error', 'Erreur à la ligne : ' . $row);

						return $this->redirectToRoute('student_list');
					}
				}
				$row++;
			}
			$em->flush();

			fclose($handle);
			unlink($path . "tmp.csv");

			$this->addFlash('success', 'La liste a bien été importé.');

			return $this->redirectToRoute('student_list');
		} else {
			$this->addFlash('error', 'Erreur: le fichier n\'est pas valide');
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
			$em->persist($student);
			$em->flush();

			$this->addFlash('success', 'Ajout étudiant réussi.');
			return $this->redirectToRoute('student_list', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('student/new.html.twig', [
			'template_title' => 'Ajouter',
			'student' => $student,
			'form' => $form,
		]);
	}

	#[Route('/{id}', name: 'student_show', methods: ['GET'])]
	public function show(
		Request $request, 
		Student $student, 
		StudentRepository $studentRepo, 
		EntityManagerInterface $em, 
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

		$this->ensureStudentScoresComplete($student, $em);

		$scores = $scoreRepo->findBy(
			['student' => $student], 
			['subject' => 'ASC']
		);

		$scoreResults = $this->scoreService->processScores($scores);

		return $this->render('student/show.html.twig', [
			'template_title' 	=> 'Détails étudiant',
			'student' 				=> $student,
			'scores'					=> $scores,
			'previous' 				=> $previousStudent ? $previousStudent->getId() : null,
			'next' 						=> $nextStudent ? $nextStudent->getId() : null,
			'best_score' 			=> $scoreResults['bestScore'],
			'total_score' 		=> $scoreResults['totalScore'],
			'average_score' 	=> $scores ? $scoreResults['totalScore'] / count($scores) : 0,
			'score_forms' 		=> $scoreResults['formViews']
		]);
	}

	#[Route('/ajax/update-score', name: 'score_update', methods: ['POST'])]
	public function updateScore(Request $request, EntityManagerInterface $em, ScoreRepository $scoreRepo): JsonResponse
	{
		$content = $request->getContent();
		$data = json_decode($content, true);

		$scoreId = $data['scoreId'];
		$newScore = (float) $data['newScore'];

		$score = $scoreRepo->find($scoreId);
		$score->setValue($newScore);

		$em->persist($score);

		try {
			$em->flush();
		} catch (\Exception $e) {
			return new JsonResponse([
				'status' => 'KO',
				'message' => $e->getMessage(),
				'input' => $data['newScore']
			]);
		}

		return new JsonResponse(['status' => 'OK']);
	}
	
	#[Route('/{id}/edit', name: 'student_edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Student $student, EntityManagerInterface $em, StudentRepository $studentRepo, SubjectRepository $subjectRepo, ScoreRepository $scoreRepo): Response
	{
		$previousStudent = null;
		$nextStudent = null;
		$firstStudent = $studentRepo->findOneBy([], ['id' => 'ASC']);
		$lastStudent = $studentRepo->findOneBy([], ['id' => 'DESC']);

		if ($student != $firstStudent)
			$previousStudent = $studentRepo->findOneBy(['id' => $student->getId() - 1]);

		if ($student != $lastStudent)
			$nextStudent = $studentRepo->findOneBy(['id' => $student->getId() + 1]);
		
		$form = $this->createForm(StudentType::class, $student);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em->flush();

			$this->addFlash('success', 'Modification réussie.');

			return $this->redirectToRoute('student_show', ['id' => $student->getId()], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('student/edit.html.twig', [
			'template_title' => 'Modifier un étudiant',
			'student' => $student,
			'previous' => $previousStudent ? $previousStudent->getId() : null,
			'next' => $nextStudent ? $nextStudent->getId() : null,
			'form' => $form,
		]);
	}

	#[Route('/{id}', name: 'student_delete', methods: ['POST'])]
	public function delete(Request $request, Student $student, EntityManagerInterface $em): Response
	{
		if ($this->isCsrfTokenValid('delete'.$student->getId(), $request->request->get('_token'))) {
			$em->remove($student);
			$em->flush();

			$this->addFlash('success', 'Suppression réussie.');
		}

		return $this->redirectToRoute('student_index', [], Response::HTTP_SEE_OTHER);
	}

	#[Route('/export-all', name: 'student_export', methods: ['GET'])]
	public function exportStudentCsv(): Response
	{
		dd('hereh');
		$students = $this->em->getRepository(Student::class)->findAll();

		$response = new StreamedResponse(function () use ($students) {
			$output = fopen('php/output', 'w');
			fputcsv($output, ['Civilité', 'Nom', 'Classe', 'Moyenne'], ';');

			foreach ($students as $student) {
				fputcsv($output, $student->getExport(), ';');
			}
			fclose($output);
		});

		$response->headers->set('Content-type', 'text/csv');
		$response->headers->set('Content-Disposition', 'attachement; filename="export_etudiants.csv');

		return $response;
	}

	/* Import csv */
	protected function decrypteinutf8($datas) {
		$datas_return = array();
		foreach ($datas as $value) {
			$datas_return[] = (preg_match('!!u', $value)) ? $value : utf8_encode($value);
		}
		return $datas_return;
	}

	private function ensureStudentScoresComplete(Student $student, $em)
	{
    $subjectRepository = $em->getRepository(Subject::class);
    $subjects = $subjectRepository->findAll();
		
    $scoreRepository = $em->getRepository(Score::class);
    $scores = $scoreRepository->findBy(['student' => $student]);

    $existingScores = [];
    foreach ($scores as $score) {
			$existingScores[$score->getSubject()->getId()] = $score;
    }

    foreach ($subjects as $subject) {
			if (!isset($existingScores[$subject->getId()])) {
				$newScore = new Score();
				$newScore->setStudent($student);
				$newScore->setSubject($subject);
				$newScore->setValue(0);
				$em->persist($newScore);
			}
    }
    $em->flush();
	}

}
