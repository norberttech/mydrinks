<?php

namespace MyDrinks\Application;

use MyDrinks\Domain\Recipe;
use MyDrinks\Domain\Recipes as DomainRecipes;

interface Recipes extends DomainRecipes
{
    /**
     * @param string $slug
     * @return null|Recipe
     */
    public function findBySlug($slug);
}