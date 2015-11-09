<?php

namespace MyDrinks\Application;

use MyDrinks\Application\SearchEngine\SearchResultSlice;
use MyDrinks\Domain\Recipe;
use MyDrinks\Application\SearchEngine\Criteria;

interface SearchEngine
{
    /**
     * @param Recipe $recipe
     */
    public function indexRecipe(Recipe $recipe);

    /**
     * @param Recipe $recipe
     */
    public function removeRecipeFromIndex(Recipe $recipe);
    
    /**
     * @param Criteria $criteria
     * @return SearchResultSlice
     */
    public function search(Criteria $criteria);
}