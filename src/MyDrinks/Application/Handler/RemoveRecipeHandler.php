<?php

namespace MyDrinks\Application\Handler;

use MyDrinks\Application\Command\RemoveRecipeCommand;
use MyDrinks\Application\Exception\Recipe\RecipeNotFoundException;
use MyDrinks\Application\Recipes;

class RemoveRecipeHandler
{
    /**
     * @var Recipes
     */
    private $recipes;

    /**
     * @param Recipes $recipes
     */
    public function __construct(Recipes $recipes)
    {
        $this->recipes = $recipes;
    }

    /**
     * @param RemoveRecipeCommand $command
     * @throws RecipeNotFoundException
     */
    public function handle(RemoveRecipeCommand $command)
    {
        $recipe = $this->recipes->findBySlug($command->slug);

        if (is_null($recipe)) {
            throw new RecipeNotFoundException(sprintf("Recipe with slug \"%s\" does not exists.", $command->slug));
        }

        $this->recipes->remove($recipe);   
    }
}
