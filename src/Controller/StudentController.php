<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\Subject;
use App\Form\StudentType;
use App\Form\SubjectType;
use App\Data\StudentFilterData;
use App\Data\StudentSearchData;
use App\Form\StudentFilterFormType;
use App\Form\StudentSearchFormType;
use App\Repository\StudentRepository;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/student')]
class StudentController extends AbstractController
{
    #[Route('/', name: 'student_index', methods: ['GET'])]
    public function index(StudentRepository $studentRepo): Response
    {
        return $this->render('student/index.html.twig', [
            'students' => $studentRepo->findAll(),
        ]);
    }

    #[Route('/list', name: 'student_list', methods: ['GET'])]
    public function list(Request $request, StudentRepository $studentRepo): Response
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

        return $this->render('student/list.html.twig', [
            'template_title' => 'Liste des étudiants',
            'meth_name' => 'list',
            'form' => $form->createView(),
            'filter_form' => $filterForm->createView(),
            'students' => $students,
        ]);
    }

    #[Route('/import', name: 'student_import', methods: ['POST'])]
    public function import(Request $request, EntityManagerInterface $em, StudentRepository $studentRepo): Response
    {
        $header = array('name' => 0, 'fanampiny' => 1, 'gender' => 2, 'classe' => 3, 'examLocation' => 4);
        $path = __DIR__ . '/../../public/upload/';
        $file = $request->files->get('clientimport');
        $file->move($path, "tmp.csv");

        $row = 1;
        
        if (($handle = fopen($path . "tmp.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $data = $this->decrypteinutf8($data); // put in utf8;

                if ($row == 1) {
                        
                } else {
                    if ($data[$header['name']] != '' && 
                        $data[$header['gender']] != '' ) {

                        $student = $studentRepo->findOneBy(['name' => $data[$header['name']] ]);

                        if (!$student) $student = new Student();

                        $student->setName($data[$header['name']]);
                        $student->setFanampiny($data[$header['fanampiny']]);
                        $student->setGender($data[$header['gender']]);
                        // $student->setClasse($data[$header['classe']]);
                        // $student->setExamLocation($data[$header['examLocation']]);

                        $em->persist($student);
                        $em->flush();
                    } else {
                        $this->addFlash('error', 'Erreur à la ligne : ' . $row);
                        return $this->redirectToRoute('student_list');
                    }
                }
                $row++;
            }

            fclose($handle);

            //delete tmp file
            unlink($path . "tmp.csv");

            $this->addFlash('success', 'La liste a bien été importé.');

            return $this->redirectToRoute('student_list');
        } else {
            $this->addFlash('error', 'Erreur: le fichier n\'est pas valide');
        }

        return $this->redirectToRoute('student_list');
    }

    #[Route('/export', name: 'student_export', methods: ['GET'])]
    public function export(StudentRepository $studentRepo): Response
    {
        $items = $studentRepo->findAll();

        $handle = fopen('php://memory', 'r+');
        $header = array('name' => 0, 'fanampiny' => 1, 'gender' => 2, 'classe' => 3, 'examLocation' => 4);

        fputs($handle, chr(239) . chr(187) . chr(191));
        fputcsv($handle, $header, ';');
        foreach ($items as $item) {
            $result = $item->getExport();
            fputcsv($handle, $result, ';');
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return new Response($content, 200, array(
            'Content-Type' => 'application/force-download; ',
            'Content-Disposition' => 'attachment; filename="liste_etudiants.csv"'
        ));
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
    public function show(Request $request, Student $student, StudentRepository $studentRepo, SubjectRepository $subjectRepo, EntityManagerInterface $em): Response
    {
        $previousStudent = null;
        $nextStudent = null;
        $firstStudent = $studentRepo->findOneBy([], ['id' => 'ASC']);
        $lastStudent = $studentRepo->findOneBy([], ['id' => 'DESC']);

        if ($student != $firstStudent)
            $previousStudent = $studentRepo->findOneBy(['id' => $student->getId() - 1]);

        if ($student != $lastStudent)
            $nextStudent = $studentRepo->findOneBy(['id' => $student->getId() + 1]);

        $subjects = $subjectRepo->findBy(['student' => $student]);

        $forms = [];
        $formViews = [];

        foreach($subjects as $subject) {
            $subjectId = $subject->getId();
            
            $forms[$subjectId] = $this->createForm(SubjectType::class, $subject);
            $formViews[$subjectId] = $forms[$subjectId]->createView();
            $forms[$subjectId]->handleRequest($request);

            if ($forms[$subjectId]->isSubmitted() && $forms[$subjectId]->isValid()) {
                dd('here');
                $em->persist($subject);
                $em->flush();

                $this->addFlash('success', 'Enregistrement effectué.');
            }
        }

        return $this->render('student/show.html.twig', [
            'template_title' => 'Détails étudiant',
            'student' => $student,
            'previous' => $previousStudent ? $previousStudent->getId() : null,
            'next' => $nextStudent ? $nextStudent->getId() : null,
            'score_forms' => $formViews
        ]);
    }

    #[Route('/{id}/edit', name: 'student_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Student $student, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Modification réussie.');

            return $this->redirectToRoute('student_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('student/edit.html.twig', [
            'template_title' => 'Modifier un étudiant',
            'student' => $student,
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

    /* Import csv */
    protected function decrypteinutf8($datas) {
        $datas_return = array();
        foreach ($datas as $value) {
            $datas_return[] = (preg_match('!!u', $value)) ? $value : utf8_encode($value);
        }
        return $datas_return;
    }
}
