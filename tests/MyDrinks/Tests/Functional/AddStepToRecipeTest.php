<?php

namespace MyDrinks\Tests\Functional;

use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;
use MyDrinks\Domain\Recipe\Step;
use MyDrinks\Tests\Functional\Page\Errors;
use MyDrinks\Tests\Functional\Page;

class AddStepToRecipeTest extends WebTestCase
{
    public function test_successful_adding_step_to_new_recipe()
    {
        $this->storage->save(new Recipe(new Name("Screwdriver")));
        
        (new Page\AddRecipeStep($this->client))
            ->open("GET", ["slug" => "screwdriver"])
            ->fillAddStepFormWithPrepareTheGlass("Highball", 250)
            ->pressSubmitButton()
            ->fillAddStepFormWithPrepareShaker("Boston Shaker", 250)
            ->pressSubmitButton()
            ->fillPourIntoShaker("Vodka", 50)
            ->pressSubmitButton();
        
        $steps = $this->storage->fetchByName(new Name('Screwdriver'))->getSteps();
        
        $this->assertStep($steps[0], Step\PrepareTheGlass::class, "Highball", 250, 1);
        $this->assertStep($steps[1], Step\PrepareTheShaker::class, "Boston Shaker", 250);
        $this->assertStep($steps[2], Step\PourIntoShaker::class, "Vodka", 50);
    }
    
    public function test_adding_pour_into_glass_from_shaker_to_recipe()
    {
        $recipe = new Recipe(new Name("Screwdriver"));
        $this->prepareGlassShakerAndLiquidForShaker($recipe);
        $this->storage->save($recipe);

        (new Page\AddRecipeStep($this->client))
            ->open("GET", ["slug" => "screwdriver"])
            ->fillPourIntoGlassFromShaker()
            ->pressSubmitButton();

        $steps = $this->storage->fetchByName(new Name('Screwdriver'))->getSteps();
        
        $this->assertStep($steps[5],Step\StrainIntoGlassFromShaker::class);
    }
    
    public function test_adding_step_in_wrong_order()
    {
        $this->storage->save(new Recipe(new Name("Screwdriver")));

        (new Page\AddRecipeStep($this->client))
            ->open("GET", ["slug" => "screwdriver"])
            ->fillPourIntoGlass("Vodka", 150)
            ->pressSubmitButton()
            ->shouldSeeError(Errors::INVALID_FIRST_STEP);
    }


    public function test_removing_step_from_recipe()
    {
        $recipe = new Recipe(new Name("Screwdriver"));
        $recipe->prepareTheGlass(new Name("Hightball"), new Recipe\Supply\Capacity(150));
        $this->storage->save($recipe);

        (new Page\RemoveRecipeStep($this->client))
            ->open("GET", ["slug" => "screwdriver", "number" => 1])
            ->shouldBeRedirectedFrom("/admin/recipes/{slug}/remove-step/{number}")
            ->shouldBeRedirectedTo("/admin/recipes/{slug}/add-step");
    }
    
    /**
     * @param $step
     */
    private function assertStep(Step $step, $expectedClass, $expectedName = null, $expectedCapacity = 0, $expectedAmount = 0)
    {
        $this->assertInstanceOf($expectedClass, $step);
        switch ($expectedClass) {
            case Step\PrepareTheGlass::class:
                $this->assertSame($expectedName, (string) $step->getName());
                $this->assertSame($expectedCapacity, $step->getCapacity()->getMilliliters());
                $this->assertSame($expectedAmount, $step->getAmount()->getValue());
                break;
            case Step\PrepareTheShaker::class:
                $this->assertSame($expectedCapacity, $step->getCapacity()->getMilliliters());
                break;
            case Step\PourIntoGlass::class:
                $this->assertSame($expectedName, (string) $step->getName());
                $this->assertSame($expectedCapacity, $step->getCapacity()->getMilliliters());
                break;
            case Step\PourIntoShaker::class:
                $this->assertSame($expectedName, (string) $step->getName());
                $this->assertSame($expectedCapacity, $step->getCapacity()->getMilliliters());
                break;
        }
    }

    /**
     * @param Recipe $recipe
     */
    private function prepareGlassShakerAndLiquidForShaker(Recipe $recipe)
    {
        $recipe->prepareTheGlass(new Name("Highball"), new Recipe\Supply\Capacity(250));
        $recipe->prepareTheShaker(new Recipe\Supply\Capacity(250));
        $recipe->pourIntoShaker(new Name("Vodka"), new Recipe\Supply\Capacity(100));
        $recipe->shakeShakerContent();
        $recipe->strainIntoGlassFromShaker();
    }
}