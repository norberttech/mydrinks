<?php

namespace MyDrinks\Tests\Functional;

use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;
use MyDrinks\Tests\Functional\Page;

class UpdateDescriptionTest extends WebTestCase
{
    public function test_successful_creating_new_recipe()
    {
        $this->storage->save(new Recipe(new Name("Screwdriver")));
        
        (new Page\UpdateRecipeDescription($this->client))
            ->open("GET", ["slug" => "screwdriver"])
            ->fillDescriptionForm('Lorem Ipsum', true)
            ->pressSubmitButton()
            ->shouldBeRedirectedFrom('/admin/recipes/{slug}/update-description')
            ->shouldBeRedirectedTo('/admin/recipes/{slug}/add-step');
        
        $recipe = $this->storage->fetchBySlug("screwdriver");
        
        $this->assertTrue($recipe->getDescription()->hasText());
        $this->assertTrue($recipe->getDescription()->isOfficialIBA());
        $this->assertSame("Lorem Ipsum", $recipe->getDescription()->getText());
    }
}