<?php

namespace MyDrinks\Domain\Exception\Recipe;

use MyDrinks\Domain\Recipe\Supply\Capacity;

class NotEnoughLiquidException extends StepException
{
    /**
     * @var Capacity
     */
    private $availableCapacity;

    public function __construct(Capacity $availableCapacity)
    {
        $this->availableCapacity = $availableCapacity;
    }

    /**
     * @return Capacity
     */
    public function getAvailableCapacity()
    {
        return $this->availableCapacity;
    }
}