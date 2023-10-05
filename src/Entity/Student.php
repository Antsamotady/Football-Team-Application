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

    #[ORM\ManyToMany(targetEntity: Matiere::class, inversedBy: 'students')]
    private $matiere;

    public function __construct()
    {
        $this->matiere = new ArrayCollection();
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
     * @return Collection|Matiere[]
     */
    public function getMatiere(): Collection
    {
        return $this->matiere;
    }

    public function addMatiere(Matiere $matiere): self
    {
        if (!$this->matiere->contains($matiere)) {
            $this->matiere[] = $matiere;
        }

        return $this;
    }

    public function removeMatiere(Matiere $matiere): self
    {
        $this->matiere->removeElement($matiere);

        return $this;
    }

}
