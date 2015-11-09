<?php

namespace MyDrinks\Domain\Recipe\Step;

use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe\Step;
use MyDrinks\Domain\Recipe\Supply\Amount;
use MyDrinks\Domain\Recipe\Supply\Capacity;

final class PrepareTheGlass implements Step
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
     * @var Amount
     */
    private $amount;

    /**
     * @param Name $name
     * @param Capacity $capacity
     * @param Amount $amount
     */
    public function __construct(Name $name, Capacity $capacity, Amount $amount)
    {
        $this->name = $name;
        $this->capacity = $capacity;
        $this->amount = $amount;
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
     * @return Amount
     */
    public function getAmount()
    {
        return $this->amount;
    }
}