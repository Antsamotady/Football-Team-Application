<?php

namespace App\Data;

use App\Entity\Classe;
use App\Entity\Location;

class ClasseFilterData
{
    private ?string $name = null;
    private ?Location $location = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;
        return $this;
    }
}
