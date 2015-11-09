<?php

namespace spec\MyDrinks\Application\Handler;

use MyDrinks\Application\Command\RemoveRecipeImageCommand;
use MyDrinks\Application\Exception\Recipe\RecipeNotFoundException;
use MyDrinks\Application\Recipe\ImageStorage;
use MyDrinks\Application\Recipes;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RemoveRecipeImageHandlerSpec extends ObjectBehavior
{
    function let(Recipes $recipes, ImageStorage $imageStorage)
    {
        $this->beConstructedWith($recipes, $imageStorage);
    }
    
    function it_throws_exception_when_recipe_with_slug_does_not_exists()
    {
        $command = new RemoveRecipeImageCommand();
        $command->slug = 'invalid';

        $this->shouldThrow(RecipeNotFoundException::class)->during("handle", [$command]);
    }
    
    function it_updates_recipe_description(Recipes $recipes, ImageStorage $imageStorage)
    {
        $recipes->findBySlug("screwdriver")->willReturn($this->createRecipe());
        $command = new RemoveRecipeImageCommand();
        $command->slug = 'screwdriver';

        $imageStorage->hasImageFor("screwdriver")->willReturn(true);
        $imageStorage->removeImageFor("screwdriver")->shouldBeCalled();

        $this->handle($command);
    }

    /**
     * @return Recipe
     */
    private function createRecipe()
    {
        $recipe = new Recipe(new Name("Screwdriver"));

        return $recipe;
    }
}
