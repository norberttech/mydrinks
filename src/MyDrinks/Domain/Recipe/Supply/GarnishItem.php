<?php

namespace MyDrinks\Domain\Recipe\Supply;

use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe\Supply;
use MyDrinks\Domain\Recipe\Supply\Amount;

final class GarnishItem implements Supply
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
}
