<?php

namespace App\Service;

use App\Form\ScoreType;
use Symfony\Component\Form\FormFactoryInterface;

class ScoreService
{
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function processScores(array $scores): array
    {
        $forms = [];
        $formViews = [];
        $bestScore = 0;
        $totalScore = 0;
        $sumCoefficient = 0;
        $sumWeightedMarks = 0;
        $weightedAverageScore = 0;

        foreach ($scores as $score) {
            $weight = $score->getSubject()->getCoefficient();
            $scoreValue = $score->getValue();
            $sumWeightedMarks += $scoreValue * $weight;
            $sumCoefficient += $weight;
            $totalScore += $scoreValue;

            if ($scoreValue > $bestScore) {
                $bestScore = $scoreValue;
            }

            $scoreId = $score->getId();
            $forms[$scoreId] = $this->formFactory->create(ScoreType::class, $score);
            $formViews[$scoreId] = $forms[$scoreId]->createView();
        }

        $weightedAverageScore = $scores ? round($sumWeightedMarks / $sumCoefficient, 2) : 0;

        return [
            'formViews' => $formViews,
            'bestScore' => $bestScore,
            'averageScore' => $weightedAverageScore,
            'totalScore' => $totalScore,
        ];
    }
}
