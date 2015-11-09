<?php

namespace spec\MyDrinks\Domain\Recipe\Supply;

use MyDrinks\Domain\Exception\InvalidArgumentException;
use MyDrinks\Domain\Recipe\Supply\Amount;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AmountSpec extends ObjectBehavior
{
    function it_can_be_created_only_from_integer_value()
    {
        $this->shouldThrow(InvalidArgumentException::class)->during("__construct", ["string"]);
    }

    function it_cant_be_created_from_non_positive_value()
    {
        $this->shouldThrow(InvalidArgumentException::class)->during("__construct", [-120]);
    }
    
    function it_can_be_added_to_another_amount()
    {
        $this->beConstructedWith(100);
        $amount = $this->add(new Amount(100));
        
        $amount->getValue()->shouldReturn(200);
    }
}
