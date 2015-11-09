<?php

namespace MyDrinks\Infrastructure\UserInterface;

use MyDrinks\Application\Recipe\Description\TasteBuilder;
use MyDrinks\Application\Recipe\Description\Tastes;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe as DomainRecipe;
use MyDrinks\Domain\Recipe\Step;
use MyDrinks\Application\Recipe\Actions;
use MyDrinks\Domain\Recipe\Supply\Amount;
use MyDrinks\Domain\Recipe\Supply\Capacity;
use MyDrinks\Infrastructure\Exception\RuntimeException;

final class RecipeHydrator 
{
    /**
     * @param array $data
     * @return DomainRecipe
     * @throws RuntimeException
     */
    public function hydrate(array $data)
    {
        $recipe = new DomainRecipe(new Name($data['name']));
        $publicationDate = is_null($data['publicationDate']) ? null : new \DateTimeImmutable($data['publicationDate']);
        $this->setPropertyValue($recipe, 'publicationDate', $publicationDate);

        $this->deserializeDescription($recipe, $data);
        $this->deserializeSteps($recipe, $data);
        $this->deserializeTaste($recipe, $data['description']);
        
        return $recipe;
    }

    /**
     * @param $object
     * @param $propertyName
     * @param $value
     */
    private function setPropertyValue($object, $propertyName, $value)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }


    /**
     * @param DomainRecipe $recipe
     * @param array $data
     * @throws \MyDrinks\Domain\Exception\InvalidArgumentException
     * @internal param Recipe $recipe
     */
    private function deserializeDescription(DomainRecipe $recipe, array $data)
    {
        $description = new DomainRecipe\Description();
        if (isset($data['description']['IBAOfficial']) && $data['description']['IBAOfficial'] === true) {
            $description->markAsIBAOfficial();
        }

        if (isset($data['description']['text']) && !empty($data['description']['text'])) {
            $description->setText($data['description']['text']);
        }

        if (isset($data['description']['alcoholContent'])) {
            $description->setAlcoholContent((int) $data['description']['alcoholContent']);
        }

        $recipe->updateDescription($description);
    }

    /**
     * @param DomainRecipe $recipe
     * @param array $data
     * @throws RuntimeException
     * @throws \MyDrinks\Domain\Exception\Recipe\GlassCapacityOverflowException
     * @throws \MyDrinks\Domain\Exception\Recipe\MissingGlassException
     */
    private function deserializeSteps(DomainRecipe $recipe, array $data)
    {
        foreach ($data['steps'] as $step) {
            switch ($step['type']) {
                case Actions::PREPARE_GLASS:
                    $recipe->prepareTheGlass(new Name($step['name']), new Capacity($step['capacity']), new Amount($step['amount']));
                    break;
                case Actions::POUR_INTO_GLASS:
                    $recipe->pourIntoGlass(new Name($step['name']), new Capacity($step['capacity']));
                    break;
                case Actions::STRAIN_INTO_GLASS_FROM_SHAKER:
                    $recipe->strainIntoGlassFromShaker();
                    break;
                case Actions::ADD_INGREDIENT_INTO_GLASS:
                    $recipe->addIngredientIntoGlass(new Name($step['name']), new Amount($step['amount']));
                    break;
                case Actions::STIR_GLASS_CONTENT:
                    $recipe->stirGlassContent();
                    break;
                case Actions::FILL_GLASS:
                    $recipe->fillGlassWith(new Name($step['name']));
                    break;
                case Actions::IGNITE_GLASS_CONTENT:
                    $recipe->igniteGlassContent();
                    break;
                case Actions::GARNISH_GLASS:
                    $recipe->garnishGlass(new Name($step['name']));
                    break;
                case Actions::EMPTY_GLASS_CONTENT:
                    $recipe->emptyTheGlass();
                    break;
                case Actions::MUDDLE_GLASS_CONTENT:
                    $recipe->muddleContent();
                    break;
                case Actions::TOP_UP_GLASS:
                    $recipe->topUpGlass(new Name($step['name']));
                    break;
                
                case Actions::PREPARE_SHAKER:
                    $recipe->prepareTheShaker(new Capacity($step['capacity']));
                    break;
                case Actions::POUR_INTO_SHAKER:
                    $recipe->pourIntoShaker(new Name($step['name']), new Capacity($step['capacity']));
                    break;
                case Actions::SHAKE_SHAKER_CONTENT:
                    $recipe->shakeShakerContent();
                    break;
                case Actions::FILL_SHAKER:
                    $recipe->fillShakerWith(new Name($step['name']));
                    break;
                case Actions::ADD_INGREDIENT_INTO_SHAKER:
                    $recipe->addIngredientIntoShaker(new Name($step['name']), new Amount($step['amount']));
                    break;
                default:
                    throw new RuntimeException(sprintf("Unknown step type \"%s\"", $step['type']));
            }
        }
    }

    /**
     * @param $recipe
     * @param array $data
     */
    private function deserializeTaste(DomainRecipe $recipe, array $data)
    {
        $tasteBuilder = new TasteBuilder();
        if (array_key_exists('taste', $data)) {
            foreach ($data['taste'] as $tasteName) {
                switch ($tasteName) {
                    case Tastes::SWEET:
                        $tasteBuilder->sweet();
                        break;
                    case Tastes::BITTER:
                        $tasteBuilder->bitter();
                        break;
                    case Tastes::SALTY:
                        $tasteBuilder->salty();
                        break;
                    case Tastes::SPICY:
                        $tasteBuilder->spicy();
                        break;
                    case Tastes::SOUR:
                        $tasteBuilder->sour();
                        break;
                }
            }
        }

        $recipe->getDescription()->changeTaste($tasteBuilder->buildTaste());
    }
}