<?php

namespace MyDrinks\Domain\Recipe\Supply;

use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe\Supply;
use MyDrinks\Domain\Recipe\Supply\Amount;

final class Ingredient implements Supply
{
    /**
     * @var Name
     */
    private $name;
    
    /**
     * @var Amount
     */
    private $amount;

    /**
     * @param Name $name
     * @param Amount $amount
     */
    public function __construct(Name $name, Amount $amount)
    {
        $this->name = $name;
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
     * @return Amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param Amount $amount
     * @return Ingredient
     */
    public function add(Amount $amount)
    {
        return new Ingredient($this->name, $this->amount->add($amount));
    }
}
