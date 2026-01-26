<?php

namespace App\Service;

use App\Entity\Score;
use App\Entity\Student;
use App\Form\ScoreOnlyType;
use App\Repository\ScoreRepository;
use Symfony\Component\Form\FormFactoryInterface;

class ScoreService
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private ScoreRepository $scoreRepository
    ) {
    }

    /**
     * @param array<Student> $students
     * @return array<int, array<string, mixed>>
     */
    public function buildStudentsScores(array $students): array
    {
        $studentsScores = [];

        foreach ($students as $student) {
            $scores = $this->scoreRepository->findBy(
                ['student' => $student],
                ['subject' => 'ASC']
            );

            $results = $this->processScores($scores);
            $id = $student->getId();
            if ($id === null) {
                throw new \LogicException('Student must be persisted before building scores.');
            }

            $studentsScores[(int) $id] = [
                'student'        => $student,
                'best_score'     => $results['bestScore'],
                'total_score'    => $results['totalScore'],
                'average_score'  => $results['averageScore'],
                'score_forms'    => $results['formViews'],
                'subjects_scores'=> $results['subjectsScores'], // ✅ NEW
            ];
        }

        return $studentsScores;
    }

    /**
     * @param array<Score> $scores
     * @return array<string, mixed>
     */
    public function processScores(array $scores): array
    {
        $formViews = [];
        $bestScore = 0;
        $totalScore = 0;
        $sumCoefficient = 0;
        $sumWeightedMarks = 0;
        $subjectsScores = [];

        foreach ($scores as $score) {
            $subject = $score->getSubject();
            if ($subject === null) {
                throw new \LogicException('Score has no subject assigned');
            }

            $scoreValue = $score->getValue();
            $weight = $subject->getCoefficient();

            $sumWeightedMarks += $scoreValue * $weight;
            $sumCoefficient += $weight;
            $totalScore += $scoreValue;

            if ($scoreValue > $bestScore) {
                $bestScore = $scoreValue;
            }

            // ✅ subject → score mapping for Twig
            $subjectsScores[] = [
                'subject' => $subject->getName(),
                'score'   => $scoreValue,
            ];

            $form = $this->formFactory->create(ScoreOnlyType::class, $score);
            $formViews[$score->getId()] = $form->createView();
        }

        $averageScore = $scores && $sumCoefficient > 0
            ? round($sumWeightedMarks / $sumCoefficient, 2)
            : 0;

        return [
            'formViews'       => $formViews,
            'bestScore'       => $bestScore,
            'totalScore'      => $totalScore,
            'averageScore'    => $averageScore,
            'subjectsScores'  => $subjectsScores,
        ];
    }
}
