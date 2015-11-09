<?php

namespace spec\MyDrinks\Domain\Recipe;

use MyDrinks\Domain\Exception\InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DescriptionSpec extends ObjectBehavior
{
    function it_is_not_iba_drink_by_default()
    {
        $this->isOfficialIBA()->shouldReturn(false);
    }
    
    function it_does_not_have_description_by_default()
    {
        $this->hasText()->shouldReturn(false);
    }
    
    function it_throws_exception_when_trying_to_describe_with_empty_string()
    {
        $this->shouldThrow(InvalidArgumentException::class)->during("setText", [""]);
    }

    function it_throws_exception_when_trying_to_describe_with_non_string()
    {
        $this->shouldThrow(InvalidArgumentException::class)->during("setText", [""]);
    }
    
    function it_doesnt_have_alcohol_content_by_default()
    {
        $this->hasKnownAlcoholContent()->shouldReturn(false);
    }
    
    function it_throws_exception_when_acohol_content_is_not_valid_percent_value()
    {
        $this->shouldThrow(InvalidArgumentException::class)->during("setAlcoholContent", [101]);
    }
}
