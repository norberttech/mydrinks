<?php

namespace MyDrinks\Tests\Functional\Page;

use MyDrinks\Application\Recipe\Actions;

final class AddRecipeStep extends BasePage
{
    private $form;
    
    /**
     * @return string;
     */
    public function getUrl()
    {
        return '/admin/recipes/{slug}/add-step';
    }
    
    public function fillAddStepFormWithPrepareTheGlass($glassName, $glassCapacity)
    {
        $this->form = $this->crawler->filter("form[name=\"recipe_step\"]")->form([
            'recipe_step[type]' => Actions::PREPARE_GLASS,
            'recipe_step[name]' => $glassName,
            'recipe_step[capacity]' => $glassCapacity,
            'recipe_step[amount]' => 1
        ], 'POST');

        return $this;
    }

    public function fillPourIntoGlass($liquidName, $capacity)
    {
        $this->form = $this->crawler->filter("form[name=\"recipe_step\"]")->form([
            'recipe_step[type]' => Actions::POUR_INTO_GLASS,
            'recipe_step[name]' => $liquidName,
            'recipe_step[capacity]' => $capacity
        ], 'POST');

        return $this;
    }

    public function fillAddStepFormWithPrepareShaker($shakerName, $capacity)
    {
        $this->form = $this->crawler->filter("form[name=\"recipe_step\"]")->form([
            'recipe_step[type]' => Actions::PREPARE_SHAKER,
            'recipe_step[name]' => $shakerName,
            'recipe_step[capacity]' => $capacity
        ], 'POST');

        return $this;
    }

    public function fillPourIntoShaker($liquidName, $capacity)
    {
        $this->form = $this->crawler->filter("form[name=\"recipe_step\"]")->form([
            'recipe_step[type]' => Actions::POUR_INTO_SHAKER,
            'recipe_step[name]' => $liquidName,
            'recipe_step[capacity]' => $capacity
        ], 'POST');

        return $this;
    }

    public function fillPourIntoGlassFromShaker()
    {
        $this->form = $this->crawler->filter("form[name=\"recipe_step\"]")->form([
            'recipe_step[type]' => Actions::STRAIN_INTO_GLASS_FROM_SHAKER,
            'recipe_step[name]' => 'liquid.shaker_contents',
        ], 'POST');

        return $this;
    }
    
    public function pressSubmitButton()
    {
        $this->client->followRedirects(true);
        $this->crawler = $this->client->submit($this->form);

        return $this;
    }

    public function shouldSeeError($errorMessage)
    {
        if (!$this->crawler->filter(sprintf('div.alert-danger:contains("%s")', $errorMessage))->count()) {
            throw new \RuntimeException(sprintf("Error \"%s\" is not visible", $errorMessage));
        }

        return $this;
    }
}