<?php

namespace MyDrinks\Domain\Recipe\BarAccessory;

use MyDrinks\Domain\Exception\Recipe\AlreadyFilledException;
use MyDrinks\Domain\Exception\InvalidArgumentException;
use MyDrinks\Domain\Exception\Recipe\LiquidsNotShakedException;
use MyDrinks\Domain\Exception\Recipe\NotEnoughLiquidException;
use MyDrinks\Domain\Exception\Recipe\ShakerCapacityOverflowException;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe\Supply;
use MyDrinks\Domain\Recipe\Supply\Amount;
use MyDrinks\Domain\Recipe\Supply\Capacity;

class Shaker
{
    /**
     * @var Capacity
     */
    private $capacity;

    /**
     * @var Capacity
     */
    private $currentCapacity;

    /**
     * @var boolean
     */
    private $shaked;

    /**
     * @var Name|null
     */
    private $filledWith;

    /**
     * @var array[]
     */
    private $ingredients;
    
    /**
     * @param Capacity $capacity
     * @throws InvalidArgumentException
     */
    public function __construct(Capacity $capacity)
    {
        if ($capacity->getMilliliters() == 0) {
            throw new InvalidArgumentException("You need to specify capacity greater than 0.");
        }
        
        $this->capacity = $capacity;
        $this->currentCapacity = new Capacity(0);
        $this->shaked = false;
        $this->ingredients = [];
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->currentCapacity->getMilliliters() === 0 && !$this->isFilled() && !count($this->ingredients);
    }
    
    /**
     * @return Capacity
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @return Capacity
     */
    public function getCurrentCapacity()
    {
        return $this->currentCapacity;
    }

    /**
     * @param Capacity $capacity
     * @throws ShakerCapacityOverflowException
     */
    public function pourIn(Capacity $capacity)
    {
        if ($capacity->getMilliliters() > $this->capacity->getMilliliters()) {
            throw new ShakerCapacityOverflowException;
        }

        if ($capacity->getMilliliters() + $this->currentCapacity->getMilliliters() > $this->capacity->getMilliliters()) {
            throw new ShakerCapacityOverflowException;
        }

        $this->currentCapacity = $this->currentCapacity->add($capacity);
    }

    /**
     * @param Capacity $capacity
     * @return Capacity
     * @throws LiquidsNotShakedException
     * @throws NotEnoughLiquidException
     */
    public function pourOut(Capacity $capacity)
    {
        try {
            $newCapacity = $this->currentCapacity->subtract($capacity);
        } catch (InvalidArgumentException $e) {
            throw new NotEnoughLiquidException($this->currentCapacity);
        }
        
        if (!$this->shaked) {
            throw new LiquidsNotShakedException();
        }
        
        $this->currentCapacity = $newCapacity;
        
        return $capacity;
    }

    /**
     * @return Capacity
     */
    public function getAvailableCapacity()
    {
        return new Capacity($this->capacity->getMilliliters() - $this->currentCapacity->getMilliliters());
    }

    /**
     * @return bool
     */
    public function isShaked()
    {
        return $this->shaked;
    }

    public function shake()
    {
        if ($this->getCurrentCapacity()->getMilliliters() == 0) {
            throw new NotEnoughLiquidException($this->getAvailableCapacity());
        }
        $this->shaked = true;
    }

    /**
     * @return bool
     */
    public function isFilled()
    {
        return !is_null($this->filledWith);
    }

    /**
     * @param Name $content
     * @throws AlreadyFilledException
     */
    public function fillWith(Name $content)
    {
        if ($this->isFilled()) {
            throw new AlreadyFilledException($this->filledWith);
        }

        $this->filledWith = $content;
    }

    /**
     * @param Name $ingredientName
     * @param Amount $amount
     */
    public function addIngredient(Name $ingredientName, Amount $amount)
    {
        if (!array_key_exists((string) $ingredientName, $this->ingredients)) {
            $this->ingredients[(string) $ingredientName] = new Supply\Ingredient($ingredientName, $amount);
            return ;
        }

        $this->ingredients[(string) $ingredientName]->add($amount);
    }
}
