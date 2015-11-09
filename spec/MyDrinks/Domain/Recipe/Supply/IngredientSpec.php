<?php

namespace spec\MyDrinks\Domain\Recipe\Supply;

use MyDrinks\Domain\Recipe\Supply;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class IngredientSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new \MyDrinks\Domain\Name("Ice"), new Supply\Amount(5));
    }
    
    function it_is_supply()
    {
        $this->shouldImplement(Supply::class);
    }

    function it_has_name()
    {
        $this->getName()->__toString()->shouldReturn("Ice");
    }
    
    function it_allows_to_add_some_amount()
    {
        $ingredient = $this->add(new Supply\Amount(5));
        $ingredient->getName()->__toString()->shouldReturn("Ice");
        $ingredient->getAmount()->getValue()->shouldReturn(10);
    }
    
    
    function it_has_amount()
    {
        $this->getAmount()->getValue()->shouldReturn(5);
    }
}
