<?php

namespace MyDrinks\Domain\Recipe\Supply;

use MyDrinks\Domain\Exception\InvalidArgumentException;

final class Amount
{
    /**
     * @var int
     */
    private $amount;

    /**
     * @param int $amount
     * @throws InvalidArgumentException
     */
    public function __construct($amount)
    {
        if (!is_integer($amount) || $amount < 0) {
            throw new InvalidArgumentException("Ingredient amount need to be a valid integer value");
        }
        
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->amount;
    }

    /**
     * @param Amount $amount
     * @return Amount
     */
    public function add(Amount $amount)
    {
        return new Amount($this->amount + $amount->getValue());
    }
}
