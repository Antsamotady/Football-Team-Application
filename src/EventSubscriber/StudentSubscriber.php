<?php

namespace App\EventSubscriber;

use App\Event\StudentEvent;
use App\Service\StudentLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StudentSubscriber implements EventSubscriberInterface
{
    public function __construct(private StudentLogger $studentLogger)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            StudentEvent::CREATED => 'onStudentCreated',
            StudentEvent::UPDATED => 'onStudentUpdated',
            StudentEvent::DELETED => 'onStudentDeleted',
            StudentEvent::IMPORTED => 'onStudentImported',
        ];
    }

    public function onStudentCreated(StudentEvent $event): void
    {
        $this->studentLogger->log('CREATE', $event->getStudent());
    }

    public function onStudentUpdated(StudentEvent $event): void
    {
        $this->studentLogger->log('UPDATE', $event->getStudent(), $event->getChanges());
    }

    public function onStudentDeleted(StudentEvent $event): void
    {
        $this->studentLogger->log('DELETE', $event->getStudent());
    }

    public function onStudentImported(StudentEvent $event): void
    {
        $this->studentLogger->log('IMPORTED', $event->getStudent());
    }
}
