<?php

namespace MyDrinks\Domain\Recipe\Step;

use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe\Step;
use MyDrinks\Domain\Recipe\Supply\Capacity;

final class TopUpGlass implements Step
{
    /**
     * @var
     */
    private $name;
    
    /**
     * @var
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
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getCapacity()
    {
        return $this->capacity;
    }
}