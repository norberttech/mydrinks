<?php

namespace spec\MyDrinks\Infrastructure\Application\CommandBus\Adapter;

use MyDrinks\Application\CommandBus;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use League\Tactician\CommandBus as TacticianBus;

class TacticianSpec extends ObjectBehavior
{
    function let(TacticianBus $commandBus)
    {
        $this->beConstructedWith($commandBus);
    }
    
    function it_is_command_bus()
    {
        $this->shouldImplement(CommandBus::class);
    }
    
    function it_handle_commands_using_tactician(TacticianBus $commandBus)
    {
        $command = new \stdClass;
        $commandBus->handle($command)->shouldBeCalled();
        
        $this->handle($command);
    }
}
