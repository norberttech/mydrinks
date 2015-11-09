<?php

namespace MyDrinks\Domain\Recipe\Supply;

use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe\Supply;
use MyDrinks\Domain\Recipe\Supply\Capacity;

final class Liquid implements Supply
{
    /**
     * @var Name
     */
    private $name;
    
    /**
     * @var Capacity
     */
    private $capacity;

    /**
     * @param Name $name
     * @param Capacity $capacity
     */
    public function __construct(Name $name, Capacity $capacity)
    {
        $this->name = $name;
        $this->capacity = $capacity;
    }
    
    /**
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Capacity
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @param Capacity $capacity
     * @return Liquid
     */
    public function fill(Capacity $capacity)
    {
        return new Liquid($this->name, $this->capacity->add($capacity)); 
    }
}
