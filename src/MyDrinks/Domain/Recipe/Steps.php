<?php

namespace MyDrinks\Domain\Recipe;

use MyDrinks\Domain\Exception\InvalidArgumentException;
use MyDrinks\Domain\Exception\UnknownStepException;
use MyDrinks\Domain\Recipe\Step\AddIngredientIntoGlass;
use MyDrinks\Domain\Recipe\Step\AddIngredientIntoShaker;
use MyDrinks\Domain\Recipe\Step\FillGlass;
use MyDrinks\Domain\Recipe\Step\FillShaker;
use MyDrinks\Domain\Recipe\Step\PourIntoGlass;
use MyDrinks\Domain\Recipe\Step\PourIntoShaker;
use MyDrinks\Domain\Recipe\Step\TopUpGlass;
use MyDrinks\Domain\Recipe\Supply\Amount;
use MyDrinks\Domain\Recipe\Supply\Ingredient;
use MyDrinks\Domain\Recipe\Supply\Liquid;

class Steps implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * @var Step[]|array
     */
    private $steps;
    
    public function __construct()
    {
        $this->steps = [];
    }

    public function clear()
    {
        $this->steps = [];
    }
        
    /**
     * @return int
     */
    public function count()
    {
        return count($this->steps);
    }

    /**
     * @param int $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->steps);
    }

    /**
     * @param int $offset
     * @return Step
     * @throws UnknownStepException
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new UnknownStepException();
        }
        
        return $this->steps[$offset];
    }

    /**
     * @param int $offset
     * @param Step $value
     * @throws InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {
        $offset = count($this->steps);
        if (!$value instanceof Step) {
            throw new InvalidArgumentException;
        }
        
        $this->steps[$offset] = $value;
    }

    /**
     * @param int $offset
     * @throws UnknownStepException
     */
    public function offsetUnset($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new UnknownStepException();
        }
        
        unset($this->steps[$offset]);
        $this->steps = array_values($this->steps);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->steps);
    }

    /**
     * @param Step $step
     */
    public function add(Step $step)
    {
        $this->steps[] = $step;
    }

    /**
     * @return Liquid[]|array
     */
    public function getLiquids()
    {
        $liquids = [];
        
        foreach ($this->steps as $step) {
            $liquid = null;
            if ($step instanceof PourIntoGlass) {
                $liquid = new Liquid($step->getName(), $step->getCapacity());
            }
            
            if ($step instanceof PourIntoShaker) {
                $liquid = new Liquid($step->getName(), $step->getCapacity());
            }

            if ($step instanceof TopUpGlass) {
                $liquid = new Liquid($step->getName(), $step->getCapacity());
            }
            
            if (is_null($liquid)) {
                continue; 
            }
            
            if (array_key_exists((string) $liquid->getName(), $liquids)) {
                $liquids[(string) $liquid->getName()]->fill($liquid->getCapacity());
            } else {
                $liquids[(string) $liquid->getName()] = $liquid;
            }
        }
        
        return array_values($liquids);
    }

    /**
     * @return Ingredient[]|array
     */
    public function getIngredients()
    {
        $ingredients = [];

        foreach ($this->steps as $step) {
            $ingredient = null;
            if ($step instanceof AddIngredientIntoGlass) {
                $ingredient = new Ingredient($step->getIngredientName(), $step->getAmount());
            }

            if ($step instanceof AddIngredientIntoShaker) {
                $ingredient = new Ingredient($step->getIngredientName(), $step->getAmount());
            }

            if ($step instanceof FillGlass) {
                $ingredient = new Ingredient($step->getContentName(), new Amount(0));
            }

            if ($step instanceof FillShaker) {
                $ingredient = new Ingredient($step->getContentName(), new Amount(0));
            }
            
            if (is_null($ingredient)) {
                continue;
            }

            if (array_key_exists((string) $ingredient->getName(), $ingredients)) {
                $ingredients[(string) $ingredient->getName()]->add($ingredient->getAmount());
            } else {
                $ingredients[(string) $ingredient->getName()] = $ingredient;
            }
        }

        return array_values($ingredients);
    }
}
