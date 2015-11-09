<?php

namespace MyDrinks\Infrastructure\Application\CommandBus\Adapter;

use League\Tactician\CommandBus;
use MyDrinks\Application\CommandBus as BaseCommandBus;

final class Tactician implements BaseCommandBus
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * @param $command
     */
    public function handle($command)
    {
        $this->commandBus->handle($command);
    }
}
