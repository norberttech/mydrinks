<?php

namespace MyDrinks\Infrastructure\Application\Recipe;

use MyDrinks\Application\Exception\Recipe\RecipeNotFoundException;
use MyDrinks\Application\Filesystem;
use MyDrinks\Application\Recipe\Storage;
use MyDrinks\Application\Serializer;
use MyDrinks\Application\SlugGenerator;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;
use Tests\Behat\Gherkin\Loader\DirectoryLoaderTest;

final class JsonFilesystemStorage implements Storage
{
    /**
     * @var Filesystem
     */
    private $filesystem;
    
    /**
     * @var SlugGenerator
     */
    private $slugGenerator;
    
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @param Filesystem $filesystem
     * @param SlugGenerator $slugGenerator
     * @param Serializer $serializer
     */
    public function __construct(Filesystem $filesystem, SlugGenerator $slugGenerator, Serializer $serializer)
    {
        $this->filesystem = $filesystem;
        $this->slugGenerator = $slugGenerator;
        $this->serializer = $serializer;
    }
    
    /**
     * @param Recipe $recipe
     */
    public function save(Recipe $recipe)
    {
        $fileName = $this->generateFileName($recipe->getName());
    
        $this->filesystem->write($fileName, $this->serializer->serialize($recipe));
    }

    /**
     * @param Recipe $recipe
     * @throws RecipeNotFoundException
     */
    public function remove(Recipe $recipe)
    {
        if (!$this->hasRecipeWithName($recipe->getName())) {
            throw new RecipeNotFoundException(sprintf("Recipe \"%s\" does not exists", (string) $recipe->getName()));
        }
        
        $this->filesystem->remove(DIRECTORY_SEPARATOR . $this->slugGenerator->generateFrom((string) $recipe->getName()));
    }

    /**
     * @param Name $name
     * @return bool
     */
    public function hasRecipeWithName(Name $name)
    {
        return $this->filesystem->has($this->generateFileName($name));
    }

    /**
     * @param Name $name
     * @return Recipe
     * @throws RecipeNotFoundException
     */
    public function fetchByName(Name $name)
    {
        if (!$this->hasRecipeWithName($name)) {
            throw new RecipeNotFoundException(sprintf("Recipe \"%s\" does not exists", (string) $name));
        }
       
        $json = $this->filesystem->read($this->generateFileName($name));
        
        return $this->serializer->deserialize($json, Recipe::class);
    }

    /**
     * @param string $slug
     * @return Recipe
     * @throws RecipeNotFoundException
     */
    public function fetchBySlug($slug)
    {
        if (!$this->filesystem->has($this->generateFileNameFromSlug($slug))) {
            throw new RecipeNotFoundException(sprintf("Recipe with slug \"%s\" does not exists", $slug));
        }

        $json = $this->filesystem->read($this->generateFileNameFromSlug($slug));

        return $this->serializer->deserialize($json, Recipe::class);
    }

    /**
     * @return \Generator
     */
    public function fetchAll()
    {
        $slugs = $this->filesystem->foldersNames('/');
        
        foreach ($slugs as $slug) {
            if ($this->filesystem->has($this->generateFileNameFromSlug($slug))) {
                yield $this->fetchBySlug($slug);
            }
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->filesystem->foldersCount('/');
    }

    /**
     * @param Name $name
     * @return string
     */
    private function generateFileName(Name $name)
    {
        return $this->generateFileNameFromSlug($this->slugGenerator->generateFrom((string) $name));
    }

    /**
     * @param string $slug
     * @return string
     */
    private function generateFileNameFromSlug($slug)
    {
        return DIRECTORY_SEPARATOR . $slug . 
            DIRECTORY_SEPARATOR . implode(str_split(hash('sha256', $slug), 2), DIRECTORY_SEPARATOR) .
            DIRECTORY_SEPARATOR . $slug . '.json';
    }
}