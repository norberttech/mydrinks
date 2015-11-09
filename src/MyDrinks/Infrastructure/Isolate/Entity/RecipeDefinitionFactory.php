<?php

namespace MyDrinks\Infrastructure\Isolate\Entity;

use Isolate\Framework\UnitOfWork\Entity\Definition\Factory;
use Isolate\UnitOfWork\Entity\ClassName;
use Isolate\UnitOfWork\Entity\Definition;
use MyDrinks\Application\Recipe\Storage;
use MyDrinks\Application\SearchEngine;
use MyDrinks\Domain\Recipe;
use MyDrinks\Infrastructure\Isolate\Entity\Recipe\EditCommandHandler;
use MyDrinks\Infrastructure\Isolate\Entity\Recipe\IdentificationStrategy;
use MyDrinks\Infrastructure\Isolate\Entity\Recipe\NewCommandHandler;
use MyDrinks\Infrastructure\Isolate\Entity\Recipe\RemoveCommandHandler;

final class RecipeDefinitionFactory implements Factory
{
    /**
     * @var Storage
     */
    private $storage;
    
    /**
     * @var SearchEngine
     */
    private $searchEngine;

    /**
     * @param Storage $storage
     * @param SearchEngine $searchEngine
     */
    public function __construct(Storage $storage, SearchEngine $searchEngine) 
    {
        $this->storage = $storage;
        $this->searchEngine = $searchEngine;
    }
    
    /**
     * @return Definition
     */
    public function createDefinition()
    {
        $definition = new Definition(
            new ClassName(Recipe::class),
            new Definition\Identity("name"),
            new IdentificationStrategy($this->storage)
        );

        $definition->setObserved([
            new Definition\Property("steps"),
            new Definition\Property("publicationDate"),
            new Definition\Property("description")
        ]);

        $definition->setEditCommandHandler(new EditCommandHandler($this->storage, $this->searchEngine));
        $definition->setNewCommandHandler(new NewCommandHandler($this->storage, $this->searchEngine));
        $definition->setRemoveCommandHandler(new RemoveCommandHandler($this->storage, $this->searchEngine));
        
        return $definition;
    }
}