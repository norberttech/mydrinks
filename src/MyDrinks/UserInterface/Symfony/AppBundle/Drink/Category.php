<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Drink;

use MyDrinks\Application\SearchEngine\SearchResultSlice;

interface Category
{
    /**
     * @param int $from
     * @param int $size
     * @return SearchResultSlice
     */
    public function getDrinks($from = 0, $size = 20);

    /**
     * @return string
     */
    public function getId();
}