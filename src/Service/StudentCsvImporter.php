<?php

namespace App\Service;

use App\Entity\Classe;
use App\Entity\Student;
use App\Event\StudentEvent;
use App\Repository\ClasseRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class StudentCsvImporter
{
    public function __construct(
        private EntityManagerInterface $em,
        private StudentRepository $studentRepo,
        private ClasseRepository $classeRepo,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function import(
        UploadedFile $file,
        ?Classe $forcedClasse = null
    ): void {
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

            [$studentNumber, $firstname, $lastname, $gender, $classeName] = array_map(
                static function ($value): string {
                    if ($value === null) {
                        return '';
                    }

                    if (is_string($value)) {
                        return $value;
                    }

                    return $value; // @phpstan-ignore-line
                },
                $data
            );

            if ($firstname === '' || $gender === '') {
                throw new \RuntimeException("Données invalides à la ligne $row");
            }

            $student = $this->studentRepo->findOneBy(['firstname' => $firstname])
                ?? new Student();

            $classe = $forcedClasse
                ?? $this->classeRepo->findOneBy(['name' => $classeName]);

            $student
                ->setStudentNumber((int) $studentNumber)
                ->setFirstname($firstname)
                ->setLastname($lastname)
                ->setGender($gender)
                ->setClasse($classe)
                ->setUpdatedAt(new \DateTimeImmutable());

            $this->em->persist($student);

            $event = new StudentEvent($student, StudentEvent::IMPORTED);
            $this->eventDispatcher->dispatch($event, StudentEvent::IMPORTED);
        }

        $this->em->flush();
    }
}
