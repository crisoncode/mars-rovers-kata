<?php

declare(strict_types=1);

namespace App\Mars\Domain\Entities;

use App\Mars\Domain\Exceptions\OutOfBoundsException;
use App\Mars\Domain\ValueObjects\Position;
use InvalidArgumentException;

final class Plateau
{
    private const DEFAULT_WIDTH = 200;
    private const DEFAULT_HEIGHT = 200;
    private const OBSTACLE_PROBABILITY = 0.1; // 10% chance of obstacle

    private int $width;
    private int $height;
    private array $obstacles;

    public function __construct(?int $width = null, ?int $height = null, bool $generateObstacles = true)
    {
        $this->width = $width ?? self::DEFAULT_WIDTH;
        $this->height = $height ?? self::DEFAULT_HEIGHT;

        if ($this->width <= 0 || $this->height <= 0) {
            throw new InvalidArgumentException('Plateau dimensions must be positive');
        }

        $this->obstacles = [];
        if ($generateObstacles) {
            $this->generateObstacles();
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
        return $position->x() >= 0
            && $position->x() <= $this->width
            && $position->y() >= 0
            && $position->y() <= $this->height;
    }

    public function validatePosition(Position $position): void
    {
        if (!$this->isValidPosition($position)) {
            throw new OutOfBoundsException('Position is outside plateau boundaries');
        }
    }

    public function hasObstacleAt(Position $position): bool
    {
        return isset($this->obstacles[$position->x()][$position->y()]);
    }

    public function addObstacle(Position $position): void
    {
        $this->validatePosition($position);
        $this->obstacles[$position->x()][$position->y()] = true;
    }

    private function generateObstacles(): void
    {
        $this->obstacles = [];

        for ($x = 0; $x <= $this->width; $x++) {
            for ($y = 0; $y <= $this->height; $y++) {
                if (rand(0, 100) / 100 < self::OBSTACLE_PROBABILITY) {
                    $this->obstacles[$x][$y] = true;
                }
            }
        }
    }
}