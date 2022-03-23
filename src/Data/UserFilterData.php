<?php

namespace App\Data;

class UserFilterData
{
    private $name;
    private $email;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $nom): self
    {
        $this->name = $nom;

        return $this;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }
}