<?php

namespace MyDrinks\Application\SearchEngine;

use MyDrinks\Application\Exception\InvalidArgumentException;

final class SearchResultSlice
{
    /**
     * @var array|Result\Recipe[]
     */
    private $items;

    /**
     * @var int
     */
    private $totalCount;
    /**
     * @var Criteria
     */
    private $criteria;

    /**
     * @param Criteria $criteria
     * @param array|Result\Recipe[] $items
     * @param $totalCount
     * @throws InvalidArgumentException
     */
    public function __construct(Criteria $criteria, array $items = [], $totalCount)
    {
        foreach ($items as $item) {
            if (!$item instanceof Result\Recipe) {
                throw new InvalidArgumentException;
            }
        }
        
        $this->items = $items;
        $this->totalCount = $totalCount;
        $this->criteria = $criteria;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return !(bool) $this->totalCount;
    }

    /**
     * @return Criteria
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @return array|Result\Recipe[]
     */
    public function getItems()
    {
        return $this->items;
    }
    
    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @return int
     */
    public function getPagesCount()
    {
        return (int) ceil($this->totalCount / $this->criteria->getSize()) - 1 ;
    }

    public function getCurrentPage()
    {
        return (int) (($this->criteria->startFrom()) / $this->criteria->getSize());
    }
}
