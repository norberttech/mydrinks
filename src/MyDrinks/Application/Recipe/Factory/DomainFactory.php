<?php

namespace MyDrinks\Application\Recipe\Factory;

use MyDrinks\Application\Recipe\Factory;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;

class DomainFactory implements Factory
{
    /**
     * @param string $name
     * @return Recipe
     */
    public function createRecipe($name)
    {
        return new Recipe(new Name($name));
    }
}