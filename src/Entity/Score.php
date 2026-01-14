<?php

namespace App\Entity;

use App\Repository\ScoreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScoreRepository::class)]
class Score
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $value = null;

    /**
     * @var Collection<int, StudentSubject>
     */
    #[ORM\OneToMany(mappedBy: 'score', targetEntity: StudentSubject::class)]
    private Collection $studentSubject;

    #[ORM\ManyToOne(targetEntity: Subject::class, inversedBy: 'scores', cascade: ['remove'])]
    private ?Subject $subject = null;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'scores')]
    private ?Student $student = null;

    public function __construct()
    {
        $this->studentSubject = new ArrayCollection();
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

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(?float $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return Collection<int, StudentSubject>
     */
    public function getStudentSubject(): Collection
    {
        return $this->studentSubject;
    }

    public function addStudentSubject(StudentSubject $studentSubject): self
    {
        if (!$this->studentSubject->contains($studentSubject)) {
            $this->studentSubject->add($studentSubject);
            $studentSubject->setScore($this);
        }
        return $this;
    }

    public function removeStudentSubject(StudentSubject $studentSubject): self
    {
        if ($this->studentSubject->removeElement($studentSubject)) {
            if ($studentSubject->getScore() === $this) {
                $studentSubject->setScore(null);
            }
        }
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
