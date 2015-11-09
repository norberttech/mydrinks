<?php

namespace MyDrinks\Infrastructure\Isolate\Entity\Recipe;

use Isolate\UnitOfWork\Entity\Definition\IdentificationStrategy as BaseIdentificationStrategy;
use MyDrinks\Application\Recipe\Storage;

class IdentificationStrategy implements BaseIdentificationStrategy
{
    /**
     * @var Storage
     */
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }
    
    /**
     * @param mixed $entity
     * @return boolean
     */
    public function isIdentified($entity)
    {
        return $this->storage->hasRecipeWithName($entity->getName());
    }

    /**
     * @param $entity
     * @return mixed
     */
    public function getIdentity($entity)
    {
        return (string) $entity->getName();
    }
}