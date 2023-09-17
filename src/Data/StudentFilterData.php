<?php

namespace App\Data;

class StudentFilterData
{
  private $name;
  private $fanampiny;
  private $classe;

  /**
   * Get the value of name
   */ 
  public function getName()
  {
    return $this->name;
  }

  /**
   * Set the value of name
   *
   * @return  self
   */ 
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get the value of fanampiny
   */ 
  public function getFanampiny()
  {
    return $this->fanampiny;
  }

  /**
   * Set the value of fanampiny
   *
   * @return  self
   */ 
  public function setFanampiny($fanampiny)
  {
    $this->fanampiny = $fanampiny;

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
}