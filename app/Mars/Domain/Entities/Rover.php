<?php

declare(strict_types=1);

namespace App\Mars\Domain\Entities;

use App\Mars\Domain\Enums\Direction;
use App\Mars\Domain\Exceptions\ObstacleDetectedException;
use App\Mars\Domain\Exceptions\OutOfBoundsException;
use App\Mars\Domain\ValueObjects\Position;

final class Rover
{
    public function __construct(
        private Position $position,
        private Direction $direction,
        private readonly Plateau $plateau
    ) {
        $this->plateau->validatePosition($this->position);
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
        $newPosition = $this->calculateNewPosition();

        if ($this->plateau->hasObstacleAt($newPosition)) {
            throw new ObstacleDetectedException($newPosition);
        }

        $this->plateau->validatePosition($newPosition);
        $this->position = $newPosition;
    }

    private function calculateNewPosition(): Position
    {
        $x = $this->position->x();
        $y = $this->position->y();

        match ($this->direction) {
            Direction::NORTH => $y++,
            Direction::EAST => $x++,
            Direction::SOUTH => $y--,
            Direction::WEST => $x--,
        };

        return new Position($x, $y);
    }
}