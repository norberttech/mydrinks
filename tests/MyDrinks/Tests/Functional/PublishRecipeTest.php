<?php

namespace MyDrinks\Tests\Functional;

use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;
use MyDrinks\Tests\Functional\Page;

class PublishRecipeTest extends WebTestCase
{
    public function test_publishing_new_recipe()
    {
        $this->storage->save(new Recipe(new Name("Screwdriver")));

        (new Page\PublishRecipe($this->client))
            ->open("GET", ["slug" => "screwdriver", "number" => 1])
            ->shouldBeRedirectedFrom("/admin/recipes/{slug}/publish")
            ->shouldBeRedirectedTo("/admin/recipes/{slug}/add-step");

        ;
        
        $this->assertTrue($this->storage->fetchBySlug("screwdriver")->isPublished());
    }
}