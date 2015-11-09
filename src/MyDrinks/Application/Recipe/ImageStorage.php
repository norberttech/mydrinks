<?php

namespace MyDrinks\Application\Recipe;

use MyDrinks\Domain\Recipe;

interface ImageStorage
{
    /**
     * @param $slug
     * @return bool
     */
    public function hasImageFor($slug);

    /**
     * @param Image $image
     * @param $slug
     */
    public function saveImageFor(Image $image, $slug);

    /**
     * @param $slug
     */
    public function removeImageFor($slug);

    /**
     * @param $slug
     * @return string
     */
    public function getPathFor($slug);
}