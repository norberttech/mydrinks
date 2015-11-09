<?php

namespace MyDrinks\Domain\Recipe\Step;

use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe\Step;
use MyDrinks\Domain\Recipe\Supply\Capacity;

final class PourIntoGlass implements Step
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
     * @param Name $liquid
     * @param Capacity $capacity
     */
    public function __construct(Name $liquid, Capacity $capacity)
    {
        $this->name = $liquid;
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
}