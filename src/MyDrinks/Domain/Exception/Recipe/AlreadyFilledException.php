<?php

namespace MyDrinks\Domain\Exception\Recipe;

use MyDrinks\Domain\Name;

class AlreadyFilledException extends StepException
{
    /**
     * @var Name
     */
    private $supply;

    /**
     * @param Name $supply
     */
    public function __construct(Name $supply)
    {
        $this->supply = $supply;
    }

    /**
     * @return Name
     */
    public function getSupply()
    {
        return $this->supply;
    }
}