<?php

namespace MyDrinks\Infrastructure\Application\Recipe;

use Aws\S3\S3Client;
use MyDrinks\Application\Recipe\ImageUrlResolver;

final class SimpleResolver implements ImageUrlResolver
{
    /**
     * @param $path
     * @return string
     */
    public function resolveUrlFor($path)
    {
       return DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
    }
}