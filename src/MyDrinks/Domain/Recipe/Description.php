<?php

namespace MyDrinks\Domain\Recipe;

use MyDrinks\Domain\Exception\InvalidArgumentException;
use MyDrinks\Domain\Recipe\Description\Taste;

class Description
{
    /**
     * @var boolean
     */
    private $officialIBA;

    /**
     * @var string|null
     */
    private $text;

    /**
     * @var int|null
     */
    private $alcoholContent;

    /**
     * @var Taste
     */
    private $taste; 
    
    public function __construct()
    {
        $this->officialIBA = false;
        $this->taste = new Taste();
    }

    public function markAsIBAOfficial()
    {
        $this->officialIBA = true;
    }
    
    /**
     * @return bool
     */
    public function isOfficialIBA()
    {
        return $this->officialIBA;
    }

    /**
     * @return bool
     */
    public function hasText()
    {
        return !is_null($this->text);
    }

    /**
     * @param string $text
     * @throws InvalidArgumentException
     */
    public function setText($text)
    {
        if (empty($text) || !is_string($text)) {
            throw new InvalidArgumentException("Description text needs to be not empty string.");
        }
        
        $this->text = $text;;
    }

    /**
     * @return null|string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return bool
     */
    public function hasKnownAlcoholContent()
    {
        return !is_null($this->alcoholContent);
    }

    /**
     * @param int $percent
     * @throws InvalidArgumentException
     */
    public function setAlcoholContent($percent)
    {
        if (!is_integer($percent) || $percent < 0 || $percent > 100) {
            throw new InvalidArgumentException(
                "Alcohol content needs to be valid integer lower than 100 an greater than 0."
            );
        }
        
        $this->alcoholContent = $percent;
    }

    /**
     * @return int|null
     */
    public function getAlcoholContent()
    {
        return $this->alcoholContent;
    }

    /**
     * @param Taste $taste
     */
    public function changeTaste(Taste $taste)
    {
        $this->taste = $taste;
    }

    /**
     * @return Taste
     */
    public function getTaste()
    {
        return $this->taste;
    }
}
