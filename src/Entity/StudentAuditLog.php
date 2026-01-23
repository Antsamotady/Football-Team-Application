<?php

namespace App\Entity;

use App\Repository\StudentAuditLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentAuditLogRepository::class)]
class StudentAuditLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $action = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $timestamp = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $owner = null;

    #[ORM\Column]
    private ?int $studentId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $studentName = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $changes = [];

    public function __construct()
    {
        $this->timestamp = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /** @internal Doctrine only */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;
        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): static
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function getOwner(): ?string
    {
        return $this->owner;
    }

    public function setOwner(?string $owner): static
    {
        $this->owner = $owner;
        return $this;
    }

    public function getStudentId(): ?int
    {
        return $this->studentId;
    }

    public function setStudentId(?int $studentId): static
    {
        $this->studentId = $studentId;
        return $this;
    }

    public function getStudentName(): ?string
    {
        return $this->studentName;
    }

    public function setStudentName(?string $studentName): static
    {
        $this->studentName = $studentName;
        return $this;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getChanges(): ?array
    {
        return $this->changes;
    }

    /**
     * @param array<string, mixed>|null $changes
     */
    public function setChanges(?array $changes): static
    {
        $this->changes = $changes;
        return $this;
    }
}