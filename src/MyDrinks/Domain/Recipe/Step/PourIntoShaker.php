<?php

namespace MyDrinks\Domain\Recipe\Step;

use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe\Step;
use MyDrinks\Domain\Recipe\Supply\Capacity;

final class PourIntoShaker implements Step
{
    /**
     * @var Capacity
     */
    private $capacity;
    
    /**
     * @var Name
     */
    private $name;

    /**
     * @param Name $liquid
     * @param Capacity $capacity
     */
    public function __construct(Name $liquid, Capacity $capacity)
    {
        $this->capacity = $capacity;
        $this->name = $liquid;
    }

    /**
     * @return Capacity
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }
}