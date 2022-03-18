<?php

namespace App\Entity;

use App\Repository\KeycompteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: KeycompteRepository::class)]
class Keycompte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $token;

    #[ORM\Column(type: 'string', length: 255)]
    private $namecompte;

    #[ORM\Column(type: 'string', length: 255)]
    private $keycompte;

    #[ORM\Column(type: 'datetime')]
    private $dateexpiration;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $limitFamille;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $limitUser;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $limitEntity;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $limitAnnonce;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $limitExtension;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $limitSuivi;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $idAbonnement;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $currency_id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $currency_value;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $delayPrevAutoTask;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $fraisdepotp;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $ratiouse;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $ratiovalue;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $ratiocalculate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getNamecompte(): ?string
    {
        return $this->namecompte;
    }

    public function setNamecompte(string $namecompte): self
    {
        $this->namecompte = $namecompte;

        return $this;
    }

    public function getKeycompte(): ?string
    {
        return $this->keycompte;
    }

    public function setKeycompte(string $keycompte): self
    {
        $this->keycompte = $keycompte;

        return $this;
    }

    public function getDateexpiration(): ?\DateTimeInterface
    {
        return $this->dateexpiration;
    }

    public function setDateexpiration(\DateTimeInterface $dateexpiration): self
    {
        $this->dateexpiration = $dateexpiration;

        return $this;
    }

    public function getLimitFamille(): ?string
    {
        return $this->limitFamille;
    }

    public function setLimitFamille(?string $limitFamille): self
    {
        $this->limitFamille = $limitFamille;

        return $this;
    }

    public function getLimitUser(): ?string
    {
        return $this->limitUser;
    }

    public function setLimitUser(?string $limitUser): self
    {
        $this->limitUser = $limitUser;

        return $this;
    }

    public function getLimitEntity(): ?string
    {
        return $this->limitEntity;
    }

    public function setLimitEntity(?string $limitEntity): self
    {
        $this->limitEntity = $limitEntity;

        return $this;
    }

    public function getLimitAnnonce(): ?string
    {
        return $this->limitAnnonce;
    }

    public function setLimitAnnonce(?string $limitAnnonce): self
    {
        $this->limitAnnonce = $limitAnnonce;

        return $this;
    }

    public function getLimitExtension(): ?string
    {
        return $this->limitExtension;
    }

    public function setLimitExtension(?string $limitExtension): self
    {
        $this->limitExtension = $limitExtension;

        return $this;
    }

    public function getLimitSuivi(): ?string
    {
        return $this->limitSuivi;
    }

    public function setLimitSuivi(?string $limitSuivi): self
    {
        $this->limitSuivi = $limitSuivi;

        return $this;
    }

    public function getIdAbonnement(): ?int
    {
        return $this->idAbonnement;
    }

    public function setIdAbonnement(?int $idAbonnement): self
    {
        $this->idAbonnement = $idAbonnement;

        return $this;
    }

    public function getCurrencyId(): ?int
    {
        return $this->currency_id;
    }

    public function setCurrencyId(?int $currency_id): self
    {
        $this->currency_id = $currency_id;

        return $this;
    }

    public function getCurrencyValue(): ?string
    {
        return $this->currency_value;
    }

    public function setCurrencyValue(string $currency_value): self
    {
        $this->currency_value = $currency_value;

        return $this;
    }

    public function getDelayPrevAutoTask(): ?int
    {
        return $this->delayPrevAutoTask;
    }

    public function setDelayPrevAutoTask(?int $delayPrevAutoTask): self
    {
        $this->delayPrevAutoTask = $delayPrevAutoTask;

        return $this;
    }

    public function getFraisdepotp(): ?string
    {
        return $this->fraisdepotp;
    }

    public function setFraisdepotp(?string $fraisdepotp): self
    {
        $this->fraisdepotp = $fraisdepotp;

        return $this;
    }

    public function getRatiouse(): ?int
    {
        return $this->ratiouse;
    }

    public function setRatiouse(?int $ratiouse): self
    {
        $this->ratiouse = $ratiouse;

        return $this;
    }

    public function getRatiovalue(): ?string
    {
        return $this->ratiovalue;
    }

    public function setRatiovalue(?string $ratiovalue): self
    {
        $this->ratiovalue = $ratiovalue;

        return $this;
    }

    public function getRatiocalculate(): ?string
    {
        return $this->ratiocalculate;
    }

    public function setRatiocalculate(?string $ratiocalculate): self
    {
        $this->ratiocalculate = $ratiocalculate;

        return $this;
    }
}
