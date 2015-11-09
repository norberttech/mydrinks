<?php

namespace MyDrinks\Application\SearchEngine;

use MyDrinks\Application\Exception\InvalidArgumentException;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;
use MyDrinks\Domain\Recipe\Description\Taste;

final class Criteria
{
    const ITEMS_PER_PAGE = 10;
    const SORT_ASC = 'asc';
    const SORT_DESC = 'desc';
    
    /**
     * @var null|string
     */
    private $query;

    /**
     * @var null|string
     */
    private $glassName;
    
    /**
     * @var int
     */
    private $start;

    /**
     * @var int
     */
    private $size;
    
    /**
     * @var bool
     */
    private $onlyPublished;

    /**
     * @var array
     */
    private $sortBy;

    /**
     * @var array
     */
    private $requiredIngredients;

    /**
     * @var array
     */
    private $optionalIngredients;

    /**
     * @var int
     */
    private $alcoholContentGreaterThan;

    /**
     * @var int
     */
    private $alcoholContentLowerThan;

    /**
     * @var Taste 
     */
    private $requiredTaste;

    /**
     * @var Taste
     */
    private $optionalTaste;

    /**
     * @var Name|null
     */
    private $excludedName;

    /**
     * @var Name|null
     */
    private $similarTo;
    
    /**
     * @param int $start
     * @param bool $onlyPublished
     */
    public function __construct($start = 0, $onlyPublished = true)
    {
        $this->start = $start;
        $this->onlyPublished = $onlyPublished;
        $this->sortBy = [];
        $this->size = self::ITEMS_PER_PAGE;
        $this->requiredIngredients = [];
        $this->optionalIngredients = [];
        $this->requiredTaste = new Taste();
        $this->optionalTaste = new Taste();
    }

    /**
     * @return bool
     */
    public function areEmpty()
    {
        return !$this->hasRequiredIngredients() 
            && !$this->hasOptionalIngredients()
            && !$this->hasQuery() 
            && !$this->hasGlassName()
            && is_null($this->alcoholContentGreaterThan)
            && is_null($this->alcoholContentLowerThan)
            && !$this->requiredTaste->isDefined()
            && !$this->optionalTaste->isDefined();
    }
    
    /**
     * @param string $query
     * @throws InvalidArgumentException
     */
    public function addQuery($query)
    {
        if (!is_string($query) || empty($query)) {
            return ;
        }
        
        $this->query = $query;
    }

    /**
     * @return bool
     */
    public function hasQuery()
    {
        return !is_null($this->query);
    }

    /**
     * @return null|string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param array $ingredients
     */
    public function mustContainIngredients($ingredients = [])
    {
        $this->requiredIngredients = array_values($ingredients);
    }

    /**
     * @return array
     */
    public function getRequiredIngredients()
    {
        return $this->requiredIngredients;
    }

    /**
     * @return bool
     */
    public function hasRequiredIngredients()
    {
        return (bool) count($this->requiredIngredients);
    }

    /**
     * @param array $ingredients
     */
    public function mayContainIngredients($ingredients = [])
    {
        $this->optionalIngredients = array_values($ingredients);
    }

    /**
     * @return bool
     */
    public function hasOptionalIngredients()
    {
        return (bool) count($this->optionalIngredients);
    }
    
    /**
     * @return array
     */
    public function getOptionalIngredients()
    {
        return $this->optionalIngredients;
    }

    /**
     * @return bool
     */
    public function hasGlassName()
    {
        return !is_null($this->glassName);
    }

    /**
     * @return null|string
     */
    public function getGlassName()
    {
        return $this->glassName;
    }

    /**
     * @param $name
     */
    public function withGlass($name)
    {
        $this->glassName = (string) $name;
    }
    
    /**
     * @return int
     */
    public function startFrom()
    {
        return $this->start;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }
    
    /**
     * @return boolean
     */
    public function onlyPublished()
    {
        return $this->onlyPublished;
    }

    /**
     * @param $field
     * @param string $order
     */
    public function sortBy($field, $order = self::SORT_DESC)
    {
        $this->sortBy[$field] = $order;
    }

    /**
     * @param $newSize
     */
    public function changeSize($newSize)
    {
        $this->size = $newSize;
    }

    /**
     * @return boolean
     */
    public function sortResults()
    {
        return (boolean) count($this->sortBy);
    }

    /**
     * @return array
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     * @param $number
     */
    public function withAlcoholContentGreaterThan($number)
    {
        $this->alcoholContentGreaterThan = (int) $number;
    }

    /**
     * @param $number
     */
    public function withAlcoholContentLowerThan($number)
    {
        $this->alcoholContentLowerThan = (int) $number;
    }
    
    /**
     * @return mixed
     */
    public function getAlcoholContentGreaterThan()
    {
        return $this->alcoholContentGreaterThan;
    }

    /**
     * @return mixed
     */
    public function getAlcoholContentLowerThan()
    {
        return $this->alcoholContentLowerThan;
    }

    /**
     * @return Taste
     */
    public function getRequiredTaste()
    {
        return $this->requiredTaste;
    }

    /**
     * @param Taste $taste
     */
    public function updateRequiredTaste(Taste $taste)
    {
        $this->requiredTaste = $taste;
    }

    /**
     * @return Taste
     */
    public function getOptionalTaste()
    {
        return $this->optionalTaste;
    }

    /**
     * @param Taste $taste
     */
    public function updateOptionalTaste(Taste $taste)
    {
        $this->optionalTaste = $taste;
    }

    /**
     * @param Name $name
     */
    public function excludeRecipeWithName(Name $name)
    {
        $this->excludedName = $name;
    }

    /**
     * @param Name $recipeName
     */
    public function similarTo(Name $recipeName)
    {
        $this->similarTo = $recipeName;
    }

    /**
     * @return bool
     */
    public function lookForSimilarRecipes()
    {
        return !is_null($this->similarTo);
    }

    /**
     * @return Name|null
     */
    public function getSimilarToRecipeName()
    {
        return $this->similarTo;
    }
    
    /**
     * @param CriteriaDecorator $decorator
     */
    public function decorate(CriteriaDecorator $decorator)
    {
        $decorator->decorate($this);
    }
}
