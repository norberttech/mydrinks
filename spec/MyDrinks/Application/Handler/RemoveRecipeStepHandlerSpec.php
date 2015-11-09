<?php

namespace spec\MyDrinks\Application\Handler;

use MyDrinks\Application\Command\RemoveRecipeStepCommand;
use MyDrinks\Application\Exception\Recipe\RecipeNotFoundException;
use MyDrinks\Application\Recipes;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;
use MyDrinks\Domain\Recipe\Supply\Capacity;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RemoveRecipeStepHandlerSpec extends ObjectBehavior
{
    function let(Recipes $recipes)
    {
        $this->beConstructedWith($recipes);
    }

    function it_throws_exception_when_recipe_with_slug_does_not_exists()
    {
        $command = new RemoveRecipeStepCommand();
        $command->slug = 'invalid';

        $this->shouldThrow(RecipeNotFoundException::class)->during("handle", [$command]);
    }
    
    public function it_remove_step_from_recipe(Recipes $recipes)
    {
        $recipe = $this->createRecipe();
        $recipes->findBySlug("screwdriver")->willReturn($recipe);
        
        $command = new RemoveRecipeStepCommand();
        $command->slug = 'screwdriver';
        $command->number = 2;
        
        $this->handle($command);
        
        if (count($recipe->getSteps()) !== 1) {
            throw new \RuntimeException(sprintf("Expected recipe step count is 1, %d given.", count($recipe->getSteps())));
        }
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
