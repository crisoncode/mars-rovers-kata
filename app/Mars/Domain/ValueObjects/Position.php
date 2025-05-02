<?php

declare(strict_types=1);

namespace App\Mars\Domain\ValueObjects;

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
}