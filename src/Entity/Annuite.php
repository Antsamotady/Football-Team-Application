<?php

namespace App\Entity;

use App\Repository\AnnuiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnuiteRepository::class)]
class Annuite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $pays;

    #[ORM\Column(type: 'string', length: 2, nullable: true)]
    private $codePays;

    #[ORM\Column(type: 'string', length: 255)]
    private $periode;

    #[ORM\Column(type: 'string', length: 255)]
    private $montants;

    #[ORM\Column(type: 'string', length: 255)]
    private $region;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $nameen;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $actif;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $isdeleted;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $taxe;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $type;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $idBackend;

    #[ORM\OneToOne(mappedBy: 'annuite', targetEntity: AnnuiteNice::class, cascade: ['persist', 'remove'])]
    private $annuiteNice;

    #[ORM\OneToMany(mappedBy: 'region', targetEntity: AnnuiteNice::class)]
    private $regionCodeNices;

    #[ORM\OneToOne(mappedBy: 'annuite', targetEntity: AnnuiteLocarno::class, orphanRemoval: true)]
    private $annuiteLocarno;

    #[ORM\OneToMany(mappedBy: 'region', targetEntity: AnnuiteLocarno::class)]
    private $regionCodeLocarnos;

    public function __construct()
    {
        $this->childs = new ArrayCollection();
        $this->regionCodeNices = new ArrayCollection();
        $this->regionCodeLocarnos = new ArrayCollection();
    }

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
        // $result[] = $this->id;
        $result[] = $this->name;
        $result[] = $this->pays;
        $result[] = $this->periode;
        $result[] = $this->montants;
        $result[] = $this->region;
        $result[] = $this->nameen;
        $result[] = $this->isdeleted;
        $result[] = $this->taxe;
        $result[] = $this->type;

        return $result;
    }

    public function __toString()
    {
        return $this->pays;
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

    public function getNameen(): ?string
    {
        return $this->nameen;
    }

    public function setNameen(?string $nameen): self
    {
        $this->nameen = $nameen;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(?bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getIsdeleted(): ?bool
    {
        return $this->isdeleted;
    }

    public function setIsdeleted(?bool $isdeleted): self
    {
        $this->isdeleted = $isdeleted;

        return $this;
    }

    public function getTaxe(): ?string
    {
        return $this->taxe;
    }

    public function setTaxe(?string $taxe): self
    {
        $this->taxe = $taxe;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getIdBackend(): ?string
    {
        return $this->idBackend;
    }

    public function setIdBackend(?string $idBackend): self
    {
        $this->idBackend = $idBackend;

        return $this;
    }

    public function getAnnuiteNice(): ?self
    {
        return $this->annuiteNice;
    }

    public function setAnnuiteNice(?self $annuiteNice): self
    {
        $this->annuiteNice = $annuiteNice;

        return $this;
    }

    /**
     * @return Collection|AnnuiteNice[]
     */
    public function getRegionCodeNices(): Collection
    {
        return $this->regionCodeNices;
    }

    public function addRegionCodeNice(AnnuiteNice $regionCodeNice): self
    {
        if (!$this->regionCodeNices->contains($regionCodeNice)) {
            $this->regionCodeNices[] = $regionCodeNice;
            $regionCodeNice->setRegion($this);
        }

        return $this;
    }

    public function removeRegionCodeNice(AnnuiteNice $regionCodeNice): self
    {
        if ($this->regionCodeNices->removeElement($regionCodeNice)) {
            // set the owning side to null (unless already changed)
            if ($regionCodeNice->getRegion() === $this) {
                $regionCodeNice->setRegion(null);
            }
        }

        return $this;
    }

    public function getAnnuiteLocarno(): ?AnnuiteLocarno
    {
        return $this->annuiteLocarno;
    }

    public function setAnnuiteLocarno(?AnnuiteLocarno $annuiteLocarno): self
    {
        // unset the owning side of the relation if necessary
        if ($annuiteLocarno === null && $this->annuiteLocarno !== null) {
            $this->annuiteLocarno->setAnnuite(null);
        }

        // set the owning side of the relation if necessary
        if ($annuiteLocarno !== null && $annuiteLocarno->getAnnuite() !== $this) {
            $annuiteLocarno->setAnnuite($this);
        }

        $this->annuiteLocarno = $annuiteLocarno;

        return $this;
    }

    /**
     * @return Collection|AnnuiteLocarno[]
     */
    public function getRegionCodeLocarnos(): Collection
    {
        return $this->regionCodeLocarnos;
    }

    public function addRegionCodeLocarno(AnnuiteLocarno $regionCodeLocarno): self
    {
        if (!$this->regionCodeLocarnos->contains($regionCodeLocarno)) {
            $this->regionCodeLocarnos[] = $regionCodeLocarno;
            $regionCodeLocarno->setRegion($this);
        }

        return $this;
    }

    public function removeRegionCodeLocarno(AnnuiteLocarno $regionCodeLocarno): self
    {
        if ($this->regionCodeLocarnos->removeElement($regionCodeLocarno)) {
            // set the owning side to null (unless already changed)
            if ($regionCodeLocarno->getRegion() === $this) {
                $regionCodeLocarno->setRegion(null);
            }
        }

        return $this;
    }

}
