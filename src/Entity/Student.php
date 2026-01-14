<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\StudentRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['student:read']],
    denormalizationContext: ['groups' => ['student:write']]
)]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['student:read', 'classe:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['student:read', 'classe:read'])]
    private string $firstname;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['student:read', 'classe:read'])]
    private ?string $lastname = null;

    #[ORM\Column(type: 'string', length: 11, nullable: true)]
    #[Groups(['student:read', 'classe:read'])]
    private ?string $gender = null;

    #[ORM\ManyToOne(targetEntity: Classe::class, inversedBy: 'students')]
    private ?Classe $classe = null;

    /**
     * @var Collection<int, StudentSubject>
     */
    #[ORM\OneToMany(mappedBy: 'student', targetEntity: StudentSubject::class)]
    private Collection $subjects;

    /**
     * @var Collection<int, Score>
     */
    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Score::class)]
    private Collection $scores;

    public function __construct()
    {
        $this->subjects = new ArrayCollection();
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

    public function getFirstname(): string
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
     * @return Collection<int, StudentSubject>
     */
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(StudentSubject $subject): self
    {
        if (!$this->subjects->contains($subject)) {
            $this->subjects->add($subject);
            $subject->setStudent($this);
        }
        return $this;
    }

    public function removeSubject(StudentSubject $subject): self
    {
        if ($this->subjects->removeElement($subject)) {
            if ($subject->getStudent() === $this) {
                $subject->setStudent(null);
            }
        }
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
            $score->setStudent($this);
        }
        return $this;
    }

    public function removeScore(Score $score): self
    {
        if ($this->scores->removeElement($score)) {
            if ($score->getStudent() === $this) {
                $score->setStudent(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->firstname . ' ' . ($this->lastname ?? '');
    }

    public function getName(): string
    {
        return $this->__toString();
    }

    /**
     * Export student data as array
     *
     * @return array<int, string|float>  PHPStan-friendly array type
     */
    public function getExport(): array
    {
        $result = [];
        $result[] = $this->gender ?? '';
        $result[] = $this->getName();
        $result[] = $this->classe?->getName() ?? '';

        $scores = $this->getScores();
        $sum = 0.0;
        foreach ($scores as $score) {
            $sum += $score->getValue() ?? 0;
        }
        $result[] = count($scores) ? $sum / count($scores) : 0.0;

        return $result;
    }

}
