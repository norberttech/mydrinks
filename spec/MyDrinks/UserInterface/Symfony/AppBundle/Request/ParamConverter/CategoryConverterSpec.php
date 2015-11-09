<?php

namespace spec\MyDrinks\UserInterface\Symfony\AppBundle\Request\ParamConverter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryConverterSpec extends ObjectBehavior
{
    function it_is_param_converter()
    {
        $this->shouldBeAnInstanceOf(ParamConverterInterface::class);
    }
    
    function it_throws_exception_when_created_with_non_category()
    {
        $this->shouldThrow(NotFoundHttpException::class)
            ->during("__construct", [[new \DateTime], []]);

        $this->shouldThrow(NotFoundHttpException::class)
            ->during("__construct", [["string"], []]);

        $this->shouldThrow(NotFoundHttpException::class)
            ->during("__construct", [[123]], []);
    }
}
