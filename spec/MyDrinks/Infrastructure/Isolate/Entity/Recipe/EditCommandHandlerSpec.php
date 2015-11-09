<?php

namespace spec\MyDrinks\Infrastructure\Isolate\Entity\Recipe;

use Isolate\UnitOfWork\Command\EditCommand;
use Isolate\UnitOfWork\Entity\Value\ChangeSet;
use MyDrinks\Application\Recipe\Storage;
use MyDrinks\Application\SearchEngine;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EditCommandHandlerSpec extends ObjectBehavior
{
    function let(Storage $storage, SearchEngine $searchEngine)
    {
        $this->beConstructedWith($storage, $searchEngine);
    }
    
    function it_save_and_index_updated_recipes(Storage $storage, SearchEngine $searchEngine)
    {
        $recipe = new Recipe(new Name("Screwdriver"));
        $recipe->publish();
        $command = new EditCommand($recipe, new ChangeSet());

        $searchEngine->indexRecipe(Argument::any())->shouldBeCalled();
        $storage->save(Argument::type(Recipe::class))->shouldBeCalled();

        $this->handle($command);
    }
}
