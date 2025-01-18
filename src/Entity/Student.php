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

    #[ORM\Column(type: 'string', length: 11, nullable: true)]
    private $gender;

    #[ORM\ManyToOne(targetEntity: Classe::class, inversedBy: 'students')]
    private $classe;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: StudentSubject::class)]
    private $subjects;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Score::class)]
    private $scores;

    public function __construct()
    {
        $this->subjects = new ArrayCollection();
        $this->scores = new ArrayCollection();
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

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): self
    {
        $this->classe = $classe;

        return $this;
    }

    /**
     * @return Collection|StudentSubject[]
     */
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(StudentSubject $subject): self
    {
        if (!$this->subjects->contains($subject)) {
            $this->subjects[] = $subject;
            $subject->setStudent($this);
        }

        return $this;
    }

    public function removeSubject(StudentSubject $subject): self
    {
        if ($this->subjects->removeElement($subject)) {
            // set the owning side to null (unless already changed)
            if ($subject->getStudent() === $this) {
                $subject->setStudent(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        $name = $this->firstname . ' ' . $this->lastname;
        return $name;
    }

    /**
     * @return Collection|Score[]
     */
    public function getScores(): Collection
    {
        return $this->scores;
    }

    public function addScore(Score $score): self
    {
        if (!$this->scores->contains($score)) {
            $this->scores[] = $score;
            $score->setStudent($this);
        }

        return $this;
    }

    public function removeScore(Score $score): self
    {
        if ($this->scores->removeElement($score)) {
            // set the owning side to null (unless already changed)
            if ($score->getStudent() === $this) {
                $score->setStudent(null);
            }
        }

        return $this;
    }

    public function getName(){
        return $this->getFirstname() . ' ' . $this->getLastname();
    }

    public function getExport() 
    {
        $result = [];
        $result[] = $this->getGender();
        $result[] = $this->getName();
        $result[] = $this->getClasse()->getName();
        $scores = $this->getScores();
        $sum=0;

        foreach($scores as $score) {
            $sum += $score->getValue();
        }
        
        $result[] = $sum/count($scores);

        return $result;
    }
}
