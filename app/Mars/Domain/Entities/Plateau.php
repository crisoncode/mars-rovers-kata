<?php

declare(strict_types=1);

namespace App\Mars\Domain\Entities;

use App\Mars\Domain\ValueObjects\Position;
use App\Mars\Domain\Exceptions\OutOfBoundsException;

final class Plateau
{
    public function __construct(
        private readonly int $width,
        private readonly int $height
    ) {
        if ($width <= 0 || $height <= 0) {
            throw new \InvalidArgumentException('Plateau dimensions must be positive');
        }
    }

    public function width(): int
    {
        return $this->width;
    }

    public function height(): int
    {
        return $this->height;
    }

    public function isValidPosition(Position $position): bool
    {
        return $position->x() >= 0 &&
            $position->x() <= $this->width &&
            $position->y() >= 0 &&
            $position->y() <= $this->height;
    }

    public function validatePosition(Position $position): void
    {
        if (!$this->isValidPosition($position)) {
            throw new OutOfBoundsException('Position is outside plateau boundaries');
        }
    }
}