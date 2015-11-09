<?php

namespace MyDrinks\Infrastructure\Application\Recipe;

use Cocur\Slugify\Slugify;
use MyDrinks\Application\Recipes;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;
use MyDrinks\Infrastructure\UserInterface\SlugGenerator\SlugifyAdapter;

final class InMemoryRecipes implements Recipes
{
    /**
     * @var array[]|Recipe[]
     */
    private $recipes;

    /**
     * @var SlugifyAdapter
     */
    private $slugGenerator;
    
    public function __construct()
    {
        $this->recipes = [];
        $this->slugGenerator = new SlugifyAdapter(new Slugify());
    }
    
    /**
     * @param Recipe $recipe
     */
    public function add(Recipe $recipe)
    {
        $this->recipes[(string) $recipe->getName()] = $recipe;
    }

    /**
     * @param Recipe $recipe
     */
    public function remove(Recipe $recipe)
    {
       unset($this->recipes[(string) $recipe->getName()]);
    }

    /**
     * @param Name $name
     * @return null|Recipe
     */
    public function findRecipeByName(Name $name)
    {
        return array_key_exists((string) $name, $this->recipes) 
            ? $this->recipes[(string) $name]
            : null;
    }

    /**
     * @param Name $name
     * @return boolean
     */
    public function hasRecipeWithName(Name $name)
    {
        return array_key_exists((string) $name, $this->recipes);
    }

    /**
     * @param string $slug
     * @return null|Recipe
     */
    public function findBySlug($slug)
    {
        foreach ($this->recipes as $recipe) {
            if ($this->slugGenerator->generateFrom($recipe->getName()) === $slug) {
                return $recipe;
            }
        }
        
        return null;
    }
}