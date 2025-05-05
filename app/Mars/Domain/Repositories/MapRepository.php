<?php

declare(strict_types=1);

namespace App\Mars\Domain\Repositories;

use App\Mars\Domain\Entities\PlanetMap;
use App\Mars\Domain\ValueObjects\Position;

interface MapRepository
{
    public function save(PlanetMap $map): void;
    public function get(): PlanetMap;
    public function hasObstacleAt(Position $position): bool;
    public function addObstacle(Position $position): void;
    public function initialize(): void;
    public function configureMap(?int $width = null, ?int $height = null, ?float $obstacleProbability = null): void;
}