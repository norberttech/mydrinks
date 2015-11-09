<?php

namespace MyDrinks\Domain\Recipe\Step;

use MyDrinks\Domain\Recipe\Step;
use MyDrinks\Domain\Recipe\Supply\Capacity;

final class PrepareTheShaker implements Step
{
    /**
     * @var Capacity
     */
    private $capacity;

    public function __construct(Capacity $capacity)
    {
        $this->capacity = $capacity;
    }

    /**
     * @return Capacity
     */
    public function getCapacity()
    {
        return $this->capacity;
    }
}