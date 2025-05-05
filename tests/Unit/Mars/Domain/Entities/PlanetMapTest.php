<?php

declare(strict_types=1);

namespace Tests\Unit\Mars\Domain\Entities;

use App\Mars\Domain\Entities\PlanetMap;
use App\Mars\Domain\Exceptions\OutOfBoundsException;
use App\Mars\Domain\ValueObjects\Position;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PlanetMapTest extends TestCase
{
    public function test_it_creates_a_planet_map_with_default_dimensions(): void
    {
        $planetMap = new PlanetMap();

        $this->assertEquals(200, $planetMap->width());
        $this->assertEquals(200, $planetMap->height());
    }

    public function test_it_creates_a_planet_map_with_custom_dimensions(): void
    {
        $planetMap = new PlanetMap(5, 5);

        $this->assertEquals(5, $planetMap->width());
        $this->assertEquals(5, $planetMap->height());
    }

    public function test_it_throws_exception_for_invalid_dimensions(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Planet map dimensions must be positive');

        new PlanetMap(0, 0);
    }

    public function test_it_validates_positions(): void
    {
        $planetMap = new PlanetMap(5, 5);

        $this->assertTrue($planetMap->isValidPosition(new Position(0, 0)));
        $this->assertTrue($planetMap->isValidPosition(new Position(5, 5)));
        $this->assertFalse($planetMap->isValidPosition(new Position(6, 6)));
        $this->assertFalse($planetMap->isValidPosition(new Position(-1, -1)));
    }

    public function test_it_throws_exception_for_invalid_position(): void
    {
        $planetMap = new PlanetMap(5, 5);

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Position is outside planet map boundaries');

        $planetMap->validatePosition(new Position(6, 6));
    }

    public function test_it_generates_obstacles(): void
    {
        $planetMap = new PlanetMap(5, 5);
        // Call initialize to generate obstacles
        $planetMap->initialize();

        $hasObstacles = false;
        for ($x = 0; $x <= 5; $x++) {
            for ($y = 0; $y <= 5; $y++) {
                if ($planetMap->hasObstacleAt(new Position($x, $y))) {
                    $hasObstacles = true;
                    break 2;
                }
            }
        }

        $this->assertTrue($hasObstacles, 'No obstacles were generated');
    }
}