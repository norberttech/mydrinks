<?php

namespace MyDrinks\Domain;

use MyDrinks\Domain\Exception\InvalidArgumentException;

final class Name
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     * @throws InvalidArgumentException
     */
    public function __construct($name)
    {
        if (!is_string($name) || empty($name)) {
            throw new InvalidArgumentException("Name can't be created from non string or empty value.");
        }
        
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @param Name $name
     * @return bool
     */
    public function isEqual(Name $name)
    {
        return strtolower($this->name) === strtolower($name->name);
    }
}
