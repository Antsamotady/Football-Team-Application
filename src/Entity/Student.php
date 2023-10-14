<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $firstname;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $lastname;

    #[ORM\Column(type: 'text', nullable: true)]
    private $gender;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: StudentClasse::class)]
    private $studentClasses;

    public function __construct()
    {
        $this->studentClasses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

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
            $studentClass->setStudent($this);
        }

        return $this;
    }

    public function removeStudentClass(StudentClasse $studentClass): self
    {
        if ($this->studentClasses->removeElement($studentClass)) {
            // set the owning side to null (unless already changed)
            if ($studentClass->getStudent() === $this) {
                $studentClass->setStudent(null);
            }
        }

        return $this;
    }
}
