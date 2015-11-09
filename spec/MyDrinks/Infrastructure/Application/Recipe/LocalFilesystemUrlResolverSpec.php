<?php

namespace spec\MyDrinks\Infrastructure\Application\Recipe;

use PhpSpec\ObjectBehavior;

final class LocalFilesystemUrlResolverSpec extends ObjectBehavior
{
    function it_generates_relative_image_path_using_prefix()
    {
        $this->beConstructedWith('/public/assets/images');
        $this->resolveUrlFor('image.jpg')->shouldReturn('/public/assets/images/image.jpg');
    }
}