<?php

namespace MyDrinks\Application;

interface CommandBus 
{
    /**
     * @param $command
     */
    public function handle($command);
}