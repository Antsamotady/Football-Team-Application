<?php

namespace App\Data;

class UserSearchData
{
    /**
     * @var string
     */
    public $q = '';

    private $name;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $nom): self
    {
        $this->name = $nom;

        return $this;
    }
}