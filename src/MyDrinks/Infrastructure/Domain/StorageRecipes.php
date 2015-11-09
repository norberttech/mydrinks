<?php

namespace MyDrinks\Infrastructure\Domain;

use Isolate\Isolate;
use MyDrinks\Application\Recipe\Storage;
use MyDrinks\Application\Recipes;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;

final class StorageRecipes implements Recipes
{
    /**
     * @var Storage
     */
    private $storage;
    /**
     * @var Isolate
     */
    private $isolate;

    /**
     * @param Storage $storage
     */
    public function __construct(Storage $storage, Isolate $isolate)
    {
        $this->storage = $storage;
        $this->isolate = $isolate;
    }

    /**
     * @param Recipe $recipe
     */
    public function add(Recipe $recipe)
    {
        $transaction = $this->isolate->getContext()->getTransaction();
        $transaction->persist($recipe);
    }

    /**
     * @param Recipe $recipe
     */
    public function remove(Recipe $recipe)
    {
        $transaction = $this->isolate->getContext()->getTransaction();
        $transaction->delete($recipe);
    }

    /**
     * @param Name $name
     * @return null|Recipe
     */
    public function findRecipeByName(Name $name)
    {
        return $this->storage->hasRecipeWithName($name) ? $this->storage->fetchByName($name) : null;
    }

    /**
     * @param Name $name
     * @return boolean
     */
    public function hasRecipeWithName(Name $name)
    {
        return $this->storage->hasRecipeWithName($name);
    }

    /**
     * @param string $slug
     * @return null|Recipe
     */
    public function findBySlug($slug)
    {
        $recipe = $this->storage->fetchBySlug($slug);
        if (!is_null($recipe)) {
            if ($this->isolate->getContext()->hasOpenTransaction()) {
                $transaction = $this->isolate->getContext()->getTransaction();
                $transaction->persist($recipe);
            }
        }
        
        return $recipe;
    }
}