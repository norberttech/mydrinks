<?php

namespace MyDrinks\Application\Handler;

use MyDrinks\Application\Command\RemoveRecipeImageCommand;
use MyDrinks\Application\Exception\Recipe\RecipeImageNotFoundException;
use MyDrinks\Application\Exception\Recipe\RecipeNotFoundException;
use MyDrinks\Application\Recipe\ImageStorage;
use MyDrinks\Application\Recipes;

final class RemoveRecipeImageHandler 
{
    /**
     * @var Recipes
     */
    private $recipes;
    
    /**
     * @var ImageStorage
     */
    private $imageStorage;

    /**
     * @param Recipes $recipes
     * @param ImageStorage $imageStorage
     */
    public function __construct(Recipes $recipes, ImageStorage $imageStorage)
    {
        $this->recipes = $recipes;
        $this->imageStorage = $imageStorage;
    }

    /**
     * @param RemoveRecipeImageCommand $command
     * @throws RecipeImageNotFoundException
     * @throws RecipeNotFoundException
     */
    public function handle(RemoveRecipeImageCommand $command)
    {
        $recipe = $this->recipes->findBySlug($command->slug);

        if (is_null($recipe)) {
            throw new RecipeNotFoundException;
        }
        
        if (!$this->imageStorage->hasImageFor($command->slug)) {
            throw new RecipeImageNotFoundException;
        }
        
        $this->imageStorage->removeImageFor($command->slug);
    }
}