<?php

namespace MyDrinks\Application;

interface Serializer 
{
    /**
     * @param $object
     * @return string
     */
    public function serialize($object);

    /**
     * @param string $data
     * @param string $class
     * @return mixed
     */
    public function deserialize($data, $class);
}