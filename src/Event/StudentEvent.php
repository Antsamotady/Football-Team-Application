<?php

namespace App\Event;

use App\Entity\Student;
use Symfony\Contracts\EventDispatcher\Event;

class StudentEvent extends Event
{
    public const CREATED = 'student.created';
    public const UPDATED = 'student.updated';
    public const DELETED = 'student.deleted';

    /**
     * @param array<string, mixed> $changes
     */
    public function __construct(
        private Student $student,
        private string $action,
        private array $changes = [])
    {
    }

    public function getStudent(): Student
    {
        return $this->student;
    }

    public function getAction(): string
    {
        return $this->action;
    }


    /**
     * @return array<string, mixed>
     */
    public function getChanges(): array
    {
        return $this->changes;
    }
}