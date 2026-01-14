<?php

namespace App\Data;

use App\Entity\Classe;

class StudentFilterData
{
    private ?string $firstname = null;
    private ?string $lastname = null;
    private ?string $gender = null;
    private ?Classe $classe = null;

    // -------------------------
    // Firstname
    // -------------------------
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;
        return $this;
    }

    // -------------------------
    // Lastname
    // -------------------------
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;
        return $this;
    }

    // -------------------------
    // Classe
    // -------------------------
    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): self
    {
        $this->classe = $classe;
        return $this;
    }

    // -------------------------
    // Gender
    // -------------------------
    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;
        return $this;
    }
}
