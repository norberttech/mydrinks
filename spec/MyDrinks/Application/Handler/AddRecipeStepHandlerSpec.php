<?php

namespace spec\MyDrinks\Application\Handler;

use MyDrinks\Application\Command\AddRecipeStepCommand;
use MyDrinks\Application\Exception\Recipe\RecipeNotFoundException;
use MyDrinks\Application\Recipes;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;
use MyDrinks\Domain\Recipe\Step;
use MyDrinks\Application\Recipe\Actions;
use MyDrinks\Domain\Recipe\Supply\Amount;
use MyDrinks\Domain\Recipe\Supply\Capacity;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use MyDrinks\Application\Exception\Recipe\UnknownStepException;

class AddRecipeStepHandlerSpec extends ObjectBehavior
{
    function let(Recipes $recipes, Recipe $recipe)
    {
        $recipes->findBySlug("invalid")->willReturn(null);
        $recipes->findBySlug("screwdriver")->willReturn($recipe);
        $this->beConstructedWith($recipes);
    }
    
    function it_throws_exception_when_recipe_with_slug_does_not_exists()
    {
        $command = new AddRecipeStepCommand();
        $command->slug = 'invalid';

        $this->shouldThrow(RecipeNotFoundException::class)->during("handle", [$command]);
    }
    
    function it_throws_exception_when_step_type_is_unknown()
    {
        $command = new AddRecipeStepCommand();
        $command->slug = 'screwdriver';
        $command->type = 'invalid_type';
        
        $this->shouldThrow(UnknownStepException::class)->during("handle", [$command]);
    }
    
    function it_add_prepare_the_glass_step_to_recipe(Recipe $recipe)
    {
        $command = new AddRecipeStepCommand();
        $command->slug = 'screwdriver';
        $command->type = Actions::PREPARE_GLASS;
        $command->name = 'Highball';
        $command->capacity = 150;
        $command->amount = 1;

        $recipe->prepareTheGlass(
                Argument::type(Name::class),
                Argument::type(Capacity::class),
                Argument::type(Amount::class)
            )->shouldBeCalled();

        $this->handle($command);
    }

    function it_add_pour_into_glass_step_to_recipe(Recipe $recipe)
    {
        $command = new AddRecipeStepCommand();
        $command->slug = 'screwdriver';
        $command->type = Actions::POUR_INTO_GLASS;
        $command->name = 'vodka';
        $command->capacity = 150;

        $recipe->pourIntoGlass(
            Argument::type(Name::class),
            Argument::type(Capacity::class)
        )->shouldBeCalled();

        $this->handle($command);
    }

    function it_add_pour_into_glass_from_shaker_step_to_recipe(Recipe $recipe)
    {
        $command = new AddRecipeStepCommand();
        $command->slug = 'screwdriver';
        $command->type = Actions::STRAIN_INTO_GLASS_FROM_SHAKER;

        $recipe->strainIntoGlassFromShaker()->shouldBeCalled();

        $this->handle($command);
    }

    function it_add_add_ingredient_into_glass_step_to_recipe(Recipe $recipe)
    {
        $command = new AddRecipeStepCommand();
        $command->slug = 'screwdriver';
        $command->type = Actions::ADD_INGREDIENT_INTO_GLASS;
        $command->name = 'carambola';
        $command->amount = 1;

        $recipe->addIngredientIntoGlass(
            Argument::type(Name::class),
            Argument::type(Amount::class)
        )->shouldBeCalled();

        $this->handle($command);
    }

    function it_add_stir_glass_content_step_to_recipe(Recipe $recipe)
    {
        $command = new AddRecipeStepCommand();
        $command->slug = 'screwdriver';
        $command->type = Actions::STIR_GLASS_CONTENT;

        $recipe->stirGlassContent()->shouldBeCalled();

        $this->handle($command);
    }

    function it_add_fill_glass_with_step_to_recipe(Recipe $recipe)
    {
        $command = new AddRecipeStepCommand();
        $command->slug = 'screwdriver';
        $command->type = Actions::FILL_GLASS;
        $command->name = "ice";

        $recipe->fillGlassWith(Argument::type(Name::class))->shouldBeCalled();

        $this->handle($command);
    }

    function it_add_ignite_glass_content_step_to_recipe(Recipe $recipe)
    {
        $command = new AddRecipeStepCommand();
        $command->slug = 'screwdriver';
        $command->type = Actions::IGNITE_GLASS_CONTENT;

        $recipe->igniteGlassContent()->shouldBeCalled();

        $this->handle($command);
    }

    function it_add_garnish_glass_step_to_recipe(Recipe $recipe)
    {
        $command = new AddRecipeStepCommand();
        $command->slug = 'screwdriver';
        $command->type = Actions::GARNISH_GLASS;
        $command->name = "orange slice";

        $recipe->garnishGlass(Argument::type(Name::class))->shouldBeCalled();

        $this->handle($command);
    }

    function it_add_empty_glass_content_step_to_recipe(Recipe $recipe)
    {
        $command = new AddRecipeStepCommand();
        $command->slug = 'screwdriver';
        $command->type = Actions::EMPTY_GLASS_CONTENT;

        $recipe->emptyTheGlass()->shouldBeCalled();

        $this->handle($command);
    }

    function it_add_top_up_glass_step_to_recipe(Recipe $recipe)
    {
        $command = new AddRecipeStepCommand();
        $command->slug = 'screwdriver';
        $command->name = 'vodka';
        $command->type = Actions::TOP_UP_GLASS;

        $recipe->topUpGlass(Argument::type(Name::class))->shouldBeCalled();

        $this->handle($command);
    }

    function it_add_muddle_glass_content_step_to_recipe(Recipe $recipe)
    {
        $command = new AddRecipeStepCommand();
        $command->slug = 'screwdriver';
        $command->type = Actions::MUDDLE_GLASS_CONTENT;

        $recipe->muddleContent()->shouldBeCalled();

        $this->handle($command);
    }
    
    function it_add_prepare_shaker_step_to_recipe(Recipe $recipe)
    {
        $command = new AddRecipeStepCommand();
        $command->slug = 'screwdriver';
        $command->type = Actions::PREPARE_SHAKER;
        $command->capacity = 350;

        $recipe->prepareTheShaker(Argument::type(Capacity::class))->shouldBeCalled();

        $this->handle($command);
    }

    function it_add_pour_into_shaker_step_to_recipe(Recipe $recipe)
    {
        $command = new AddRecipeStepCommand();
        $command->slug = 'screwdriver';
        $command->type = Actions::POUR_INTO_SHAKER;
        $command->capacity = 350;
        $command->name = 'vodka';

        $recipe->pourIntoShaker(Argument::type(Name::class), Argument::type(Capacity::class))->shouldBeCalled();

        $this->handle($command);
    }

    function it_add_shake_shaker_content_step_to_recipe(Recipe $recipe)
    {
        $command = new AddRecipeStepCommand();
        $command->slug = 'screwdriver';
        $command->type = Actions::SHAKE_SHAKER_CONTENT;

        $recipe->shakeShakerContent()->shouldBeCalled();

        $this->handle($command);
    }

    function it_add_fill_shaker_step_to_recipe(Recipe $recipe)
    {
        $command = new AddRecipeStepCommand();
        $command->slug = 'screwdriver';
        $command->type = Actions::FILL_SHAKER;
        $command->name = 'ice';

        $recipe->fillShakerWith(Argument::type(Name::class))->shouldBeCalled();

        $this->handle($command);
    }

    function it_add_add_ingredient_into_shaker_step_to_recipe(Recipe $recipe)
    {
        $command = new AddRecipeStepCommand();
        $command->slug = 'screwdriver';
        $command->type = Actions::ADD_INGREDIENT_INTO_SHAKER;
        $command->name = 'ice';
        $command->amount = 5;

        $recipe->addIngredientIntoShaker(Argument::type(Name::class), Argument::type(Amount::class))
            ->shouldBeCalled();

        $this->handle($command);
    }
}
