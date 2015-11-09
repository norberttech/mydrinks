<?php

namespace spec\MyDrinks\Domain\Recipe\Supply;

use MyDrinks\Domain\Exception\InvalidArgumentException;
use MyDrinks\Domain\Recipe\Supply\Capacity;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CapacitySpec extends ObjectBehavior
{
    function it_can_be_created_only_from_integer_value()
    {
        $this->shouldThrow(InvalidArgumentException::class)->during("__construct", ["string"]);
    }
    
    function it_cant_be_created_from_non_positive_value()
    {
        $this->shouldThrow(InvalidArgumentException::class)->during("__construct", [-120]);
    }
    
    function it_is_stored_in_milliliters()
    {
        $this->beConstructedWith(100);
        $this->getMilliliters()->shouldReturn(100);
    }
    
    function it_can_be_expanded()
    {
        $this->beConstructedWith(100);
        $capacity = $this->add(new Capacity(100));
        $capacity->shouldBeAnInstanceOf(Capacity::class);
        $capacity->getMilliliters()->shouldReturn(200);
    }
}
