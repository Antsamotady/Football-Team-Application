<?php

namespace App\Service;

use App\Entity\Student;
use App\Repository\StudentRepository;
use App\Repository\ClasseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StudentCsvImporter
{
    public function __construct(
        private EntityManagerInterface $em,
        private StudentRepository $studentRepo,
        private ClasseRepository $classeRepo
    ) {
    }

    public function import(UploadedFile $file): void
    {
        $csv = new \SplFileObject($file->getPathname());
        $csv->setFlags(
            \SplFileObject::READ_CSV
            | \SplFileObject::SKIP_EMPTY
        );
        $csv->setCsvControl(';');

        $row = 0;

        foreach ($csv as $data) {
            $row++;

            // Skip header
            if ($row === 1) {
                continue;
            }

            // Critical guard
            if (!is_array($data) || $data === [null]) {
                continue;
            }

            if (count($data) < 4) {
                throw new \RuntimeException("CSV invalide à la ligne $row");
            }

            [$firstname, $lastname, $gender, $classeName] = array_map(
                static function ($value): string {
                    if ($value === null) {
                        return '';
                    }

                    if (is_string($value)) {
                        return $value;
                    }

                    return (string) $value; // @phpstan-ignore-line
                },
                $data
            );

            if ($firstname === '' || $gender === '') {
                throw new \RuntimeException("Données invalides à la ligne $row");
            }

            $student = $this->studentRepo->findOneBy(['firstname' => $firstname])
                ?? new Student();

            $student
                ->setFirstname($firstname)
                ->setLastname($lastname)
                ->setGender($gender)
                ->setClasse(
                    $this->classeRepo->findOneBy(['name' => $classeName])
                )
                ->setUpdatedAt(new \DateTimeImmutable());

            $this->em->persist($student);
        }

        $this->em->flush();
    }
}
