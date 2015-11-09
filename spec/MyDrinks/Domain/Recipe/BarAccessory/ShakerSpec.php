<?php

namespace spec\MyDrinks\Domain\Recipe\BarAccessory;

use MyDrinks\Domain\Exception\Recipe\AlreadyFilledException;
use MyDrinks\Domain\Exception\InvalidArgumentException;
use MyDrinks\Domain\Exception\Recipe\LiquidsNotShakedException;
use MyDrinks\Domain\Exception\Recipe\NotEnoughLiquidException;
use MyDrinks\Domain\Exception\Recipe\ShakerCapacityOverflowException;
use MyDrinks\Domain\Recipe\Supply;
use MyDrinks\Domain\Recipe\Supply\Capacity;
use MyDrinks\Domain\Name;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ShakerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new Capacity(250));
    }
    
    function it_throws_exception_when_created_with_0_capacity()
    {
        $this->shouldThrow(InvalidArgumentException::class)
            ->during("__construct", [new Capacity(0)]);
    }

    function it_has_capacity()
    {
        $this->getCapacity()->getMilliliters()->shouldReturn(250);
    }

    function it_is_not_shaked_by_defualt()
    {
        $this->isShaked()->shouldReturn(false);
    }
        
    function it_throws_exception_when_shaking_empty_shaker()
    {
        $this->shouldThrow(NotEnoughLiquidException::class)->during("shake");
    }
        
    
    function it_throws_exception_when_over_filled()
    {
        $this->shouldThrow(ShakerCapacityOverflowException::class)
            ->during("pourIn", [new Capacity(400)]);

        $this->pourIn(new Capacity(100));
        $this->shouldThrow(ShakerCapacityOverflowException::class)
            ->during("pourIn", [new Capacity(200)]);
    }
    
    function it_knows_that_even_salomon_cant_pour_from_an_empty_shaker()
    {
        $this->shouldThrow(NotEnoughLiquidException::class)
            ->during("pourOut", [new Capacity(250)]);
    }
    
    function it_throws_exception_when_pouring_out_not_shaked_contents()
    {
        $this->pourIn(new Capacity(100));
        $this->shouldThrow(LiquidsNotShakedException::class)->during('pourOut', [new Capacity(100)]);
    }
    
    function it_increase_available_capacity_after_pour_out()
    {
        $this->pourIn(new Capacity(100));
        $this->getAvailableCapacity()->getMilliliters()->shouldReturn(150);

        $this->shake();
        $this->pourOut(new Capacity(80));
        
        $this->getAvailableCapacity()->getMilliliters()->shouldReturn(230);
    }
    
    function it_can_be_shaked_in_order_to_mix_contents()
    {
        $this->pourIn(new Capacity(100));
        $this->shake();
        $this->isShaked()->shouldReturn(true);
    }

    function it_throws_exception_when_filled_twice()
    {
        $this->isFilled()->shouldReturn(false);
        $this->fillWith(new Name("Ice"));
        $this->isFilled()->shouldReturn(true);

        $this->shouldThrow(AlreadyFilledException::class)->during("fillWith", [new Name("Ice")]);
    }

    function it_accept_adding_ingredients()
    {
        $this->addIngredient(new Name("orange quarter"), new Supply\Amount(1));

        $this->isEmpty()->shouldReturn(false);
    }
}

