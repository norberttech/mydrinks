<?php

namespace MyDrinks\Infrastructure\UserInterface;

use MyDrinks\Application\Recipe\Description\Tastes;
use MyDrinks\Domain\Recipe;
use MyDrinks\Domain\Recipe\Description\Taste;
use MyDrinks\Domain\Recipe\Step;
use MyDrinks\Application\Recipe\Actions;
use MyDrinks\Infrastructure\Exception\RuntimeException;

final class RecipeConverter 
{
    /**
     * @param Recipe $recipe
     * @return array
     * @throws RuntimeException
     */
    public function toArray(Recipe $recipe)
    {
        return [
            'name' => (string) $recipe->getName(),
            'publicationDate' => $recipe->isPublished() ? $recipe->getPublicationDate()->format('Y-m-d H:i:s') : null,
            'steps' => $this->serializeSteps($recipe),
            'glass' => $recipe->isGlassRequired() ? (string) $recipe->getGlass()->getName() : null,
            'description' => [
                'text' => $recipe->getDescription()->getText(),
                'IBAOfficial' => $recipe->getDescription()->isOfficialIBA(),
                'alcoholContent' => $recipe->getDescription()->getAlcoholContent(),
                'taste' => $this->serializeTaste($recipe->getDescription()->getTaste())
            ]
        ];
    }


    /**
     * @param Recipe $recipe
     * @return array
     * @throws RuntimeException
     * @throws \MyDrinks\Domain\Exception\Recipe\StepException
     */
    private function serializeSteps(Recipe $recipe)
    {
        $data = [];

        $steps = $recipe->getSteps();
        foreach ($steps as $step) {

            switch (get_class($step)) {
                case Step\PrepareTheGlass::class:
                    $data[] = [
                        'type' => Actions::PREPARE_GLASS,
                        'name' => (string) $step->getName(),
                        'capacity' => $step->getCapacity()->getMilliliters(),
                        'amount' => $step->getAmount()->getValue()
                    ];
                    break;
                case Step\PourIntoGlass::class:
                    $data[] = [
                        'type' => Actions::POUR_INTO_GLASS,
                        'name' => (string) $step->getName(),
                        'capacity' => $step->getCapacity()->getMilliliters(),
                    ];
                    break;
                case Step\PrepareTheShaker::class:
                    $data[] = [
                        'type' => Actions::PREPARE_SHAKER,
                        'capacity' => $step->getCapacity()->getMilliliters(),
                    ];
                    break;
                case Step\PourIntoShaker::class:
                    $data[] = [
                        'type' => Actions::POUR_INTO_SHAKER,
                        'name' => (string) $step->getName(),
                        'capacity' => $step->getCapacity()->getMilliliters(),
                    ];
                    break;
                case Step\ShakeShakerContent::class:
                    $data[] = [
                        'type' => Actions::SHAKE_SHAKER_CONTENT,
                    ];
                    break;
                case Step\StrainIntoGlassFromShaker::class:
                    $data[] = [
                        'type' => Actions::STRAIN_INTO_GLASS_FROM_SHAKER,
                    ];
                    break;
                case Step\FillGlass::class:
                    $data[] = [
                        'type' => Actions::FILL_GLASS,
                        'name' => (string) $step->getContentName(),
                    ];
                    break;
                case Step\FillShaker::class:
                    $data[] = [
                        'type' => Actions::FILL_SHAKER,
                        'name' => (string) $step->getContentName(),
                    ];
                    break;
                case Step\AddIngredientIntoGlass::class:
                    $data[] = [
                        'type' => Actions::ADD_INGREDIENT_INTO_GLASS,
                        'name' => (string) $step->getIngredientName(),
                        'amount' => $step->getAmount()->getValue()
                    ];
                    break;
                case Step\AddIngredientIntoShaker::class:
                    $data[] = [
                        'type' => Actions::ADD_INGREDIENT_INTO_SHAKER,
                        'name' => (string) $step->getIngredientName(),
                        'amount' => $step->getAmount()->getValue()
                    ];
                    break;
                case Step\EmptyTheGlass::class:
                    $data[] = [
                        'type' => Actions::EMPTY_GLASS_CONTENT,
                    ];
                    break;
                case Step\TopUpGlass::class:
                    $data[] = [
                        'type' => Actions::TOP_UP_GLASS,
                        'name' => (string) $step->getName()
                    ];
                    break;
                case Step\StirGlassContent::class:
                    $data[] = [
                        'type' => Actions::STIR_GLASS_CONTENT,
                    ];
                    break;
                case Step\IgniteGlassContent::class:
                    $data[] = [
                        'type' => Actions::IGNITE_GLASS_CONTENT,
                    ];
                    break;
                case Step\GarnishGlass::class:
                    $data[] = [
                        'type' => Actions::GARNISH_GLASS,
                        'name' => (string) $step->getDecorationName()
                    ];
                    break;
                case Step\MuddleGlassContent::class:
                    $data[] = [
                        'type' => Actions::MUDDLE_GLASS_CONTENT,
                    ];
                    break;
                default:
                    throw new RuntimeException(sprintf("Unknown step class \"%s\"", get_class($step)));
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    private function serializeTaste(Taste $taste)
    {
        $tastes = [];
        if ($taste->isSweet()) {
            $tastes[] = Tastes::SWEET;
        }
        
        if ($taste->isBitter()) {
            $tastes[] = Tastes::BITTER;
        }
        
        if ($taste->isSour()) {
            $tastes[] = Tastes::SOUR;
        }
        
        if ($taste->isSpicy()) {
            $tastes[] = Tastes::SPICY;
        }
        
        if ($taste->isSalty()) {
            $tastes[] = Tastes::SALTY;
        }

        return $tastes;
    }
}