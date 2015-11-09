<?php

namespace MyDrinks\Application\Recipe;

use MyDrinks\Domain\Recipe;

interface Factory 
{
    /**
     * @param string $name
     * @return Recipe
     */
    public function createRecipe($name);
}