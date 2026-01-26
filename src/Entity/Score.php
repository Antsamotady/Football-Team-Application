<?php

namespace App\Entity;

use App\Repository\ScoreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ScoreRepository::class)]
class Score
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Assert\Range(
        max: 20,
        maxMessage: 'The score must be less than or equal to {{ limit }}.'
    )]
    private ?float $value = null;

    #[ORM\ManyToOne(targetEntity: Subject::class, inversedBy: 'scores')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Subject $subject = null;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'scores')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Student $student = null;

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

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(?float $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;
        return $this;
    }
}
