<?php

namespace MyDrinks\Application;

interface SlugGenerator 
{
    /**
     * @param string $string
     * @return string
     */
    public function generateFrom($string);
}