<?php

namespace spec\MyDrinks\Application\Handler;

use MyDrinks\Application\Command\RemoveRecipeCommand;
use MyDrinks\Application\Exception\Recipe\RecipeNotFoundException;
use MyDrinks\Application\Recipes;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;
use MyDrinks\Domain\Recipe\Supply\Capacity;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RemoveRecipeHandlerSpec extends ObjectBehavior
{
    function let(Recipes $recipes)
    {
        $this->beConstructedWith($recipes);
    }

    function it_throws_exception_when_recipe_with_slug_does_not_exists()
    {
        $command = new RemoveRecipeCommand();
        $command->slug = 'invalid';

        $this->shouldThrow(RecipeNotFoundException::class)->during("handle", [$command]);
    }
    
    public function it_remove_step_from_recipe(Recipes $recipes)
    {
        $recipe = $this->createRecipe();
        $recipes->findBySlug("screwdriver")->willReturn($recipe);
        $recipes->remove($recipe)->shouldBeCalled();

        $command = new RemoveRecipeCommand();
        $command->slug = 'screwdriver';
        
        $this->handle($command);
    }

    /**
     * @return Recipe
     */
    private function createRecipe()
    {
        $recipe = new Recipe(new Name("Screwdriver"));
        $recipe->prepareTheGlass(new Name("Highball"), new Capacity(350));
        $recipe->pourIntoGlass(new Name("Vodka"), new Capacity(150));
        
        return $recipe;
    }
}
