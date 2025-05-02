<?php

declare(strict_types=1);

namespace Tests\Unit\Mars\Domain\Entities;

use App\Mars\Domain\Entities\Plateau;
use App\Mars\Domain\Entities\Rover;
use App\Mars\Domain\Enums\Direction;
use App\Mars\Domain\Exceptions\OutOfBoundsException;
use App\Mars\Domain\ValueObjects\Position;
use PHPUnit\Framework\TestCase;

final class RoverTest extends TestCase
{
    private Plateau $plateau;
    private Rover $rover;

    protected function setUp(): void
    {
        $this->plateau = new Plateau(5, 5);
        $this->rover = new Rover(
            new Position(1, 2),
            Direction::NORTH,
            $this->plateau
        );
    }

    public function test_it_creates_a_rover(): void
    {
        $this->assertEquals(1, $this->rover->position()->x());
        $this->assertEquals(2, $this->rover->position()->y());
        $this->assertEquals(Direction::NORTH, $this->rover->direction());
    }

    public function test_it_throws_exception_for_invalid_initial_position(): void
    {
        $this->expectException(OutOfBoundsException::class);

        new Rover(
            new Position(6, 6),
            Direction::NORTH,
            $this->plateau
        );
    }

    public function test_it_turns_left(): void
    {
        $this->rover->turnLeft();
        $this->assertEquals(Direction::WEST, $this->rover->direction());

        $this->rover->turnLeft();
        $this->assertEquals(Direction::SOUTH, $this->rover->direction());

        $this->rover->turnLeft();
        $this->assertEquals(Direction::EAST, $this->rover->direction());

        $this->rover->turnLeft();
        $this->assertEquals(Direction::NORTH, $this->rover->direction());
    }

    public function test_it_turns_right(): void
    {
        $this->rover->turnRight();
        $this->assertEquals(Direction::EAST, $this->rover->direction());

        $this->rover->turnRight();
        $this->assertEquals(Direction::SOUTH, $this->rover->direction());

        $this->rover->turnRight();
        $this->assertEquals(Direction::WEST, $this->rover->direction());

        $this->rover->turnRight();
        $this->assertEquals(Direction::NORTH, $this->rover->direction());
    }

    public function test_it_moves_forward(): void
    {
        $this->rover->moveForward();
        $this->assertEquals(1, $this->rover->position()->x());
        $this->assertEquals(3, $this->rover->position()->y());

        $this->rover->turnRight();
        $this->rover->moveForward();
        $this->assertEquals(2, $this->rover->position()->x());
        $this->assertEquals(3, $this->rover->position()->y());

        $this->rover->turnRight();
        $this->rover->moveForward();
        $this->assertEquals(2, $this->rover->position()->x());
        $this->assertEquals(2, $this->rover->position()->y());

        $this->rover->turnRight();
        $this->rover->moveForward();
        $this->assertEquals(1, $this->rover->position()->x());
        $this->assertEquals(2, $this->rover->position()->y());
    }

    public function test_it_throws_exception_when_moving_out_of_bounds(): void
    {
        $rover = new Rover(
            new Position(5, 5),
            Direction::NORTH,
            $this->plateau
        );

        $this->expectException(OutOfBoundsException::class);
        $rover->moveForward();
    }
}