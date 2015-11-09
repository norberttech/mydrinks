<?php

namespace MyDrinks\Tests\Functional;

use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;
use MyDrinks\Tests\Functional\Page;

class RemoveRecipeTest extends WebTestCase
{
    public function test_removing_recipe()
    {
        $this->storage->save(new Recipe(new Name("Screwdriver")));
        (new Page\CreateRecipe($this->client))
            ->open()
            ->pressRemoveRecipeButton("screwdriver");

        $this->assertFalse($this->storage->hasRecipeWithName(new Name('Screwdriver')));
    }
}