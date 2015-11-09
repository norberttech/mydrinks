<?php

namespace MyDrinks\Application;

use MyDrinks\Application\AutoComplete\Item\Supply;

interface AutoComplete 
{
    /**
     * @param Supply $supply
     * @return mixed
     */
    public function indexSupply(Supply $supply);
    
    /**
     * @param string $namePartial
     * @param int $limit
     * @return  []
     */
    public function supply($namePartial, $limit = 10);

    /**
     * @param string $ingredientNamePartial
     * @param int $limit
     * @return  []
     */
    public function ingredient($ingredientNamePartial, $limit = 10);

    /**
     * @param $glassNamePartial
     * @param int $limit
     * @return []
     */
    public function glass($glassNamePartial, $limit = 10);
}