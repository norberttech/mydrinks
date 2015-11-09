<?php

namespace spec\MyDrinks\Application\Handler;

use MyDrinks\Application\Command\UpdateRecipeDescriptionCommand;
use MyDrinks\Application\Exception\Recipe\RecipeNotFoundException;
use MyDrinks\Application\Recipe\Description\Tastes;
use MyDrinks\Application\Recipes;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UpdateRecipeDescriptionHandlerSpec extends ObjectBehavior
{
    function let(Recipes $recipes)
    {
        $this->beConstructedWith($recipes);
    }
    
    function it_throws_exception_when_recipe_with_slug_does_not_exists()
    {
        $command = new UpdateRecipeDescriptionCommand();
        $command->slug = 'invalid';

        $this->shouldThrow(RecipeNotFoundException::class)->during("handle", [$command]);
    }
    
    function it_updates_recipe_description(Recipes $recipes)
    {
        $recipe = $this->createRecipe();
        $recipes->findBySlug("screwdriver")->willReturn($recipe);

        $command = new UpdateRecipeDescriptionCommand();
        $command->slug = 'screwdriver';
        $command->text = 'Lorem ipsum';
        $command->IBAOfficial = true;
        $command->taste = [
            Tastes::SWEET,
            Tastes::SOUR
        ];
        
        $this->handle($command);

        if (!$recipe->getDescription()->isOfficialIBA()) {
            throw new \RuntimeException("Expected recipe to be IBA official.");
        }

        if (!$recipe->getDescription()->getTaste()->isSweet() && !$recipe->getDescription()->getTaste()->isSour()) {
            throw new \RuntimeException("Expected recipe to be sweet and sour.");
        }

        if ($recipe->getDescription()->getText() !== $command->text)  {
            throw new \RuntimeException(
                sprintf(
                    "Expected recipe description is %s, %s given.", 
                    $command->text, $recipe->getDescription()->getText()
                )
            );
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
