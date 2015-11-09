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
    private $recipeFactor;

    /**
     * @param Recipes $recipes
     * @param Factory $recipeFactor
     */
    public function __construct(Recipes $recipes, Factory $recipeFactor)
    {
        $this->recipes = $recipes;
        $this->recipeFactor = $recipeFactor;
    }

    /**
     * @param CreateNewRecipeCommand $command
     * @throws RecipeAlreadyExistsException
     */
    public function handle(CreateNewRecipeCommand $command)
    {
        $recipe = $this->recipeFactor->createRecipe($command->name);
        
        if ($this->recipes->hasRecipeWithName($recipe->getName())) {
            throw new RecipeAlreadyExistsException();
        }
        
        $this->recipes->add($recipe);
    }
}
