<?php

namespace MyDrinks\Infrastructure\Isolate\Entity\Recipe;

use Isolate\UnitOfWork\Command\EditCommand;
use Isolate\UnitOfWork\Command\EditCommandHandler as BaseHandler;
use MyDrinks\Application\Recipe\Storage;
use MyDrinks\Application\SearchEngine;
use MyDrinks\Domain\Recipe;

final class EditCommandHandler implements BaseHandler 
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
     * @param EditCommand $command
     */
    public function handle(EditCommand $command)
    {
        $recipe = $command->getEntity();
        
        if (!$recipe instanceof Recipe) {
            throw new \RuntimeException(sprintf("Entity \"%s\" can't be handled by Recipe\\EditCommandHandler", get_class($recipe)));
        }
        
        $this->storage->save($recipe);
        
        $this->engine->indexRecipe($recipe);
    }
}