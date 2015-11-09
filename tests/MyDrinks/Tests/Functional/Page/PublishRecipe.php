<?php

namespace MyDrinks\Tests\Functional\Page;

final class PublishRecipe extends BasePage
{
    /**
     * @return string;
     */
    public function getUrl()
    {
        return '/admin/recipes/{slug}/publish';
    }

    public function open($method = 'GET', $parameters = [])
    {
        parent::open($method, $parameters);
        
        return new AddRecipeStep($this->client, $this);
    }
}