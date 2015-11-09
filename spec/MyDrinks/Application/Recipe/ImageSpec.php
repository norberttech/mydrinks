<?php

namespace spec\MyDrinks\Application\Recipe;

use MyDrinks\Application\Exception\InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ImageSpec extends ObjectBehavior
{
    function it_throws_exception_when_name_contains_forbidden_characters()
    {
        $this->shouldThrow(InvalidArgumentException::class)
            ->during("__construct", ["file\\name", "imagecontent"]);
    }
    
    function it_throws_exception_when_content_is_empty()
    {
        $this->shouldThrow(InvalidArgumentException::class)
            ->during("__construct", ["image.jpg", ""]);
    }

    function it_throws_exception_image_name_does_not_have_extension()
    {
        $this->shouldThrow(InvalidArgumentException::class)
            ->during("__construct", ["image", "image_content"]);
    }

    function it_throws_exception_for_image_with_not_supported_extension()
    {
        $this->shouldThrow(InvalidArgumentException::class)
            ->during("__construct", ["image.png", "image_content"]);
    }
}
