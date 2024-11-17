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

        foreach ($scores as $score) {
            $scoreValue = $score->getValue();
            $totalScore += $scoreValue;

            if ($scoreValue > $bestScore) {
                $bestScore = $scoreValue;
            }

            $scoreId = $score->getId();
            $forms[$scoreId] = $this->formFactory->create(ScoreType::class, $score);
            $formViews[$scoreId] = $forms[$scoreId]->createView();
        }

        return [
            'formViews' => $formViews,
            'bestScore' => $bestScore,
            'totalScore' => $totalScore,
        ];
    }
}
