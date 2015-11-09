<?php

namespace spec\MyDrinks\Application\Handler;

use MyDrinks\Application\Command\UploadRecipeImageCommand;
use MyDrinks\Application\Exception\Recipe\RecipeNotFoundException;
use MyDrinks\Application\Filesystem;
use MyDrinks\Application\Recipe\Image;
use MyDrinks\Application\Recipe\ImageStorage;
use MyDrinks\Application\Recipes;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe;
use MyDrinks\Tests\Doubles\FakeFile;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UploadRecipeImageHandlerSpec extends ObjectBehavior
{
    function let(Recipes $recipes, ImageStorage $imageStorage, Filesystem $filesystem)
    {
        $this->beConstructedWith($recipes, $imageStorage, $filesystem);
    }
    
    function it_throws_exception_when_recipe_with_slug_does_not_exists()
    {
        $command = new UploadRecipeImageCommand();
        $command->slug = 'invalid';

        $this->shouldThrow(RecipeNotFoundException::class)->during("handle", [$command]);
    }
    
    function it_updates_recipe_description(Recipes $recipes, ImageStorage $imageStorage, Filesystem $filesystem)
    {
        $recipes->findBySlug("screwdriver")->willReturn(new Recipe(new Name("Screwdriver")));

        $command = new UploadRecipeImageCommand();
        $command->slug = 'screwdriver';
        $command->image = new FakeFile("fake_image.jpg");
        $command->extension = 'jpg';
        
        $filesystem->read('fake_image.jpg')->willReturn('content');

        $imageStorage->saveImageFor(Argument::type(Image::class), "screwdriver")->shouldBeCalled();
        
        $this->handle($command);
    }
}
