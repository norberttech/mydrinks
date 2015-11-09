<?php

namespace spec\MyDrinks\Domain\Recipe\BarAccessory;

use MyDrinks\Domain\Exception\Recipe\AlreadyFilledException;
use MyDrinks\Domain\Exception\Recipe\ContentAlreadyMuddledException;
use MyDrinks\Domain\Exception\Recipe\GlassIsAlreadyOnFireException;
use MyDrinks\Domain\Exception\Recipe\GlassCapacityOverflowException;
use MyDrinks\Domain\Exception\Recipe\EmptyVesselException;
use MyDrinks\Domain\Recipe\Supply;
use MyDrinks\Domain\Recipe\Supply\Capacity;
use MyDrinks\Domain\Name;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GlassSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new Name("Highball"), new Capacity(250));
    }

    function it_has_amount_1_by_default()
    {
        $this->getAmount()->getValue()->shouldReturn(1);
    }
    
    function it_has_name()
    {
        $this->getName()->__toString()->shouldReturn("Highball");
    }

    function it_has_capacity()
    {
        $this->getCapacity()->getMilliliters()->shouldReturn(250);
    }
    
    function it_cant_be_filled_with_more_liquids_than_capacity_limit()
    {
        $this->canPourIn(new Capacity(300))->shouldReturn(false);
    }

    function it_can_be_filled_with_less_liquids_than_capacity_limit()
    {
        $this->canPourIn(new Capacity(100))->shouldReturn(true);
    }

    function it_throws_exception_when_over_filled()
    {
        $this->shouldThrow(GlassCapacityOverflowException::class)
            ->during("pourIn", [new Capacity(400)]);

        $this->pourIn(new Capacity(100));
        $this->shouldThrow(GlassCapacityOverflowException::class)
            ->during("pourIn", [new Capacity(200)]);
    }
    
    function it_throws_exception_when_filled_twice()
    {
        $this->isFilled()->shouldReturn(false);
        $this->fillWith(new Name("Ice"));
        $this->isFilled()->shouldReturn(true);
        
        $this->shouldThrow(AlreadyFilledException::class)->during("fillWith", [new Name("Ice")]);
    }
    
    function it_throws_exception_when_emptying_an_empty_glass()
    {
        $this->shouldThrow(EmptyVesselException::class)->during("emptyTheContent");
    }
    
    function it_empty_the_content_that_was_previously_filled()
    {
        $this->fillWith(new Name("Ice"));
        $this->emptyTheContent();
        $this->isFilled()->shouldReturn(false);
    }
    
    function it_cant_be_stirred_when_its_empty()
    {
        $this->shouldThrow(EmptyVesselException::class)->during("stir");
    }
    
    function it_allows_to_stir_the_liquid()
    {
        $this->pourIn(new Capacity(100));
        $this->stir();
        $this->isStirred()->shouldReturn(true);
    }

    function it_allows_to_stir_twice_after_pouring_some_liquid()
    {
        $this->pourIn(new Capacity(100));
        $this->stir();
        $this->pourIn(new Capacity(50));
        $this->isStirred()->shouldReturn(false);
    }
    
    function it_cant_be_ignited_when_its_empty()
    {
        $this->shouldThrow(EmptyVesselException::class)->during("ignite");
    }

    function it_can_be_on_fire()
    {
        $this->pourIn(new Capacity(100));
        $this->ignite();
        $this->isOnFire()->shouldReturn(true);
    }

    function it_canot_be_ignited_twice()
    {
        $this->pourIn(new Capacity(100));
        $this->ignite();
        $this->shouldThrow(GlassIsAlreadyOnFireException::class)->during("ignite");
    }
    
    function it_disallow_to_pour_in_when_its_on_fire()
    {
        $this->pourIn(new Capacity(100));
        $this->ignite();
        $this->shouldThrow(GlassIsAlreadyOnFireException::class)->during("pourIn", [new Capacity(50)]);
    }

    function it_disallow_to_fill_when_its_on_fire()
    {
        $this->fillWith(new Name("Ice"));
        $this->pourIn(new Capacity(50));
        $this->ignite();
        $this->shouldThrow(GlassIsAlreadyOnFireException::class)->during("fillWith", [new Name("Ice")]);
    }
    
    function it_accept_adding_ingredients()
    {
        $this->addIngredient(new Name("orange quarter"), new Supply\Amount(1));
        
        $this->isEmpty()->shouldReturn(false);
    }
    
    function it_can_be_decorated()
    {
        $this->garnish(new Name("orange slice"));
        $this->isDecorated()->shouldReturn(true);
    }
    
    function it_throws_exception_when_muddled_empty()
    {
        $this->shouldThrow(EmptyVesselException::class)->during("muddle");
    }
    
    function it_throws_exception_when_muddled_twice()
    {
        $this->addIngredient(new Name("orange quarter"), new Supply\Amount(3));
        $this->muddle();
        $this->shouldThrow(ContentAlreadyMuddledException::class)->during("muddle");
    }

    function it_can_be_top_up()
    {
        $this->topUp();
        $this->getCurrentCapacity()->getMilliliters()->shouldReturn(250);
        $this->isFull()->shouldReturn(true);
    }

    function it_throws_exception_when_glass_is_full()
    {
        $this->topUp();
        $this->shouldThrow(GlassCapacityOverflowException::class)->during("topUp");
    }
}

