<?php

namespace MyDrinks\Application\Handler;

use MyDrinks\Application\Command\UploadRecipeImageCommand;
use MyDrinks\Application\Exception\Recipe\RecipeNotFoundException;
use MyDrinks\Application\Exception\Recipe\RuntimeException;
use MyDrinks\Application\Filesystem;
use MyDrinks\Application\Recipe\Image;
use MyDrinks\Application\Recipe\ImageStorage;
use MyDrinks\Application\Recipes;

final class UploadRecipeImageHandler 
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
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Recipes $recipes
     * @param ImageStorage $imageStorage
     * @param Filesystem $filesystem
     */
    public function __construct(Recipes $recipes, ImageStorage $imageStorage, Filesystem $filesystem)
    {
        $this->recipes = $recipes;
        $this->imageStorage = $imageStorage;
        $this->filesystem = $filesystem;
    }

    /**
     * @param UploadRecipeImageCommand $command
     * @throws RecipeNotFoundException
     * @throws RuntimeException
     */
    public function handle(UploadRecipeImageCommand $command)
    {
        $recipe = $this->recipes->findBySlug($command->slug);

        if (is_null($recipe)) {
            throw new RecipeNotFoundException;
        }
        
        if (!$command->image instanceof \SplFileInfo) {
            throw new RuntimeException("UploadRecipeImageHandler expected image to implement \\SplFileInfo");
        }
        
        $image = new Image(
            $command->slug . '.' . $command->extension, 
            $this->filesystem->read($command->image->getRealPath())
        );
     
        $this->imageStorage->saveImageFor($image, $command->slug);
    }
}