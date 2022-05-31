<?php

namespace App\Entity;

use App\Repository\LoginErrorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoginErrorRepository::class)]
class LoginError
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    private $email;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $timestamp;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $toBeBlocked;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbFailure;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTimestamp(): ?string
    {
        return $this->timestamp;
    }

    public function setTimestamp(?string $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getToBeBlocked(): ?bool
    {
        return $this->toBeBlocked;
    }

    public function setToBeBlocked(bool $toBeBlocked): self
    {
        $this->toBeBlocked = $toBeBlocked;

        return $this;
    }

    public function getNbFailure(): ?int
    {
        return $this->nbFailure;
    }

    public function setNbFailure(?int $nbFailure): self
    {
        $this->nbFailure = $nbFailure;

        return $this;
    }
}
