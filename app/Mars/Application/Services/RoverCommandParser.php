<?php

declare(strict_types=1);

namespace App\Mars\Application\Services;

use App\Mars\Application\Commands\MoveForwardCommand;
use App\Mars\Application\Commands\TurnLeftCommand;
use App\Mars\Application\Commands\TurnRightCommand;
use App\Mars\Ports\Input\RoverCommandInterface;

final class RoverCommandParser
{
    /**
     * @return RoverCommandInterface[]
     */
    public function parse(string $commands): array
    {
        return array_map(
            fn(string $command) => match ($command) {
                'M' => new MoveForwardCommand(),
                'L' => new TurnLeftCommand(),
                'R' => new TurnRightCommand(),
                default => throw new \InvalidArgumentException("Invalid command: {$command}")
            },
            str_split(strtoupper($commands))
        );
    }
}