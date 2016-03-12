<?php

namespace MyDrinks\Application\Handler;

use MyDrinks\Application\Command\CreateNewRecipeCommand;
use MyDrinks\Application\Recipe\Factory;
use MyDrinks\Domain\Exception\RecipeAlreadyExistsException;
use MyDrinks\Application\Recipes;

class CreateNewRecipeHandler
{
    /**
     * @var Recipes
     */
    private $recipes;
    
    /**
     * @var Factory
     */
    private $recipeFactory;

    /**
     * @param Recipes $recipes
     * @param Factory $recipeFactory
     */
    public function __construct(Recipes $recipes, Factory $recipeFactory)
    {
        $this->recipes = $recipes;
        $this->recipeFactory = $recipeFactory;
    }

    /**
     * @param CreateNewRecipeCommand $command
     * @throws RecipeAlreadyExistsException
     */
    public function handle(CreateNewRecipeCommand $command)
    {
        $recipe = $this->recipeFactory->createRecipe($command->name);
        
        if ($this->recipes->hasRecipeWithName($recipe->getName())) {
            throw new RecipeAlreadyExistsException();
        }
        
        $this->recipes->add($recipe);
    }
}
