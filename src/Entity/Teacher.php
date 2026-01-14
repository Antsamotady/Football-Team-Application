<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TeacherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TeacherRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['teacher:read']],
    denormalizationContext: ['groups' => ['teacher:write']]
)]
class Teacher
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['teacher:read', 'classe:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 64)]
    #[Groups(['teacher:read', 'classe:read'])]
    private string $firstname;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    #[Groups(['teacher:read', 'classe:read'])]
    private ?string $lastname = null;

    #[ORM\Column(type: 'string', length: 11)]
    #[Groups(['teacher:read', 'classe:read'])]
    private string $gender;

    /**
     * @var Collection<int, Classe>
     */
    #[ORM\ManyToMany(targetEntity: Classe::class, inversedBy: 'teachers')]
    #[Groups(['teacher:read', 'classe:read'])]
    private Collection $classe;

    /**
     * @var Collection<int, Subject>
     */
    #[ORM\OneToMany(mappedBy: 'teacher', targetEntity: Subject::class)]
    private Collection $subjects;

    public function __construct()
    {
        $this->classe = new ArrayCollection();
        $this->subjects = new ArrayCollection();
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

    public function getGender(): string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return Collection<int, Classe>
     */
    public function getClasse(): Collection
    {
        return $this->classe;
    }

    public function addClasse(Classe $classe): self
    {
        if (!$this->classe->contains($classe)) {
            $this->classe->add($classe);
        }

        return $this;
    }

    public function removeClasse(Classe $classe): self
    {
        $this->classe->removeElement($classe);

        return $this;
    }

    /**
     * @return Collection<int, Subject>
     */
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): self
    {
        if (!$this->subjects->contains($subject)) {
            $this->subjects->add($subject);
            $subject->setTeacher($this);
        }

        return $this;
    }

    public function removeSubject(Subject $subject): self
    {
        if ($this->subjects->removeElement($subject)) {
            if ($subject->getTeacher() === $this) {
                $subject->setTeacher(null);
            }
        }

        return $this;
    }
}
