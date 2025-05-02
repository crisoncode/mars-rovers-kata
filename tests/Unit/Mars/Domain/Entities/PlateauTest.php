<?php

declare(strict_types=1);

namespace Tests\Unit\Mars\Domain\Entities;

use App\Mars\Domain\Entities\Plateau;
use App\Mars\Domain\Exceptions\OutOfBoundsException;
use App\Mars\Domain\ValueObjects\Position;
use PHPUnit\Framework\TestCase;

final class PlateauTest extends TestCase
{
    public function test_it_creates_a_plateau_with_default_dimensions(): void
    {
        $plateau = new Plateau();

        $this->assertEquals(200, $plateau->width());
        $this->assertEquals(200, $plateau->height());
    }

    public function test_it_creates_a_plateau_with_custom_dimensions(): void
    {
        $plateau = new Plateau(5, 5);

        $this->assertEquals(5, $plateau->width());
        $this->assertEquals(5, $plateau->height());
    }

    public function test_it_throws_exception_for_invalid_dimensions(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Plateau dimensions must be positive');

        new Plateau(0, 0);
    }

    public function test_it_validates_positions(): void
    {
        $plateau = new Plateau(5, 5);

        $this->assertTrue($plateau->isValidPosition(new Position(0, 0)));
        $this->assertTrue($plateau->isValidPosition(new Position(5, 5)));
        $this->assertFalse($plateau->isValidPosition(new Position(6, 6)));
        $this->assertFalse($plateau->isValidPosition(new Position(-1, -1)));
    }

    public function test_it_throws_exception_for_out_of_bounds_position(): void
    {
        $plateau = new Plateau(5, 5);

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Position is outside plateau boundaries');

        $plateau->validatePosition(new Position(6, 6));
    }

    public function test_it_generates_obstacles(): void
    {
        $plateau = new Plateau(5, 5);
        $hasObstacle = false;

        // Check if any obstacle was generated
        for ($x = 0; $x <= 5; $x++) {
            for ($y = 0; $y <= 5; $y++) {
                if ($plateau->hasObstacleAt(new Position($x, $y))) {
                    $hasObstacle = true;
                    break 2;
                }
            }
        }

        $this->assertTrue($hasObstacle);
    }
}