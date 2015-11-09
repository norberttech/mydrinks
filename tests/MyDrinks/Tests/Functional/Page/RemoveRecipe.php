<?php

namespace MyDrinks\Tests\Functional\Page;

final class RemoveRecipe extends BasePage
{
    /**
     * @return string;
     */
    public function getUrl()
    {
        return '/admin/recipes/{slug}';
    }

    public function open($method = 'DELETE', $parameters = [])
    {
        parent::open($method, $parameters);
        
        return new CreateRecipe($this->client, $this);
    }
}