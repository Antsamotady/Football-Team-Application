<?php

namespace App\Entity;

use App\Repository\StudentSubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentSubjectRepository::class)]
class StudentSubject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;
    
    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'subjects')]
    private $student;

    #[ORM\ManyToMany(targetEntity: Subject::class, inversedBy: 'studentSubjects')]
    private $subject;

    #[ORM\ManyToOne(targetEntity: Score::class, inversedBy: 'studentSubject')]
    private $score;

    public function __construct()
    {
        $this->subject = new ArrayCollection();
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

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }

    /**
     * @return Collection|Subject[]
     */
    public function getSubject(): Collection
    {
        return $this->subject;
    }

    public function addSubject(Subject $subject): self
    {
        if (!$this->subject->contains($subject)) {
            $this->subject[] = $subject;
        }

        return $this;
    }

    public function removeSubject(Subject $subject): self
    {
        $this->subject->removeElement($subject);

        return $this;
    }

    public function getScore(): ?Score
    {
        return $this->score;
    }

    public function setScore(?Score $score): self
    {
        $this->score = $score;

        return $this;
    }
}
