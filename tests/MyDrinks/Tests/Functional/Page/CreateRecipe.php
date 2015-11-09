<?php

namespace MyDrinks\Tests\Functional\Page;

final class CreateRecipe extends BasePage
{
    private $form;
    
    /**
     * @return string;
     */
    public function getUrl()
    {
        return '/admin/recipes';
    }
    
    public function fillNewRecipeForm($name)
    {
        $this->form = $this->crawler->filter("form[name=\"create_recipe\"]")->form([
            'create_recipe[name]' => $name,
        ], 'POST');

        return $this;
    }

    /**
     * @return $this|AddRecipeStep
     */
    public function pressSubmitButton()
    {
        $this->crawler = $this->client->submit($this->form);

        $status = $this->client->getResponse()->getStatusCode();
        if ($status === 302) {
            $this->client->followRedirect();
            return new AddRecipeStep($this->client, $this);
        }
        
        return $this;
    }

    /**
     * @return $this|AddRecipeStep
     */
    public function pressRemoveRecipeButton($slug)
    {
        $form = $this->crawler->filter("#remove-recipe-" .  $slug)
            ->form(
                [],
               'POST'
            );

        $this->crawler = $this->client->submit($form);
        $status = $this->client->getResponse()->getStatusCode();
        if ($status === 302) {
            $this->client->followRedirect();
            return new CreateRecipe($this->client, $this);
        }

        return $this;
    }

    /**
     * @param $errorMessage
     * @return $this
     */
    public function shouldSeeError($errorMessage)
    {
        if (!$this->crawler->filter(sprintf('li:contains("%s")', $errorMessage))->count()) {
            throw new \RuntimeException(sprintf("Error \"%s\" is not visible", $errorMessage));
        }
        
        return $this;
    }
}