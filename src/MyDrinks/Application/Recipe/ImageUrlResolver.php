<?php

namespace MyDrinks\Application\Recipe;

interface ImageUrlResolver
{
    /**
     * @param string $path
     * @return string
     */
    public function resolveUrlFor($path);
}