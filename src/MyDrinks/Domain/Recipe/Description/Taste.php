<?php

namespace MyDrinks\Domain\Recipe\Description;

class Taste
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

    /**
     * @param bool|false $sweet
     * @param bool|false $bitter
     * @param bool|false $sour
     * @param bool|false $spicy
     * @param bool|false $salty
     */
    public function __construct($sweet = false, $bitter = false, $sour = false, $spicy = false, $salty = false)
    {
        $this->sweet = $sweet;
        $this->bitter = $bitter;
        $this->sour = $sour;
        $this->spicy = $spicy;
        $this->salty = $salty;
    }

    /**
     * @return bool
     */
    public function isSweet()
    {
        return $this->sweet;
    }

    /**
     * @return bool
     */
    public function isBitter()
    {
        return $this->bitter;
    }

    /**
     * @return bool
     */
    public function isSour()
    {
        return $this->sour;
    }

    /**
     * @return bool
     */
    public function isSpicy()
    {
        return $this->spicy;
    }

    /**
     * @return bool
     */
    public function isSalty()
    {
        return $this->salty;
    }

    /**
     * @return bool
     */
    public function isDefined()
    {
        return $this->sweet || $this->salty || $this->bitter || $this->spicy || $this->sour;
    }
}
