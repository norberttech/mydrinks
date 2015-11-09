<?php

namespace MyDrinks\Application\Handler;

use MyDrinks\Application\Command\UpdateRecipeDescriptionCommand;
use MyDrinks\Application\Exception\Recipe\RecipeNotFoundException;
use MyDrinks\Application\Recipe\Description\TasteBuilder;
use MyDrinks\Application\Recipe\Description\Tastes;
use MyDrinks\Application\Recipes;
use MyDrinks\Domain\Recipe\Description;

class UpdateRecipeDescriptionHandler
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
     * @param UpdateRecipeDescriptionCommand $command
     * @throws RecipeNotFoundException
     * @throws \MyDrinks\Domain\Exception\InvalidArgumentException
     */
    public function handle(UpdateRecipeDescriptionCommand $command)
    {
        $recipe = $this->recipes->findBySlug($command->slug);
        
        if (is_null($recipe)) {
            throw new RecipeNotFoundException;
        }
        
        $description = new Description();
        
        if ((boolean) $command->IBAOfficial) {
            $description->markAsIBAOfficial();
        }
        
        if (strlen($command->text)) {
            $description->setText($command->text);
        }
        
        if (!is_null($command->alcoholContent)) {
            $description->setAlcoholContent($command->alcoholContent);
        }
        
        if (!empty($command->taste) && is_array($command->taste)) {
            $tasteBuilder = new TasteBuilder();
            foreach ($command->taste as $tasteName) {
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
            
            $description->changeTaste($tasteBuilder->buildTaste());
        }
        
        $recipe->updateDescription($description);
    }
}
