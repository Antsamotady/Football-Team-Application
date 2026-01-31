<?php

namespace App\Service;

use App\Entity\Student;
use App\Entity\Subject;
use App\Data\StudentFilterData;
use App\Data\StudentSearchData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;

class StudentExporter
{
    public function __construct(
        private CacheInterface $cache,
        private EntityManagerInterface $em,
        private CsvExporter $csvExporter
    ) {
    }

    public function generateFilename(
        StudentSearchData|StudentFilterData|null $criteria = null,
        ?string $classeName = null
    ): string {
        $timestamp = date('Y-m-d_H-i');
        $fileName = '';

        if ($classeName) {
            $baseName = sprintf('classe_%s', $classeName);
        } else {
            $baseName = 'etudiants';
        }

        if ($criteria instanceof StudentSearchData) {
            $searchTerm = $criteria->getName() ?? '';
            $fileName = sprintf('%s_recherche_%s_%s.csv', $baseName, $searchTerm, $timestamp);
        } elseif ($criteria instanceof StudentFilterData) {
            $fileName = sprintf('%s_filtre_%s.csv', $baseName, $timestamp);
        } else {
            $fileName = sprintf('%s_complet_%s.csv', $baseName, $timestamp);
        }

        $clean = preg_replace('/[^\w\-\.]/', '_', $fileName);

        return $clean === null ? '' : $clean;
    }


    /**
     * @param Student[] $students
     */
    public function exportStudents(array $students): string
    {
        // Cache subjects (they don't change often)
        // $subjects = $this->cache->get('export_subjects_list', function(ItemInterface $item) {
        //     $item->expiresAfter(3600); // 1 hour
        //     return $this->em->getRepository(Subject::class)
        //         ->findBy([], ['name' => 'ASC']);
        // });

        $subjects = $this->em->getRepository(Subject::class)
                ->findBy([], ['name' => 'ASC']);
        
        // Create headers - ranking first
        $headers = ['Classement', 'Sexe', 'Nom', 'Classe'];
        foreach ($subjects as $subject) {
            $headers[] = $subject->getName();
        }
        $headers[] = 'Moyenne (pondérée)';
        
        // Calculate averages for sorting
        $studentsWithAverages = [];
        foreach ($students as $student) {
            $studentsWithAverages[] = [
                'student' => $student,
                'average' => $this->calculateWeightedAverage($student),
            ];
        }
        
        // Sort by average (highest first)
        usort($studentsWithAverages, function($a, $b) {
            return $b['average'] <=> $a['average'];
        });
        
        // Prepare data with ranking
        $data = [];
        $rank = 1;
        $previousAverage = null;
        $sameRankCount = 0; // Start at 1 for the first student
        
        foreach ($studentsWithAverages as $studentData) {
            $student = $studentData['student'];
            $currentAverage = $studentData['average'];
            
            // Handle ties - assign same rank to students with same average
            if ($previousAverage !== null && abs($currentAverage - $previousAverage) < 0.001) {
                // Same average as previous student, keep same rank
                $sameRankCount++;
            } else {
                // Different average, increase rank by number of tied students
                $rank += $sameRankCount;
                $sameRankCount = 1;
            }
            
            $row = $student->getExportWithSubjects($subjects);
            // Add rank at the beginning
            array_unshift($row, $rank);
            $data[] = $row;
            
            $previousAverage = $currentAverage;
        }
        
        return $this->csvExporter->export($data, $headers);
    }
        
    /**
     * Calculate weighted average for a student (for sorting)
     * 
     * @param Student $student
     */
    private function calculateWeightedAverage(Student $student): float
    {
        $cacheKey = 'student_avg_' . $student->getId();
        
        return $this->cache->get($cacheKey, function(ItemInterface $item) use ($student) {
            $item->expiresAfter(300); // 5 minutes
            
            $totalWeightedSum = 0.0;
            $totalCoefficient = 0;
            
            foreach ($student->getScores() as $score) {
                $value = $score->getValue();
                $subject = $score->getSubject();
                
                if ($value !== null && $subject !== null) {
                    $coefficient = $subject->getCoefficient();
                    $totalWeightedSum += $value * $coefficient;
                    $totalCoefficient += $coefficient;
                }
            }
            
            return $totalCoefficient > 0 ? round($totalWeightedSum / $totalCoefficient, 2) : 0.0;
        });
    }
}