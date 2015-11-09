<?php

namespace spec\MyDrinks\Domain;

use MyDrinks\Domain\Exception\InvalidArgumentException;
use MyDrinks\Domain\Name;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NameSpec extends ObjectBehavior
{
    function it_throws_exception_when_created_from_non_string()
    {
        $this->shouldThrow(InvalidArgumentException::class)->during('__construct', [new \DateTime()]);
    }

    function it_throws_exception_when_created_empty_string()
    {
        $this->shouldThrow(InvalidArgumentException::class)->during('__construct', [""]);
    }
    
    function it_can_be_caster_to_string()
    {
        $this->beConstructedWith("Vodka");
        $this->__toString()->shouldReturn("Vodka");
    }
    
    function it_can_be_compared_against_other_name_ignoring_uppercase()
    {
        $this->beConstructedWith("VODKA");
        $this->isEqual(new Name("vodka"))->shouldReturn(true);
    }
}
