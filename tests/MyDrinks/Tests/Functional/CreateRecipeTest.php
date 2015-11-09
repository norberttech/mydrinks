<?php

namespace MyDrinks\Tests\Functional;

use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;
use MyDrinks\Tests\Functional\Page;

class CreateRecipeTest extends WebTestCase
{
    public function test_successful_creating_new_recipe()
    {
        (new Page\CreateRecipe($this->client))
            ->open()
            ->fillNewRecipeForm('Screwdriver')
            ->pressSubmitButton()
            ->shouldBeRedirectedFrom('/admin/recipes')
            ->shouldBeRedirectedTo('/admin/recipes/{slug}/add-step');
        
        $this->assertTrue($this->storage->hasRecipeWithName(new Name('Screwdriver')));   
    }

    public function test_creating_recipe_without_name()
    {
        (new Page\CreateRecipe($this->client))
            ->open()
            ->fillNewRecipeForm('')
            ->pressSubmitButton()
            ->shouldSeeError(Page\Errors::EMPTY_VALUE_MSG);

        $this->assertFalse($this->storage->hasRecipeWithName(new Name('Screwdriver')));
    }
    
    public function test_adding_recipe_when_recipe_with_same_name_already_exists()
    {
        $this->storage->save(new Recipe(new Name("Screwdriver")));

        (new Page\CreateRecipe($this->client))
            ->open()
            ->fillNewRecipeForm('Screwdriver')
            ->pressSubmitButton()
            ->shouldSeeError(Page\Errors::RECIPE_NAME_NOT_UNIQUE_MSG);
    }
}