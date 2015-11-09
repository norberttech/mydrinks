<?php

namespace MyDrinks\Infrastructure\Application\Recipe;

use MyDrinks\Application\Exception\Recipe\RecipeImageNotFoundException;
use MyDrinks\Application\Filesystem;
use MyDrinks\Application\Recipe\Image;
use MyDrinks\Application\Recipe\ImageStorage;
use MyDrinks\Domain\Recipe;

final class FilesystemImageStorage implements ImageStorage
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param $slug
     * @return bool
     */
    public function hasImageFor($slug)
    {
        $path = $this->generateBasePath($slug);
        $filePath = $this->composeJpgFilePath($slug, $path);

        return $this->filesystem->has($filePath);
    }

    /**
     * @param Image $image
     * @param $slug
     */
    public function saveImageFor(Image $image, $slug)
    {
        $newFilename = $this->composeJpgFilePath($slug, $this->generateBasePath($slug));

        $this->filesystem->write($newFilename, $image->getContent());
    }

    /**
     * @param $slug
     */
    public function removeImageFor($slug)
    {
        $path = $this->generateBasePath($slug);
        $filePath = $this->composeJpgFilePath($slug, $path);
        
        $this->filesystem->remove($filePath);
    }

    /**
     * @param string $slug
     * @return string
     * @throws RecipeImageNotFoundException
     */
    public function getPathFor($slug)
    {
        if (!$this->hasImageFor($slug)) {
            throw new RecipeImageNotFoundException(sprintf("Recipe image for slug \"%s\" does not exists", $slug));
        }
        $path = $this->generateBasePath($slug);
        
        return $this->composeJpgFilePath($slug, $path);
    }

    /**
     * @param string $slug
     * @return string
     */
    protected function generateBasePath($slug)
    {
        return DIRECTORY_SEPARATOR . $slug .
            DIRECTORY_SEPARATOR . implode(str_split(hash('sha256', $slug), 2), DIRECTORY_SEPARATOR) .
            DIRECTORY_SEPARATOR;
    }

    /**
     * @param $slug
     * @param $path
     * @return string
     */
    private function composeJpgFilePath($slug, $path)
    {
        return trim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $slug . '.' . Image::EXTENSION_JPG;
    }
}