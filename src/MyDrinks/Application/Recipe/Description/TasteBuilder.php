<?php

namespace MyDrinks\Application\Recipe\Description;

use MyDrinks\Domain\Recipe\Description\Taste;

final class TasteBuilder
{
    /**
     * @var bool
     */
    private $sweet;

    /**
     * @var bool
     */
    private $bitter;

    /**
     * @var bool
     */
    private $sour;

    /**
     * @var bool
     */
    private $spicy;

    /**
     * @var bool
     */
    private $salty;
    
    public function __construct()
    {
        $this->sweet = false;
        $this->bitter = false;
        $this->sour = false;
        $this->spicy = false;
        $this->salty = false;
    }
    
    public function sweet()
    {
        $this->sweet = true;
        
        return $this;
    }
    
    public function bitter()
    {
        $this->bitter = true;
        
        return $this;
    }
    
    public function sour()
    {
        $this->sour = true;
        
        return $this;
    }
    
    public function spicy()
    {
        $this->spicy = true;
        
        return $this;
    }
    
    public function salty()
    {
        $this->salty = true;
        
        return $this;
    }
    
    public function buildTaste()
    {
        return new Taste($this->sweet, $this->bitter, $this->sour, $this->spicy, $this->salty);
    }
}