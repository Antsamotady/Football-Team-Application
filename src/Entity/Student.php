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
    private $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $fanampiny;

    #[ORM\Column(type: 'text', nullable: true)]
    private $gender;

    #[ORM\ManyToOne(targetEntity: Classe::class, inversedBy: 'students')]
    private $classe;

    #[ORM\ManyToOne(targetEntity: ExamLocation::class, inversedBy: 'students')]
    private $examLocation;

    #[ORM\ManyToMany(targetEntity: Mark::class, inversedBy: 'students')]
    private $marks;

    public function __construct()
    {
        $this->marks = new ArrayCollection();
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

    public function getFanampiny(): ?string
    {
        return $this->fanampiny;
    }

    public function setFanampiny(?string $fanampiny): self
    {
        $this->fanampiny = $fanampiny;

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

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): self
    {
        $this->classe = $classe;

        return $this;
    }

    public function getExamLocation(): ?ExamLocation
    {
        return $this->examLocation;
    }

    public function setExamLocation(?ExamLocation $examLocation): self
    {
        $this->examLocation = $examLocation;

        return $this;
    }

    public function getExport() 
    {
        $result = [];
        $result[] = $this->name;
        $result[] = $this->fanampiny;
        $result[] = $this->classe;
        $result[] = $this->examLocation;

        return $result;
    }

    /**
     * @return Collection|Mark[]
     */
    public function getMarks(): Collection
    {
        return $this->marks;
    }

    public function addMark(Mark $mark): self
    {
        if (!$this->marks->contains($mark)) {
            $this->marks[] = $mark;
        }

        return $this;
    }

    public function removeMark(Mark $mark): self
    {
        $this->marks->removeElement($mark);

        return $this;
    }

}
