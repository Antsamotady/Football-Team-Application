<?php

namespace App\Service;

use App\Entity\Configuration;
use App\Entity\Student;
use App\Entity\Subject;
use Doctrine\ORM\EntityManagerInterface;

class BulletinService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * @return array<mixed|null>
     */
    public function getBulletinData(Student $student): array
    {
        $config = $this->entityManager->getRepository(Configuration::class)
            ->find(1);
        
        $passingScore = $config?->getPassingScore();
        $academicYear = $config?->getAcademicYear();
        $deliberationDate = $config?->getDeliberationDate();

        // Get all subjects for the school
        $subjects = $this->entityManager->getRepository(Subject::class)
            ->findBy([], ['name' => 'ASC']);
        
        // Calculate statistics
        $weightedSum = 0;
        $totalCoefficient = 0;
        $subjectsData = [];
        $hasAnyScore = false;
        
        foreach ($subjects as $subject) {
            // Find the student's score for this subject
            $score = null;
            $scoreValue = null;
            
            foreach ($student->getScores() as $studentScore) {
                if ($studentScore->getSubject() === $subject) {
                    $score = $studentScore;
                    $scoreValue = $studentScore->getValue();
                    break;
                }
            }
            
            // Calculate if the student passed this subject (score >= 10)
            $passed = $scoreValue !== null && $scoreValue >= $passingScore;
            
            if ($scoreValue !== null) {
                $hasAnyScore = true;
                $weightedSum += $scoreValue * $subject->getCoefficient();
                $totalCoefficient += $subject->getCoefficient();
            }
            
            $subjectsData[] = [
                'subject' => $subject,
                'score' => $score,
                'value' => $scoreValue,
                'coefficient' => $subject->getCoefficient(),
                'passed' => $passed,
                'weightedValue' => $scoreValue !== null ? $scoreValue * $subject->getCoefficient() : null,
            ];
        }
        
        // Calculate overall average
        $overallAverage = $totalCoefficient > 0 ? $weightedSum / $totalCoefficient : null;
        
        // Determine if student passed overall (average >= 10)
        $overallPassed = $overallAverage !== null && $overallAverage >= $passingScore;
        
        // Get class rank if applicable (you might want to calculate this)
        $classRank = $this->calculateClassRank($student, $overallAverage);
        
        return [
            'student'                   => $student,
            'subjectsData'              => $subjectsData,
            'overallAverage'            => $overallAverage,
            'overallAverageFormatted'   => $overallAverage !== null ? number_format($overallAverage, 2, ',', ' ') : 'N/A',
            'totalCoefficient'          => $totalCoefficient,
            'weightedSum'               => $weightedSum,
            'overallPassed'             => $overallPassed,
            'classRank'                 => $classRank,
            'hasScores'                 => $hasAnyScore,
            'academicYear'              => $academicYear,
            'generatedAt'               => $deliberationDate,
        ];
    }
    
    /**
     * @return array<mixed|null>
     */
    private function calculateClassRank(Student $student, ?float $studentAverage): ?array
    {
        if (!$student->getClasse() || $studentAverage === null) {
            return null;
        }
        
        $classStudents = $student->getClasse()->getStudents();
        $studentsWithAverages = [];
        
        foreach ($classStudents as $classStudent) {
            $average = $this->calculateStudentAverage($classStudent);
            if ($average !== null) {
                $studentsWithAverages[] = [
                    'student' => $classStudent,
                    'average' => $average,
                ];
            }
        }
        
        // Sort by average descending
        usort($studentsWithAverages, function($a, $b) {
            return $b['average'] <=> $a['average'];
        });
        
        // Find the student's rank
        $rank = null;
        $totalStudents = count($studentsWithAverages);
        
        foreach ($studentsWithAverages as $index => $studentData) {
            if ($studentData['student']->getId() === $student->getId()) {
                $rank = $index + 1;
                break;
            }
        }
        
        if ($rank === null) {
            return null;
        }
        
        return [
            'rank' => $rank,
            'total' => $totalStudents,
            'percentage' => round(($rank / $totalStudents) * 100, 1),
        ];
    }
    
    private function calculateStudentAverage(Student $student): ?float
    {
        $weightedSum = 0;
        $totalCoefficient = 0;
        
        foreach ($student->getScores() as $score) {
            $value = $score->getValue();
            $subject = $score->getSubject();
            
            if ($value !== null && $subject !== null) {
                $weightedSum += $value * $subject->getCoefficient();
                $totalCoefficient += $subject->getCoefficient();
            }
        }
        
        return $totalCoefficient > 0 ? $weightedSum / $totalCoefficient : null;
    }
}