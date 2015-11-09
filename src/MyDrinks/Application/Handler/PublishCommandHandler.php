<?php

namespace MyDrinks\Application\Handler;

use MyDrinks\Application\Command\PublishRecipeCommand;
use MyDrinks\Application\Exception\Recipe\RecipeAlreadyPublishedException;
use MyDrinks\Application\Exception\Recipe\RecipeNotFoundException;
use MyDrinks\Application\Recipes;

class PublishCommandHandler
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
     * @param PublishRecipeCommand $command
     * @throws RecipeAlreadyPublishedException
     * @throws RecipeNotFoundException
     */
    public function handle(PublishRecipeCommand $command)
    {
        $recipe = $this->recipes->findBySlug($command->slug);

        if (is_null($recipe)) {
            throw new RecipeNotFoundException(sprintf("Recipe with slug \"%s\" does not exists.", $command->slug));
        }
        
        if ($recipe->isPublished()) {
            throw new RecipeAlreadyPublishedException;
        }
        
        $recipe->publish();
    }
}
