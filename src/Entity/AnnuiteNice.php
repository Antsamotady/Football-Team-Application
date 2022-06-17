<?php

namespace App\Entity;

use App\Repository\AnnuiteNiceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnuiteNiceRepository::class)]
class AnnuiteNice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(inversedBy: 'annuiteNice', targetEntity: Annuite::class)]
    private $annuite;

    #[ORM\ManyToOne(targetEntity: Annuite::class, inversedBy: 'regionCodeNices')]
    private $region;

    #[ORM\Column(type: 'float', nullable: true)]
    private $taxRegister;

    #[ORM\Column(type: 'float', nullable: true)]
    private $costClassRegister;

    #[ORM\Column(type: 'float', nullable: true)]
    private $taxRenew;

    #[ORM\Column(type: 'float', nullable: true)]
    private $costClassRenew;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnnuite(): ?Annuite
    {
        return $this->annuite;
    }

    public function setAnnuite(?Annuite $annuite): self
    {
        $this->annuite = $annuite;

        return $this;
    }

    public function getRegion(): ?Annuite
    {
        return $this->region;
    }

    public function setRegion(?Annuite $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getTaxRegister(): ?float
    {
        return $this->taxRegister;
    }

    public function setTaxRegister(?float $taxRegister): self
    {
        $this->taxRegister = $taxRegister;

        return $this;
    }

    public function getCostClassRegister(): ?float
    {
        return $this->costClassRegister;
    }

    public function setCostClassRegister(?float $costClassRegister): self
    {
        $this->costClassRegister = $costClassRegister;

        return $this;
    }

    public function getTaxRenew(): ?float
    {
        return $this->taxRenew;
    }

    public function setTaxRenew(?float $taxRenew): self
    {
        $this->taxRenew = $taxRenew;

        return $this;
    }

    public function getCostClassRenew(): ?float
    {
        return $this->costClassRenew;
    }

    public function setCostClassRenew(float $costClassRenew): self
    {
        $this->costClassRenew = $costClassRenew;

        return $this;
    }

    public function getExport() 
    {
        $result = [];
        // $result[] = $this->id;
        $result[] = $this->annuite->getName();
        $result[] = $this->region ? $this->region->getPays() : '';
        $result[] = $this->taxRegister;
        $result[] = $this->taxRenew;
        $result[] = $this->costClassRenew;
        $result[] = $this->costClassRegister;

        return $result;
    }

}
