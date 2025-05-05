<?php

declare(strict_types=1);

namespace App\Mars\Domain\ValueObjects;

use App\Mars\Domain\Enums\Direction;

final class Position
{
    public function __construct(
        private readonly int $x,
        private readonly int $y
    ) {
    }

    public function x(): int
    {
        return $this->x;
    }

    public function y(): int
    {
        return $this->y;
    }

    public function equals(Position $other): bool
    {
        return $this->x === $other->x && $this->y === $other->y;
    }

    public function move(Direction $direction): Position
    {
        $newX = $this->x;
        $newY = $this->y;

        match ($direction) {
            Direction::NORTH => $newY++,
            Direction::EAST => $newX++,
            Direction::SOUTH => $newY--,
            Direction::WEST => $newX--,
        };

        return new Position($newX, $newY);
    }
}