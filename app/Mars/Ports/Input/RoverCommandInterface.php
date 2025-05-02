<?php

declare(strict_types=1);

namespace App\Mars\Ports\Input;

use App\Mars\Domain\Entities\Rover;

interface RoverCommandInterface
{
    public function execute(Rover $rover): void;
}