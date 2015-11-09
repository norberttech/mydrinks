<?php

namespace MyDrinks\Infrastructure\UserInterface\Serializer;

use MyDrinks\Application\Exception\InvalidArgumentException;
use MyDrinks\Application\Serializer;
use MyDrinks\Domain\Recipe;
use MyDrinks\Infrastructure\UserInterface\RecipeConverter as RecipeConverter;
use MyDrinks\Infrastructure\UserInterface\RecipeHydrator as RecipeHydrator;

class JsonSerializer implements Serializer
{
    /**
     * @var RecipeConverter
     */
    private $recipeConverter;

    /**
     * @var RecipeHydrator
     */
    private $recipeHydrator;
    
    public function __construct()
    {
        $this->recipeConverter = new RecipeConverter();
        $this->recipeHydrator = new RecipeHydrator();
    }
    
    /**
     * @param $object
     * @return string
     * @throws InvalidArgumentException
     */
    public function serialize($object)
    {
        if ($object instanceof Recipe) {
            return json_encode($this->recipeConverter->toArray($object));
        }

        throw new InvalidArgumentException(sprintf("Unsupported object \"%s\".", get_class($object)));
    }

    /**
     * @param string $data
     * @param string $class
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function deserialize($data, $class)
    {
        if (!is_string($class) || !class_exists($class)) {
            throw new InvalidArgumentException(sprintf("Invalid class\"%s\"", $class));
        }
        
        if ($class === Recipe::class) {
            return $this->recipeHydrator->hydrate(json_decode($data, true));
        }

        throw new InvalidArgumentException(sprintf("Unsupported class \"%s\".", $class));
    }
}
