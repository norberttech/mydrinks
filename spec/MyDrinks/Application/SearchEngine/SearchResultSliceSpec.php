<?php

namespace spec\MyDrinks\Application\SearchEngine;

use MyDrinks\Application\Exception\InvalidArgumentException;
use MyDrinks\Application\SearchEngine\Criteria;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SearchResultSliceSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new Criteria(0), [], 0);
    }
    
    function it_throws_exception_when_at_least_one_of_item_is_not_valid_search_result()
    {
        $this->shouldThrow(InvalidArgumentException::class)->during("__construct", [new Criteria(), [new \DateTime], 10]);
    }
    
    function it_can_be_empty()
    {
        $this->isEmpty()->shouldReturn(true);
    }
    
    function it_can_calculate_pages_count()
    {
        $this->beConstructedWith(new Criteria(0), [], 101);
        
        $this->getPagesCount()->shouldReturn(10);
    }

    function it_can_calculate_current_page()
    {
        $this->beConstructedWith(new Criteria(51), [], 100);

        $this->getCurrentPage()->shouldReturn(5);
    }
}
