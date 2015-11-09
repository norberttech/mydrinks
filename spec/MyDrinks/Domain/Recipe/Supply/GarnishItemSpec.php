<?php

namespace spec\MyDrinks\Domain\Recipe\Supply;

use MyDrinks\Domain\Recipe\Supply;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GarnishItemSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new \MyDrinks\Domain\Name("Orange slice"), new Supply\Amount(1));
    }

    function it_is_supply()
    {
        $this->shouldImplement(Supply::class);
    }

    function it_has_name()
    {
        $this->getName()->__toString()->shouldReturn("Orange slice");
    }

    function it_has_amount()
    {
        $this->getAmount()->getValue()->shouldReturn(1);
    }
}
