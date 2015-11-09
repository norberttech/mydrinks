<?php

namespace MyDrinks\Application\Command;

use MyDrinks\Application\Recipe\Description\Tastes;
use MyDrinks\Domain\Recipe;

final class UpdateRecipeDescriptionCommand 
{
    public $slug; 
    
    public $text;
    
    public $IBAOfficial;
    
    public $alcoholContent;
    
    public $taste = [];
    
    public static function createFromRecipe(Recipe $recipe)
    {
        $command = new UpdateRecipeDescriptionCommand();
        $command->text = $recipe->getDescription()->getText();
        $command->IBAOfficial = $recipe->getDescription()->isOfficialIBA();
        $command->alcoholContent = $recipe->getDescription()->getAlcoholContent();
        
        $taste = $recipe->getDescription()->getTaste();
        
        if ($taste->isSweet()) {
            $command->taste[] = Tastes::SWEET;
        }
        if ($taste->isSpicy()) {
            $command->taste[] = Tastes::SPICY;
        }
        if ($taste->isBitter()) {
            $command->taste[] = Tastes::BITTER;
        }
        if ($taste->isSalty()) {
            $command->taste[] = Tastes::SALTY;
        }
        if ($taste->isSour()) {
            $command->taste[] = Tastes::SOUR;
        }
        
        return $command;
    }
}