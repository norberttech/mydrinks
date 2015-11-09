<?php

namespace MyDrinks\Application\Handler;

use MyDrinks\Application\Command\RemoveRecipeStepCommand;
use MyDrinks\Application\Exception\Recipe\RecipeNotFoundException;
use MyDrinks\Application\Recipes;

class RemoveRecipeStepHandler
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
     * @param RemoveRecipeStepCommand $command
     * @throws RecipeNotFoundException
     * @throws \MyDrinks\Domain\Exception\Recipe\MissingGlassException
     */
    public function handle(RemoveRecipeStepCommand $command)
    {
        $recipe = $this->recipes->findBySlug($command->slug);

        if (is_null($recipe)) {
            throw new RecipeNotFoundException(sprintf("Recipe with slug \"%s\" does not exists.", $command->slug));
        }
        
        $recipe->removeStep((int) $command->number);
    }
}
