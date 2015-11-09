<?php

namespace MyDrinks\Application\AutoComplete\Item;

class Supply 
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $polishName;

    public function __construct($id, $polishName)
    {
        $this->id = $id;
        $this->polishName = $polishName;
    }

    /**
     * @return string
     */
    public function getPolishName()
    {
        return $this->polishName;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        $data = explode('.', $this->id);
        if (count($data)) {
            return current($data);
        }
        
        return 'unknown';
    }
}