<?php

namespace App\Entity;

use App\Repository\ExtensionsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExtensionsRepository::class)]
class Extensions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $pays;

    #[ORM\Column(type: 'string', length: 2)]
    private $codePays;

    #[ORM\Column(type: 'string', length: 255)]
    private $periode;

    #[ORM\Column(type: 'string', length: 255)]
    private $montants;

    #[ORM\Column(type: 'string', length: 255)]
    private $region;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getCodePays(): ?string
    {
        return $this->codePays;
    }

    public function setCodePays(string $codePays): self
    {
        $this->codePays = $codePays;

        return $this;
    }

    public function getPeriode(): ?string
    {
        return $this->periode;
    }

    public function setPeriode(string $periode): self
    {
        $this->periode = $periode;

        return $this;
    }

    public function getMontants(): ?string
    {
        return $this->montants;
    }

    public function setMontants(string $montants): self
    {
        $this->montants = $montants;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getExport() 
    {
        $result = [];
        $result[] = $this->pays;
        $result[] = $this->periode;
        $result[] = $this->montants;
        $result[] = $this->region;

        return $result;
    }

    public function __toString()
    {
        return $this->pays;
    }

}
