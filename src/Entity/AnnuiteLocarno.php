<?php

namespace App\Entity;

use App\Repository\AnnuiteLocarnoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnuiteLocarnoRepository::class)]
class AnnuiteLocarno
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(inversedBy: 'annuiteLocarno', targetEntity: Annuite::class, cascade: ['persist', 'remove'])]
    private $annuite;

    #[ORM\ManyToOne(targetEntity: Annuite::class, inversedBy: 'regionCodeLocarnos')]
    private $region;

    #[ORM\Column(type: 'float', nullable: true)]
    private $taxRegister;

    #[ORM\Column(type: 'float', nullable: true)]
    private $taxRenew;

    #[ORM\Column(type: 'float', nullable: true)]
    private $costViewRenew;

    #[ORM\Column(type: 'float', nullable: true)]
    private $costViewRegister;

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

    public function getTaxRenew(): ?float
    {
        return $this->taxRenew;
    }

    public function setTaxRenew(?float $taxRenew): self
    {
        $this->taxRenew = $taxRenew;

        return $this;
    }

    public function getCostViewRenew(): ?float
    {
        return $this->costViewRenew;
    }

    public function setCostViewRenew(?float $costViewRenew): self
    {
        $this->costViewRenew = $costViewRenew;

        return $this;
    }

    public function getCostViewRegister(): ?float
    {
        return $this->costViewRegister;
    }

    public function setCostViewRegister(?float $costViewRegister): self
    {
        $this->costViewRegister = $costViewRegister;

        return $this;
    }

    public function getExport() 
    {
        $result = [];
        $result[] = $this->id;
        $result[] = $this->annuite;
        $result[] = $this->region;
        $result[] = $this->taxRegister;
        $result[] = $this->taxRenew;
        $result[] = $this->costViewRenew;
        $result[] = $this->costViewRegister;

        return $result;
    }

}
