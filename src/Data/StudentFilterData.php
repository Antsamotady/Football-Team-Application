<?php

namespace App\Data;

class StudentFilterData
{
  private $firstname;
  private $lastname;
  private $gender;
  private $classe;

  /**
   * Get the value of firstname
   */ 
  public function getFirstname()
  {
    return $this->firstname;
  }

  /**
   * Set the value of firstname
   *
   * @return  self
   */ 
  public function setFirstname($firstname)
  {
    $this->firstname = $firstname;

    return $this;
  }

  /**
   * Get the value of lastname
   */ 
  public function getLastname()
  {
    return $this->lastname;
  }

  /**
   * Set the value of lastname
   *
   * @return  self
   */ 
  public function setLastname($lastname)
  {
    $this->lastname = $lastname;

    return $this;
  }

  /**
   * Get the value of classe
   */ 
  public function getClasse()
  {
    return $this->classe;
  }

  /**
   * Set the value of classe
   *
   * @return  self
   */ 
  public function setClasse($classe)
  {
    $this->classe = $classe;

    return $this;
  }

  /**
   * Get the value of gender
   */ 
  public function getGender()
  {
    return $this->gender;
  }

  /**
   * Set the value of gender
   *
   * @return  self
   */ 
  public function setGender($gender)
  {
    $this->gender = $gender;

    return $this;
  }
}