<?php

namespace App\Data;

class StudentFilterData
{
  private $name;
  private $fanampiny;

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
}