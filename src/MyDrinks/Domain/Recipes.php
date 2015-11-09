<?php

namespace MyDrinks\Domain;

interface Recipes 
{
    /**
     * @param Recipe $recipe
     */
    public function add(Recipe $recipe);

    /**
     * @param Recipe $recipe
     */
    public function remove(Recipe $recipe);

    /**
     * @param Name $name
     * @return null|Recipe
     */
    public function findRecipeByName(Name $name);

    /**
     * @param Name $name
     * @return boolean
     */
    public function hasRecipeWithName(Name $name);
}