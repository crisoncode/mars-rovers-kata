<?php

declare(strict_types=1);

namespace App\Mars\Domain\Exceptions;

use App\Mars\Domain\ValueObjects\Position;

final class ObstacleDetectedException extends \RuntimeException
{
    public function __construct(
        private readonly Position $position,
        string $message = 'Obstacle detected at position'
    ) {
        parent::__construct(sprintf('%s (%d, %d)', $message, $position->x(), $position->y()));
    }

    public function position(): Position
    {
        return $this->position;
    }
}