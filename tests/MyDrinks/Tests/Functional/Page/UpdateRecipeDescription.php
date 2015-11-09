<?php

namespace MyDrinks\Tests\Functional\Page;

final class UpdateRecipeDescription extends BasePage
{
    private $form;
    
    /**
     * @return string;
     */
    public function getUrl()
    {
        return '/admin/recipes/{slug}/update-description';
    }
    
    public function fillDescriptionForm($text, $IBAOfficial)
    {
        $this->form = $this->crawler->filter("form[name=\"recipe_description\"]")->form([
            'recipe_description[text]' => $text,
            'recipe_description[IBAOfficial]' => $IBAOfficial
        ], 'POST');

        return $this;
    }
    
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
    
    public function shouldSeeError($errorMessage)
    {
        if (!$this->crawler->filter(sprintf('li:contains("%s")', $errorMessage))->count()) {
            throw new \RuntimeException(sprintf("Error \"%s\" is not visible", $errorMessage));
        }
        
        return $this;
    }
}