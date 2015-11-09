<?php

namespace spec\MyDrinks\Domain\Recipe;

use MyDrinks\Domain\Exception\InvalidArgumentException;
use MyDrinks\Domain\Name;
use MyDrinks\Domain\Recipe\Step;
use MyDrinks\Domain\Recipe\Supply\Amount;
use MyDrinks\Domain\Recipe\Supply\Capacity;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StepsSpec extends ObjectBehavior
{
    function it_allows_to_array_access()
    {
        $this->shouldImplement(\ArrayAccess::class);
    }
    
    function it_is_traversable()
    {
        $this->shouldImplement(\Traversable::class);
    }

    function it_is_countable()
    {
        $this->shouldImplement(\Countable::class);
    }
    
    function it_allows_to_add_only_steps(Step $step)
    {
        $this->add($step);
        $this->count()->shouldReturn(1);
    }
    
    function it_throws_exception_when_value_is_not_valid_step()
    {
        $this->shouldThrow(InvalidArgumentException::class)->during("offsetSet", [0, "not step"]);
        $this->shouldThrow(InvalidArgumentException::class)->during("offsetSet", [0, new \DateTime()]);
    }
    
    function it_returns_liquids_used_in_steps()
    {
        $this->add(new Step\AddIngredientIntoGlass(new Name("ice cube"), new Amount(2)));
        $this->add(new Step\PourIntoGlass(new Name("vodka"), new Capacity(50)));
        $this->add(new Step\PourIntoGlass(new Name("vodka"), new Capacity(50)));
        $this->add(new Step\PourIntoShaker(new Name("orange juice"), new Capacity(50)));
        $this->add(new Step\TopUpGlass(new Name("whisky"), new Capacity(50)));
        
        $this->getLiquids()->shouldHaveCount(3);
        $this->getLiquids()[0]->getName()->__toString()->shouldReturn("vodka");
        $this->getLiquids()[1]->getName()->__toString()->shouldReturn("orange juice");
        $this->getLiquids()[2]->getName()->__toString()->shouldReturn("whisky");
    }

    function it_returns_ingredients_steps()
    {
        $this->add(new Step\PourIntoGlass(new Name("vodka"), new Capacity(50)));
        $this->add(new Step\AddIngredientIntoGlass(new Name("ice cube"), new Amount(2)));
        $this->add(new Step\AddIngredientIntoGlass(new Name("ice cube"), new Amount(3)));
        $this->add(new Step\AddIngredientIntoShaker(new Name("ice cube"), new Amount(5)));

        $this->getIngredients()->shouldHaveCount(1);
        $this->getIngredients()[0]->getName()->__toString()->shouldReturn("ice cube");
    }

    function it_returns_ingredients_with_items_used_to_fill_up()
    {
        $this->add(new Step\FillGlass(new Name("ice")));
        $this->add(new Step\FillShaker(new Name("ice")));

        $this->getIngredients()->shouldHaveCount(1);
        $this->getIngredients()[0]->getName()->__toString()->shouldReturn("ice");
        $this->getIngredients()[0]->getAmount()->getValue()->shouldReturn(0);
    }
}
