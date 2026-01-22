<?php

namespace App\Data;

use App\Entity\Classe;
use App\Entity\Location;
use App\Data\ScoreFilterData;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class StudentFilterData
{
    private ?string $firstname = null;
    private ?string $lastname = null;
    private ?string $gender = null;
    private ?Classe $classe = null;
    private ?Location $location = null;
    /**
     * @var Collection<int, ScoreFilterData>
     */
    private Collection $scoreFilters;

    public function __construct()
    {
        $this->scoreFilters = new ArrayCollection();
        $this->addScoreFilter(new ScoreFilterData());
    }
    
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): self
    {
        $this->classe = $classe;
        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;
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

    /**
     * Get scoreFilterData>
     *
     * @return Collection<int, ScoreFilterData>
     */ 
    public function getScoreFilters()
    {
        return $this->scoreFilters;
    }

    public function addScoreFilter(ScoreFilterData $scoreFilter): self
    {
        if (!$this->scoreFilters->contains($scoreFilter)) {
            $this->scoreFilters->add($scoreFilter);
        }
        return $this;
    }
    
    public function removeScoreFilter(ScoreFilterData $scoreFilter): self
    {
        $this->scoreFilters->removeElement($scoreFilter);
        return $this;
    }
}
