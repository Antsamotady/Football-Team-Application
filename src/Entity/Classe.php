<?php

namespace App\Entity;

use App\Repository\ClasseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClasseRepository::class)]
class Classe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $location;

    #[ORM\ManyToMany(targetEntity: Subject::class, mappedBy: 'classe')]
    private $subjects;

    #[ORM\OneToMany(mappedBy: 'classe', targetEntity: StudentClasse::class)]
    private $studentClasses;

    public function __construct()
    {
        $this->subjects = new ArrayCollection();
        $this->studentClasses = new ArrayCollection();
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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection|Subject[]
     */
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): self
    {
        if (!$this->subjects->contains($subject)) {
            $this->subjects[] = $subject;
            $subject->addClasse($this);
        }

        return $this;
    }

    public function removeSubject(Subject $subject): self
    {
        if ($this->subjects->removeElement($subject)) {
            $subject->removeClasse($this);
        }

        return $this;
    }

    /**
     * @return Collection|StudentClasse[]
     */
    public function getStudentClasses(): Collection
    {
        return $this->studentClasses;
    }

    public function addStudentClass(StudentClasse $studentClass): self
    {
        if (!$this->studentClasses->contains($studentClass)) {
            $this->studentClasses[] = $studentClass;
            $studentClass->setClasse($this);
        }

        return $this;
    }

    public function removeStudentClass(StudentClasse $studentClass): self
    {
        if ($this->studentClasses->removeElement($studentClass)) {
            // set the owning side to null (unless already changed)
            if ($studentClass->getClasse() === $this) {
                $studentClass->setClasse(null);
            }
        }

        return $this;
    }
}
