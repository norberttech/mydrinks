<?php

namespace MyDrinks\Application\Handler;

use MyDrinks\Application\Command\AddRecipeStepCommand;
use MyDrinks\Application\Exception\Recipe\RecipeNotFoundException;
use MyDrinks\Application\Exception\Recipe\UnknownStepException;
use MyDrinks\Application\Recipe\Actions;
use MyDrinks\Application\Recipes;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe\Supply\Amount;
use MyDrinks\Domain\Recipe\Supply\Capacity;

class AddRecipeStepHandler
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
     * @param AddRecipeStepCommand $command
     * @throws RecipeNotFoundException
     * @throws UnknownStepException
     * @throws \MyDrinks\Domain\Exception\Recipe\GlassCapacityOverflowException
     * @throws \MyDrinks\Domain\Exception\Recipe\MissingGlassException
     */
    public function handle(AddRecipeStepCommand $command)
    {
        $recipe = $this->recipes->findBySlug($command->slug);
        if (is_null($recipe)) {
            throw new RecipeNotFoundException(sprintf("Recipe with slug \"%s\" does not exists.", $command->slug));
        }
        
        switch ($command->type) {
            case Actions::PREPARE_GLASS:
                $amount = is_null($command->amount) ? null : new Amount($command->amount);
                $recipe->prepareTheGlass(new Name($command->name), new Capacity($command->capacity), $amount);
                break;
            case Actions::POUR_INTO_GLASS;
                $recipe->pourIntoGlass(new Name($command->name), new Capacity($command->capacity));
                break;
            case Actions::STRAIN_INTO_GLASS_FROM_SHAKER:
                $recipe->strainIntoGlassFromShaker();
                break;
            case Actions::ADD_INGREDIENT_INTO_GLASS:
                $recipe->addIngredientIntoGlass(new Name($command->name), new Amount($command->amount));
                break;
            case Actions::STIR_GLASS_CONTENT:
                $recipe->stirGlassContent();
                break;
            case Actions::FILL_GLASS:
                $recipe->fillGlassWith(new Name($command->name));
                break;
            case Actions::IGNITE_GLASS_CONTENT:
                $recipe->igniteGlassContent();
                break;
            case Actions::GARNISH_GLASS:
                $recipe->garnishGlass(new Name($command->name));
                break;
            case Actions::EMPTY_GLASS_CONTENT:
                $recipe->emptyTheGlass();
                break;
            case Actions::MUDDLE_GLASS_CONTENT:
                $recipe->muddleContent();
                break;
            case Actions::TOP_UP_GLASS:
                $recipe->topUpGlass(new Name($command->name));
                break;
            
            case Actions::PREPARE_SHAKER:
                $recipe->prepareTheShaker(new Capacity($command->capacity));
                break;
            case Actions::POUR_INTO_SHAKER:
                $recipe->pourIntoShaker(new Name($command->name), new Capacity($command->capacity));
                break;
            case Actions::SHAKE_SHAKER_CONTENT:
                $recipe->shakeShakerContent();
                break;
            case Actions::FILL_SHAKER:
                $recipe->fillShakerWith(new Name($command->name));
                break;
            case Actions::ADD_INGREDIENT_INTO_SHAKER:
                $recipe->addIngredientIntoShaker(new Name($command->name), new Amount($command->amount));
                break;
            default:
                throw new UnknownStepException(sprintf("Unknown step type \"%s\"", $command->type));
        }
    }
}
