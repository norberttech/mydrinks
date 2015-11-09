<?php

namespace MyDrinks\Application\Recipe;

use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;

interface Storage 
{
    /**
     * @param Recipe $recipe
     */
    public function save(Recipe $recipe);

    /**
     * @param Recipe $recipe
     */
    public function remove(Recipe $recipe);
    
    /**
     * @param Name $name
     * @return bool
     */
    public function hasRecipeWithName(Name $name);

    /**
     * @param Name $name
     * @return Recipe
     */
    public function fetchByName(Name $name);

    /**
     * @param string $slug
     * @return Recipe
     */
    public function fetchBySlug($slug);

    /**
     * @return \Generator
     */
    public function fetchAll();

    /**
     * @return int
     */
    public function count();
}