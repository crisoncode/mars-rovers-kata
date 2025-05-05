<?php

declare(strict_types=1);

namespace App\Mars\Domain\Entities;

use App\Mars\Domain\Enums\Direction;
use App\Mars\Domain\Exceptions\ObstacleDetectedException;
use App\Mars\Domain\ValueObjects\Position;

final class Rover
{
    private Position $position;
    private Direction $direction;
    private readonly PlanetMap $planetMap;
    
    public static function createDefault(PlanetMap $planetMap): self
    {
        // Default position at (2, 3) facing North as mentioned in README example
        $defaultPosition = new Position(2, 3);
        $defaultDirection = Direction::NORTH;
        
        return new self($defaultPosition, $defaultDirection, $planetMap);
    }

    public function __construct(Position $position, Direction $direction, PlanetMap $planetMap)
    {
        $this->position = $position;
        $this->direction = $direction;
        $this->planetMap = $planetMap;
        $this->planetMap->validatePosition($this->position);
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function direction(): Direction
    {
        return $this->direction;
    }

    public function turnLeft(): void
    {
        $this->direction = $this->direction->turnLeft();
    }

    public function turnRight(): void
    {
        $this->direction = $this->direction->turnRight();
    }

    public function moveForward(): void
    {
        $newPosition = $this->position->move($this->direction);

        if ($this->planetMap->hasObstacleAt($newPosition)) {
            throw new ObstacleDetectedException($newPosition);
        }

        $this->planetMap->validatePosition($newPosition);
        $this->position = $newPosition;
    }
}