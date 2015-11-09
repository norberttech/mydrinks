<?php

namespace MyDrinks\Infrastructure\Application\Recipe;

use MyDrinks\Application\Recipe\ImageUrlResolver;

final class LocalFilesystemUrlResolver implements ImageUrlResolver
{
    /**
     * @var string
     */
    private $recipeImagePrefix;
    
    public function __construct($recipeImagePrefix)
    {
        $this->recipeImagePrefix = rtrim(ltrim($recipeImagePrefix, DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);
    }
    
    /**
     * @param string $path
     * @return string
     */
    public function resolveUrlFor($path)
    {
        $pathPrefix = strlen($this->recipeImagePrefix)
            ? DIRECTORY_SEPARATOR . $this->recipeImagePrefix . DIRECTORY_SEPARATOR
            : DIRECTORY_SEPARATOR;

        return $pathPrefix . ltrim($path, DIRECTORY_SEPARATOR);
    }
}