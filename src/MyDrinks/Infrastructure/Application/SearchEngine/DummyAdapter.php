<?php

namespace MyDrinks\Infrastructure\Application\SearchEngine;

use MyDrinks\Application\SearchEngine;
use MyDrinks\Application\SearchEngine\Criteria;
use MyDrinks\Application\SearchEngine\SearchResultSlice;
use MyDrinks\Domain\Recipe;

final class DummyAdapter implements SearchEngine
{
    /**
     * @param Recipe $recipe
     */
    public function indexRecipe(Recipe $recipe)
    {
    }

    /**
     * @param Recipe $recipe
     */
    public function removeRecipeFromIndex(Recipe $recipe)
    {
    }
    
    /**
     * @param Criteria $criteria
     * @return SearchResultSlice
     */
    public function search(Criteria $criteria)
    {
        return new SearchResultSlice($criteria, [
            new SearchEngine\Result\Recipe("screwdriver", "Screwdriver", "This is screwdriver")
        ], 1);
    }
}