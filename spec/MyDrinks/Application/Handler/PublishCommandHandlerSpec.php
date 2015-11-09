<?php

namespace spec\MyDrinks\Application\Handler;

use MyDrinks\Application\Command\PublishRecipeCommand;
use MyDrinks\Application\Exception\Recipe\RecipeAlreadyPublishedException;
use MyDrinks\Application\Exception\Recipe\RecipeNotFoundException;
use MyDrinks\Application\Recipes;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PublishCommandHandlerSpec extends ObjectBehavior
{
    function let(Recipes $recipes)
    {
        $this->beConstructedWith($recipes);
    }

    function it_throws_exception_when_recipe_with_slug_does_not_exists()
    {
        $command = new PublishRecipeCommand();
        $command->slug = 'invalid';

        $this->shouldThrow(RecipeNotFoundException::class)->during("handle", [$command]);
    }

    function it_throws_exception_on_attempt_to_publish_twice(Recipes $recipes)
    {
        $recipe = $this->createRecipe();
        $recipe->publish();
        $recipes->findBySlug("screwdriver")->willReturn($recipe);

        $command = new PublishRecipeCommand();
        $command->slug = 'screwdriver';

        $this->shouldThrow(RecipeAlreadyPublishedException::class)->during("handle", [$command]);
    }
    
    function it_publish_command(Recipes $recipes)
    {
        $recipe = $this->createRecipe();
        $recipes->findBySlug("screwdriver")->willReturn($recipe);

        $command = new PublishRecipeCommand();
        $command->slug = 'screwdriver';

        $this->handle($command);

        if (!$recipe->isPublished()) {
            throw new \RuntimeException("Expected recipe to be published.");
        }
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
