<?php

namespace App\Service;

use App\Entity\Student;
use App\Data\StudentSearchData;
use App\Data\StudentFilterData;

class StudentExporter
{
    public function __construct(
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
        /** @var list<list<string|int|float|null>> $data */
        $data = array_values(array_map(
            fn (Student $student) => $student->getExport(),
            $students
        ));

        $headers = ['Civilité', 'Nom', 'Classe', 'Moyenne'];

        return $this->csvExporter->export($data, $headers);
    }

}