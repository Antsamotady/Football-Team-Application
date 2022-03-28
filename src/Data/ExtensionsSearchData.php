<?php

namespace App\Data;

class ExtensionsSearchData
{
    /**
     * @var string
     */
    private $nom;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $s): self
    {
        $this->nom = $s;

        return $this;
    }
}