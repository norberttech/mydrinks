<?php

namespace spec\MyDrinks\Application\SearchEngine;

use MyDrinks\Application\Exception\InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CriteriaSpec extends ObjectBehavior
{
    function it_is_empty_by_default()
    {
        $this->hasQuery()->shouldReturn(false);
    }

    function it_is_not_empty_when_query_is_added()
    {
        $this->addQuery("screwdriver");

        $this->hasQuery()->shouldReturn(true);
    }
}

