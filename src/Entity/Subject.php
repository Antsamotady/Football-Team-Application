<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 128)]
    private $name;

    #[ORM\Column(type: 'integer')]
    private $coefficient;

    #[ORM\ManyToMany(targetEntity: StudentSubject::class, mappedBy: 'subject')]
    private $studentSubjects;

    #[ORM\ManyToOne(targetEntity: Teacher::class, inversedBy: 'subjects')]
    private $teacher;

    #[ORM\OneToMany(mappedBy: 'subject', targetEntity: Score::class)]
    private $scores;

    public function __construct()
    {
        $this->studentSubjects = new ArrayCollection();
        $this->j = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCoefficient(): ?int
    {
        return $this->coefficient;
    }

    public function setCoefficient(int $coefficient): self
    {
        $this->coefficient = $coefficient;

        return $this;
    }

    /**
     * @return Collection|StudentSubject[]
     */
    public function getStudentSubjects(): Collection
    {
        return $this->studentSubjects;
    }

    public function addStudentSubject(StudentSubject $studentSubject): self
    {
        if (!$this->studentSubjects->contains($studentSubject)) {
            $this->studentSubjects[] = $studentSubject;
            $studentSubject->addSubject($this);
        }

        return $this;
    }

    public function removeStudentSubject(StudentSubject $studentSubject): self
    {
        if ($this->studentSubjects->removeElement($studentSubject)) {
            $studentSubject->removeSubject($this);
        }

        return $this;
    }

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    /**
     * @return Collection|Score[]
     */
    public function getJ(): Collection
    {
        return $this->scores;
    }

    public function addJ(Score $scores): self
    {
        if (!$this->scores->contains($scores)) {
            $this->scores[] = $scores;
            $scores->setSubject($this);
        }

        return $this;
    }

    public function removeJ(Score $scores): self
    {
        if ($this->scores->removeElement($scores)) {
            // set the owning side to null (unless already changed)
            if ($scores->getSubject() === $this) {
                $scores->setSubject(null);
            }
        }

        return $this;
    }
}
