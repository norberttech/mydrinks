<?php

namespace spec\MyDrinks\Infrastructure\UserInterface\SlugGenerator\Adapter;

use Cocur\Slugify\Slugify;
use MyDrinks\Application\SlugGenerator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SlugifyAdapterSpec extends ObjectBehavior
{
    function let(Slugify $slugify)
    {
        $this->beConstructedWith($slugify);
    }
    
    function it_is_slug_generator()
    {
        $this->shouldImplement(SlugGenerator::class);
    }
    
    function it_generate_slugs_using_slugify(Slugify $slugify)
    {
        $slugify->slugify('żąćź')->willReturn('zacz');
        $this->generateFrom("żąćź")->shouldReturn('zacz');
    }
}
