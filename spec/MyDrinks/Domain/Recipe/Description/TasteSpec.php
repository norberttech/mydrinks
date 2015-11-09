<?php

namespace spec\MyDrinks\Domain\Recipe\Description;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TasteSpec extends ObjectBehavior
{
    function it_has_no_taste_by_default()
    {
        $this->isSweet()->shouldReturn(false);
        $this->isBitter()->shouldReturn(false);
        $this->isSour()->shouldReturn(false);
        $this->isSpicy()->shouldReturn(false);
        $this->isSalty()->shouldReturn(false);
        $this->isDefined()->shouldReturn(false);
    }
}
