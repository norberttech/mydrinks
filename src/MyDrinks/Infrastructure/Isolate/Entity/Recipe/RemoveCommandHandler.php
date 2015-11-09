<?php

namespace MyDrinks\Infrastructure\Isolate\Entity\Recipe;

use Isolate\UnitOfWork\Command\RemoveCommand;
use Isolate\UnitOfWork\Command\RemoveCommandHandler as BaseHandler;
use MyDrinks\Application\Recipe\Storage;
use MyDrinks\Application\SearchEngine;
use MyDrinks\Domain\Recipe;

final class RemoveCommandHandler implements BaseHandler 
{
    /**
     * @var Storage
     */
    private $storage;
    /**
     * @var SearchEngine
     */
    private $engine;

    /**
     * @param Storage $storage
     * @param SearchEngine $engine
     */
    public function __construct(Storage $storage, SearchEngine $engine)
    {
        $this->storage = $storage;
        $this->engine = $engine;
    }
    
    /**
     * @param RemoveCommand $command
     */
    public function handle(RemoveCommand $command)
    {
        $recipe = $command->getEntity();
        if (!$recipe instanceof Recipe) {
            throw new \RuntimeException(sprintf("Entity \"%s\" can't be handled by Recipe\\NewCommandHandler", get_class($recipe)));
        }
        
        $this->storage->remove($recipe);
        
        $this->engine->removeRecipeFromIndex($recipe);
    }
}