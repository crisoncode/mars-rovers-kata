<?php

declare(strict_types=1);

namespace App\Mars\Domain\Repositories;

use App\Mars\Domain\Entities\PlanetMap;
use App\Mars\Domain\Entities\Rover;

interface RoverRepository
{
    public function save(Rover $rover): void;
    public function get(): ?Rover;
    public function initialize(PlanetMap $map): void;
}
