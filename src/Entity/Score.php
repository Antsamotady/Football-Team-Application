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
    private $id;

    #[ORM\Column(type: 'float', nullable: true)]
    private $value;

    #[ORM\OneToMany(mappedBy: 'score', targetEntity: StudentSubject::class)]
    private $studentSubject;

    public function __construct()
    {
        $this->studentSubject = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection|StudentSubject[]
     */
    public function getStudentSubject(): Collection
    {
        return $this->studentSubject;
    }

    public function addStudentSubject(StudentSubject $studentSubject): self
    {
        if (!$this->studentSubject->contains($studentSubject)) {
            $this->studentSubject[] = $studentSubject;
            $studentSubject->setScore($this);
        }

        return $this;
    }

    public function removeStudentSubject(StudentSubject $studentSubject): self
    {
        if ($this->studentSubject->removeElement($studentSubject)) {
            // set the owning side to null (unless already changed)
            if ($studentSubject->getScore() === $this) {
                $studentSubject->setScore(null);
            }
        }

        return $this;
    }
}
