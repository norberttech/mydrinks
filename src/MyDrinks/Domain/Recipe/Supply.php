<?php

namespace MyDrinks\Domain\Recipe;

interface Supply 
{
    /**
     * @return string
     */
    public function getName();
}