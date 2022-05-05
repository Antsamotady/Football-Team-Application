<?php

namespace App\Data;

class ClientFilterData
{
    private $name;
    private $cleAbo;
    private $dateFin;
    private $flagActif;
    private $nbTitre;
    private $nbUsers;
    private $nbEntities;
    private $simulation;
    private $limitAnnonce;
    private $dateMin;
    private $dateMax;

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
     * Get the value of cleAbo
     */ 
    public function getCleAbo()
    {
        return $this->cleAbo;
    }

    /**
     * Set the value of cleAbo
     *
     * @return  self
     */ 
    public function setCleAbo($cleAbo)
    {
        $this->cleAbo = $cleAbo;

        return $this;
    }

    /**
     * Get the value of dateFin
     */ 
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set the value of dateFin
     *
     * @return  self
     */ 
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get the value of flagActif
     */ 
    public function getFlagActif()
    {
        return $this->flagActif;
    }

    /**
     * Set the value of flagActif
     *
     * @return  self
     */ 
    public function setFlagActif($flagActif)
    {
        $this->flagActif = $flagActif;

        return $this;
    }

    /**
     * Get the value of nbTitre
     */ 
    public function getNbTitre()
    {
        return $this->nbTitre;
    }

    /**
     * Set the value of nbTitre
     *
     * @return  self
     */ 
    public function setNbTitre($nbTitre)
    {
        $this->nbTitre = $nbTitre;

        return $this;
    }

    /**
     * Get the value of dateMax
     */ 
    public function getDateMax()
    {
        return $this->dateMax;
    }

    /**
     * Set the value of dateMax
     *
     * @return  self
     */ 
    public function setDateMax($dateMax)
    {
        $this->dateMax = $dateMax;

        return $this;
    }

    /**
     * Get the value of dateMin
     */ 
    public function getDateMin()
    {
        return $this->dateMin;
    }

    /**
     * Set the value of dateMin
     *
     * @return  self
     */ 
    public function setDateMin($dateMin)
    {
        $this->dateMin = $dateMin;

        return $this;
    }

    /**
     * Get the value of nbUsers
     */ 
    public function getNbUsers()
    {
        return $this->nbUsers;
    }

    /**
     * Set the value of nbUsers
     *
     * @return  self
     */ 
    public function setNbUsers($nbUsers)
    {
        $this->nbUsers = $nbUsers;

        return $this;
    }

    /**
     * Get the value of nbEntities
     */ 
    public function getNbEntities()
    {
        return $this->nbEntities;
    }

    /**
     * Set the value of nbEntities
     *
     * @return  self
     */ 
    public function setNbEntities($nbEntities)
    {
        $this->nbEntities = $nbEntities;

        return $this;
    }

    /**
     * Get the value of simulation
     */ 
    public function getSimulation()
    {
        return $this->simulation;
    }

    /**
     * Set the value of simulation
     *
     * @return  self
     */ 
    public function setSimulation($simulation)
    {
        $this->simulation = $simulation;

        return $this;
    }

    /**
     * Get the value of limitAnnonce
     */ 
    public function getLimitAnnonce()
    {
        return $this->limitAnnonce;
    }

    /**
     * Set the value of limitAnnonce
     *
     * @return  self
     */ 
    public function setLimitAnnonce($limitAnnonce)
    {
        $this->limitAnnonce = $limitAnnonce;

        return $this;
    }
}