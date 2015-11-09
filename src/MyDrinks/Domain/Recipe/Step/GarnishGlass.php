<?php

namespace MyDrinks\Domain\Recipe\Step;

use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe\Step;

final class GarnishGlass implements Step
{
    /**
     * @var Name
     */
    private $decorationName;

    /**
     * @param Name $name
     */
    public function __construct(Name $name)
    {
        $this->decorationName = $name;
    }

    /**
     * @return Name
     */
    public function getDecorationName()
    {
        return $this->decorationName;
    }
}