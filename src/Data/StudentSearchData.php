<?php

namespace App\Data;

class StudentSearchData
{
    /**
     * @var string
     */
    private $name;

    public function getName(): ?string
    {
        return $this->name;
    }


    /**
     * Set the value of name
     *
     * @param  string  $name
     *
     * @return  self
     */ 
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }
}