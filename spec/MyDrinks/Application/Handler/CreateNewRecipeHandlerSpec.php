<?php

namespace spec\MyDrinks\Application\Handler;

use MyDrinks\Application\Command\CreateNewRecipeCommand;
use MyDrinks\Application\Recipe\Factory;
use MyDrinks\Application\Recipes;
use MyDrinks\Domain\Exception\RecipeAlreadyExistsException;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CreateNewRecipeHandlerSpec extends ObjectBehavior
{
    function let(Recipes $recipes, Factory $recipeFactory)
    {
        $recipeFactory->createRecipe("Screwdriver")->willReturn(new Recipe(new Name("Screwdriver")));
        
        $this->beConstructedWith($recipes, $recipeFactory);
    }
    
    function it_adds_new_recipe_into_recipes(Recipes $recipes)
    {
        $recipes->hasRecipeWithName(Argument::type(Name::class))->willReturn(false);
        $recipes->add(Argument::type(Recipe::class))->shouldBeCalled();
        
        $command = new CreateNewRecipeCommand();
        $command->name = "Screwdriver";
        $this->handle($command);
    }
    
    function it_throws_an_exception_when_recipe_with_same_name_already_exists(Recipes $recipes)
    {
        $recipes->hasRecipeWithName(Argument::type(Name::class))->willReturn(true);

        $command = new CreateNewRecipeCommand();
        $command->name = "Screwdriver";
        
        $this->shouldThrow(RecipeAlreadyExistsException::class)->during('handle', [$command]);
    }
}
