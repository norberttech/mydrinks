<?php

namespace MyDrinks\Domain\Recipe\BarAccessory;

use InvalidArgumentException;
use MyDrinks\Domain\Exception\Recipe\AlreadyFilledException;
use MyDrinks\Domain\Exception\Recipe\ContentAlreadyMuddledException;
use MyDrinks\Domain\Exception\Recipe\GlassIsAlreadyOnFireException;
use MyDrinks\Domain\Exception\Recipe\GlassCapacityOverflowException;
use MyDrinks\Domain\Exception\Recipe\EmptyVesselException;
use MyDrinks\Domain\Recipe\Supply;
use MyDrinks\Domain\Recipe\Supply\Amount;
use MyDrinks\Domain\Recipe\Supply\Capacity;
use MyDrinks\Domain\Name;

class Glass
{
    /**
     * @var Name
     */
    private $name;

    /**
     * @var Capacity
     */
    private $capacity;

    /**
     * @var Capacity
     */
    private $currentCapacity;
    
    /**
     * @var Amount
     */
    private $amount;

    /**
     * @var Name|null
     */
    private $filledWith;

    /**
     * @var bool
     */
    private $stirred;

    /**
     * @var bool
     */
    private $isOnFire;

    /**
     * @var array[]
     */
    private $ingredients;

    /**
     * @var Name|null
     */
    private $decoration;

    /**
     * @var boolean
     */
    private $muddled;
    
    /**
     * @param Name $name
     * @param Capacity $capacity
     * @param Amount $amount
     */
    public function __construct(Name $name, Capacity $capacity, Amount $amount = null)
    {
        if ($capacity->getMilliliters() == 0) {
            throw new InvalidArgumentException("You need to specify capacity greater than 0.");
        }
        
        $this->name = $name;
        $this->capacity = $capacity;
        $this->amount = is_null($amount) ? new Amount(1) : $amount;
        $this->stirred = false;
        $this->muddled = false;
        $this->isOnFire = false;
        $this->currentCapacity = new Capacity(0);
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
     * @return bool
     */
    public function isFull()
    {
        return $this->capacity->getMilliliters() == $this->currentCapacity->getMilliliters();
    }

    /**
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Capacity
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @return Amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return Capacity
     */
    public function getCurrentCapacity()
    {
        return $this->currentCapacity;
    }
    
    /**
     * @return Capacity
     */
    public function getTotalCapacity()
    {
        return new Capacity($this->getAmount()->getValue() * $this->getCapacity()->getMilliliters());
    }
    
    /**
     * @param Capacity $capacity
     * @return boolean
     */
    public function canPourIn(Capacity $capacity)
    {
        return $this->getTotalCapacity()->getMilliliters() >= $capacity->getMilliliters();
    }

    /**
     * @param Capacity $capacity
     * @throws GlassCapacityOverflowException
     * @throws GlassIsAlreadyOnFireException
     */
    public function pourIn(Capacity $capacity)
    {
        if ($this->isOnFire()) {
            throw new GlassIsAlreadyOnFireException;
        }
        
        if (!$this->canPourIn($capacity)) {
            throw new GlassCapacityOverflowException;
        }

        if (is_null($this->currentCapacity)) {
            $this->currentCapacity = $capacity;
            return;
        }

        if ($capacity->getMilliliters() + $this->currentCapacity->getMilliliters() > $this->getTotalCapacity()->getMilliliters()) {
            throw new GlassCapacityOverflowException;
        }

        $this->currentCapacity = $this->currentCapacity->add($capacity);
        $this->stirred = false;
        $this->muddled = false;
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
     * @throws GlassIsAlreadyOnFireException
     */
    public function fillWith(Name $content)
    {
        if ($this->isOnFire()) {
            throw new GlassIsAlreadyOnFireException;
        }
        
        if ($this->isFilled()) {
            throw new AlreadyFilledException($this->filledWith);
        }
        
        $this->filledWith = $content;
        $this->stirred = false;
    }

    /**
     * @throws EmptyVesselException
     */
    public function emptyTheContent()
    {
        if ($this->isEmpty()) {
            throw new EmptyVesselException();
        }
        
        $this->filledWith = null;
        $this->ingredients = [];
        $this->currentCapacity = new Capacity(0);
    }

    /**
     * @throws EmptyVesselException
     */
    public function stir()
    {
        if ($this->isEmpty()) {
            throw new EmptyVesselException();
        }
        
        $this->stirred = true;
    }

    /**
     * @return bool
     */
    public function isStirred()
    {
        return $this->stirred;
    }

    /**
     * @throws EmptyVesselException
     */
    public function ignite()
    {
        if ($this->isEmpty()) {
            throw new EmptyVesselException;
        }
        
        if ($this->isOnFire()) {
            throw new GlassIsAlreadyOnFireException;
        }

        $this->isOnFire = true;
    }

    /**
     * @return bool
     */
    public function isOnFire()
    {
        return $this->isOnFire;
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
        $this->muddled = false;
        $this->stirred = false;
    }

    /**
     * @param Name $decoration
     */
    public function garnish(Name $decoration)
    {
        $this->decoration = $decoration;
    }

    /**
     * @return bool
     */
    public function isDecorated()
    {
        return !is_null($this->decoration);
    }

    /**
     * @throws ContentAlreadyMuddledException
     * @throws EmptyVesselException
     */
    public function muddle()
    {
        if ($this->isEmpty()) {
            throw new EmptyVesselException;
        }
        
        if ($this->muddled) {
            throw new ContentAlreadyMuddledException;
        }
        
        $this->muddled = true;
    }

    /**
     * @throws GlassCapacityOverflowException
     * @throws GlassIsAlreadyOnFireException
     * @return Capacity
     */
    public function topUp()
    {
        if ($this->isFull()) {
            throw new GlassCapacityOverflowException;
        }
        
        $capacity = $this->getCapacity()->getMilliliters();
        $currentCapacity = $this->getCurrentCapacity()->getMilliliters();
     
        $capacityDiff = new Capacity($capacity - $currentCapacity);
        $this->pourIn($capacityDiff);
        
        return $capacityDiff;
    }
}
