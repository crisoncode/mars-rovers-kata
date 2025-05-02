<?php

declare(strict_types=1);

namespace App\Mars\Application\Commands;

use App\Mars\Domain\Entities\Rover;
use App\Mars\Ports\Input\RoverCommandInterface;

final class TurnRightCommand implements RoverCommandInterface
{
    public function execute(Rover $rover): void
    {
        $rover->turnRight();
    }
}