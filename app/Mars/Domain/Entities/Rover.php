<?php

declare(strict_types=1);

namespace App\Mars\Domain\Entities;

use App\Mars\Domain\Enums\Direction;
use App\Mars\Domain\ValueObjects\Position;

final class Rover
{
    public function __construct(
        private Position $position,
        private Direction $direction,
        private readonly Plateau $plateau
    ) {
        $this->plateau->validatePosition($position);
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
        $newPosition = match ($this->direction) {
            Direction::NORTH => new Position($this->position->x(), $this->position->y() + 1),
            Direction::EAST => new Position($this->position->x() + 1, $this->position->y()),
            Direction::SOUTH => new Position($this->position->x(), $this->position->y() - 1),
            Direction::WEST => new Position($this->position->x() - 1, $this->position->y()),
        };

        $this->plateau->validatePosition($newPosition);
        $this->position = $newPosition;
    }
}