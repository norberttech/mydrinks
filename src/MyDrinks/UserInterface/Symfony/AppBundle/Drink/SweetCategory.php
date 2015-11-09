<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Drink;

use MyDrinks\Application\Recipe\Description\TasteBuilder;
use MyDrinks\Application\SearchEngine;
use MyDrinks\Application\SearchEngine\SearchResultSlice;

class SweetCategory implements Category
{
    /**
     * @var SearchEngine
     */
    private $searchEngine;
    
    /**
     * @param SearchEngine $searchEngine
     */
    public function __construct(SearchEngine $searchEngine)
    {
        $this->searchEngine = $searchEngine;
    }
    
    /**
     * @return string
     */
    public function getId()
    {
        return 'sweet';
    }

    /**
     * @param int $from
     * @param int $size
     * @return SearchResultSlice
     */
    public function getDrinks($from = 0, $size = 20)
    {
        $criteria = new SearchEngine\Criteria($from);
        $criteria->changeSize($size);
        $criteria->updateRequiredTaste((new TasteBuilder())->sweet()->buildTaste());
        
        return $this->searchEngine->search($criteria);
    }
}
