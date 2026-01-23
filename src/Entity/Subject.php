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
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 128)]
    private string $name;

    #[ORM\Column(type: 'integer')]
    private int $coefficient;

    #[ORM\ManyToOne(targetEntity: Teacher::class, inversedBy: 'subjects')]
    private ?Teacher $teacher = null;

    /**
     * @var Collection<int, Score>
     */
    #[ORM\OneToMany(
        mappedBy: 'subject',
        targetEntity: Score::class,
        cascade: ['remove'],
        orphanRemoval: true
    )]
    private Collection $scores;

    public function __construct()
    {
        $this->scores = new ArrayCollection();
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCoefficient(): int
    {
        return $this->coefficient;
    }

    public function setCoefficient(int $coefficient): self
    {
        $this->coefficient = $coefficient;

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
     * @return Collection<int, Score>
     */
    public function getScores(): Collection
    {
        return $this->scores;
    }

    public function addScore(Score $score): self
    {
        if (!$this->scores->contains($score)) {
            $this->scores->add($score);
            $score->setSubject($this);
        }

        return $this;
    }

    public function removeScore(Score $score): self
    {
        if ($this->scores->removeElement($score)) {
            if ($score->getSubject() === $this) {
                $score->setSubject(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
