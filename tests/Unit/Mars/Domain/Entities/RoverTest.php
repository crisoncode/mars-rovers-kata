<?php

declare(strict_types=1);

namespace Tests\Unit\Mars\Domain\Entities;

use App\Mars\Domain\Entities\PlanetMap;
use App\Mars\Domain\Entities\Rover;
use App\Mars\Domain\Enums\Direction;
use App\Mars\Domain\Exceptions\ObstacleDetectedException;
use App\Mars\Domain\Exceptions\OutOfBoundsException;
use App\Mars\Domain\ValueObjects\Position;
use PHPUnit\Framework\TestCase;

final class RoverTest extends TestCase
{
    private PlanetMap $planetMap;
    private Position $initialPosition;
    private Direction $initialDirection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->planetMap = new PlanetMap(5, 5);
        $this->initialPosition = new Position(2, 2);
        $this->initialDirection = Direction::NORTH;
    }

    public function test_it_creates_a_rover(): void
    {
        $rover = new Rover(
            $this->initialPosition,
            $this->initialDirection,
            $this->planetMap
        );

        $this->assertEquals($this->initialPosition, $rover->position());
        $this->assertEquals($this->initialDirection, $rover->direction());
    }

    public function test_it_turns_left(): void
    {
        $rover = new Rover(
            $this->initialPosition,
            Direction::NORTH,
            $this->planetMap
        );

        $rover->turnLeft();
        $this->assertEquals(Direction::WEST, $rover->direction());

        $rover->turnLeft();
        $this->assertEquals(Direction::SOUTH, $rover->direction());

        $rover->turnLeft();
        $this->assertEquals(Direction::EAST, $rover->direction());

        $rover->turnLeft();
        $this->assertEquals(Direction::NORTH, $rover->direction());
    }

    public function test_it_turns_right(): void
    {
        $rover = new Rover(
            $this->initialPosition,
            Direction::NORTH,
            $this->planetMap
        );

        $rover->turnRight();
        $this->assertEquals(Direction::EAST, $rover->direction());

        $rover->turnRight();
        $this->assertEquals(Direction::SOUTH, $rover->direction());

        $rover->turnRight();
        $this->assertEquals(Direction::WEST, $rover->direction());

        $rover->turnRight();
        $this->assertEquals(Direction::NORTH, $rover->direction());
    }

    public function test_it_moves_forward(): void
    {
        // Create a planet map without random obstacles
        $planetMap = new PlanetMap(5, 5, 0);

        $rover = new Rover(
            new Position(2, 2),
            Direction::NORTH,
            $planetMap
        );

        $rover->moveForward();
        $this->assertEquals(new Position(2, 3), $rover->position());
    }

    public function test_it_detects_obstacles(): void
    {
        // Create a planet map with a known obstacle at (1, 3)
        $planetMap = new PlanetMap(5, 5, 1);
        $planetMap->addObstacle(new Position(1, 3));

        $rover = new Rover(
            new Position(1, 2),
            Direction::NORTH,
            $planetMap
        );

        $this->expectException(ObstacleDetectedException::class);
        $rover->moveForward();
    }

    public function test_it_throws_exception_when_moving_out_of_bounds(): void
    {
        $rover = new Rover(
            new Position(5, 5),
            Direction::NORTH,
            $this->planetMap
        );

        $this->expectException(OutOfBoundsException::class);
        $rover->moveForward();
    }
}
