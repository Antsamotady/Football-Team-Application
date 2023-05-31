<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 20)]
    private $name;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $surname;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private $price;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'player')]
    private $team;

    #[ORM\Column(type: 'boolean')]
    private $isAvailableForSale;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->getName() . ' ' . $this->getSurname();
    }

    /**
     * Get the value of price
     */ 
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the value of price
     *
     * @return  self
     */ 
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    public function getIsAvailableForSale(): ?bool
    {
        return $this->isAvailableForSale;
    }

    public function setIsAvailableForSale(bool $isAvailableForSale): self
    {
        $this->isAvailableForSale = $isAvailableForSale;

        return $this;
    }
}
