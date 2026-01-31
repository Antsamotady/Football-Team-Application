<?php

namespace App\Entity;

use App\Repository\ConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigurationRepository::class)]
class Configuration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $passingScore = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $AcademicYear = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $deliberationDate = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $InstitutionName = null;

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

    public function getPassingScore(): ?float
    {
        return $this->passingScore;
    }

    public function setPassingScore(?float $passingScore): static
    {
        $this->passingScore = $passingScore;

        return $this;
    }

    public function getAcademicYear(): ?string
    {
        return $this->AcademicYear;
    }

    public function setAcademicYear(?string $AcademicYear): static
    {
        $this->AcademicYear = $AcademicYear;

        return $this;
    }

    public function getDeliberationDate(): ?\DateTime
    {
        return $this->deliberationDate;
    }

    public function setDeliberationDate(?\DateTime $deliberationDate): static
    {
        $this->deliberationDate = $deliberationDate;

        return $this;
    }

    public function getInstitutionName(): ?string
    {
        return $this->InstitutionName;
    }

    public function setInstitutionName(?string $InstitutionName): static
    {
        $this->InstitutionName = $InstitutionName;

        return $this;
    }
}
