<?php

namespace spec\MyDrinks\Domain\Recipe\Supply;

use MyDrinks\Domain\Recipe\Supply;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LiquidSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new \MyDrinks\Domain\Name("Vodka"), new Supply\Capacity(50));
    }
    
    function it_is_supply()
    {
        $this->shouldImplement(Supply::class);
    }
    
    function it_has_name()
    {
        $this->getName()->__toString()->shouldReturn("Vodka");
    }
    
    function it_can_be_filled()
    {
        $liquid = $this->fill(new Supply\Capacity(100));
        $liquid->getName()->__toString()->shouldReturn("Vodka");
        $liquid->getCapacity()->getMilliliters()->shouldReturn(150);
    }
    
    function it_has_capacity()
    {
        $this->getCapacity()->getMilliliters()->shouldReturn(50);
    }
}
