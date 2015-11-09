<?php

namespace MyDrinks\Domain\Recipe\Step;

use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe\Step;
use MyDrinks\Domain\Recipe\Supply\Amount;

final class AddIngredientIntoShaker implements Step
{
    /**
     * @var Name
     */
    private $ingredientName;
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
        $this->ingredientName = $name;
        $this->amount = $amount;
    }

    /**
     * @return Amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return Name
     */
    public function getIngredientName()
    {
        return $this->ingredientName;
    }
}