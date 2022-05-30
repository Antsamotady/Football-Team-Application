<?php

namespace App\Entity;

use App\Repository\AbonnementRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AbonnementRepository::class)]
class Abonnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 64)]
    private $nomClient;

    #[ORM\Column(type: 'string', length: 255)]
    private $cleAbo;

    #[ORM\Column(type: 'datetime')]
    private $dateFin;

    #[ORM\Column(type: 'boolean')]
    private $flagActif;

    /**
     * Doit être un nombre positif ou null
     *
     * @Assert\PositiveOrZero(message="Nombre positif ou null")
     */
    #[ORM\Column(type: 'integer')]
    private $nbTitres;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbUsers;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbEntities;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $simulation;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $limitAnnonce;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomClient(): ?string
    {
        return $this->nomClient;
    }

    public function setNomClient(string $nomClient): self
    {
        $this->nomClient = $nomClient;

        return $this;
    }

    public function getCleAbo(): ?string
    {
        return $this->cleAbo;
    }

    public function setCleAbo(string $cleAbo): self
    {
        $this->cleAbo = $cleAbo;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getFlagActif(): ?bool
    {
        return $this->flagActif;
    }

    public function setFlagActif(bool $flagActif): self
    {
        $this->flagActif = $flagActif;

        return $this;
    }

    public function getNbTitres(): ?int
    {
        return $this->nbTitres;
    }

    public function setNbTitres(int $nbTitres): self
    {
        $this->nbTitres = $nbTitres;

        return $this;
    }

    public function getNbUsers(): ?int
    {
        return $this->nbUsers;
    }

    public function setNbUsers(int $nbUsers): self
    {
        $this->nbUsers = $nbUsers;

        return $this;
    }

    public function getNbEntities(): ?int
    {
        return $this->nbEntities;
    }

    public function setNbEntities(?int $nbEntities): self
    {
        $this->nbEntities = $nbEntities;

        return $this;
    }

    public function getSimulation(): ?bool
    {
        return $this->simulation;
    }

    public function setSimulation(bool $simulation): self
    {
        $this->simulation = $simulation;

        return $this;
    }

    public function getLimitAnnonce(): ?int
    {
        return $this->limitAnnonce;
    }

    public function setLimitAnnonce(?int $limitAnnonce): self
    {
        $this->limitAnnonce = $limitAnnonce;

        return $this;
    }

    public function getExport() 
    {
        $result = [];
        $result[] = $this->nomClient;
        $result[] = $this->flagActif;
        $result[] = $this->cleAbo;
        $result[] = $this->nbTitres;
        $result[] = $this->getDateFin()->format('d-m-Y');
        $result[] = $this->nbUsers;
        $result[] = $this->nbEntities;
        $result[] = $this->simulation;
        $result[] = $this->limitAnnonce;

        return $result;
    }
    
    public function __toString()
    {
        return $this->nomClient;
    }

}
