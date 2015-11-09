<?php

namespace MyDrinks\Application\SearchEngine;

interface CriteriaDecorator
{
    /**
     * @param Criteria $criteria
     */
    public function decorate(Criteria $criteria);
}