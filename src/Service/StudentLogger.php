<?php

namespace App\Service;

use App\Entity\Student;
use App\Entity\StudentAuditLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @phpstan-type StudentLogEntry array{
 *     timestamp: \DateTimeInterface,
 *     action: string,
 *     user: string,
 *     student_id: int|null,
 *     student_name: string,
 *     changes: array<string, mixed>
 * }
 */
class StudentLogger
{
    private string $logDirectory;
    private Security $security;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, Security $security, string $projectDir)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->logDirectory = $projectDir . '/public/uploads/student';
        
        // Create directory if it doesn't exist
        if (!is_dir($this->logDirectory)) {
            mkdir($this->logDirectory, 0777, true);
        }
    }

    /**
     * @param array<string, mixed> $changes
     */
    public function log(string $action, Student $student, array $changes = []): void
    {
        $user = $this->security->getUser();
        $username = $user ? $user->getUserIdentifier() : 'anonymous';

        $timestamp = new \DateTimeImmutable();

        /** @var StudentLogEntry $logEntry */
        $logEntry = [
            'timestamp' => $timestamp, // DateTime for DB
            'action' => $action,
            'user' => $username,
            'student_id' => $student->getId(),
            'student_name' => $student->getName(),
            'changes' => $changes,
        ];

        $this->writeToFile($logEntry, $timestamp);
        $this->saveToDatabase($logEntry);
    }

    /**
     * @param StudentLogEntry $logEntry
     */
    private function saveToDatabase(array $logEntry): void
    {
        $auditLog = new StudentAuditLog();
        $auditLog->setAction($logEntry['action']);
        $auditLog->setTimestamp($logEntry['timestamp']);
        $auditLog->setOwner($logEntry['user']);
        $auditLog->setStudentId($logEntry['student_id']);   // Parameter #1 $studentId of method App\Entity\StudentAuditLog::setStudentId() expects int, int|null given.
        $auditLog->setStudentName($logEntry['student_name']);
        $auditLog->setChanges($logEntry['changes']);

        $this->entityManager->persist($auditLog);
        $this->entityManager->flush();
    }

    /**
     * @param StudentLogEntry $logEntry
     */
    private function writeToFile(array $logEntry, \DateTimeInterface $timestamp): void
    {
        $logFile = $this->logDirectory . '/student_activity.log';

        $fileEntry = $logEntry;
        $fileEntry['timestamp'] = $timestamp->format('Y-m-d H:i:s');

        $jsonEntry = json_encode($fileEntry, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        file_put_contents($logFile, $jsonEntry . PHP_EOL . PHP_EOL, FILE_APPEND);
    }
}