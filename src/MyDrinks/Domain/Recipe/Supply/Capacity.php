<?php

namespace MyDrinks\Domain\Recipe\Supply;

use MyDrinks\Domain\Exception\InvalidArgumentException;

final class Capacity
{
    /**
     * @var int
     */
    private $milliliters;

    /**
     * @param int $milliliters
     * @throws InvalidArgumentException
     */
    public function __construct($milliliters)
    {
        if (!is_integer($milliliters) || $milliliters < 0) {
            throw new InvalidArgumentException("Liquid capacity need to be a valid integer value");
        }
        
        $this->milliliters = $milliliters;
    }

    /**
     * @return int
     */
    public function getMilliliters()
    {
        return $this->milliliters;
    }

    /**
     * @param Capacity $capacity
     * @return Capacity
     */
    public function add(Capacity $capacity)
    {
        return new Capacity($this->milliliters + $capacity->getMilliliters());
    }

    /**
     * @param Capacity $capacity
     * @return Capacity
     */
    public function subtract(Capacity $capacity)
    {
        return new Capacity($this->milliliters - $capacity->getMilliliters());
    }
}
