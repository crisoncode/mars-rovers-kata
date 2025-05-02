<?php

declare(strict_types=1);

namespace Tests\Unit\Mars\Domain\ValueObjects;

use App\Mars\Domain\ValueObjects\Position;
use PHPUnit\Framework\TestCase;

final class PositionTest extends TestCase
{
    public function test_it_creates_a_position(): void
    {
        $position = new Position(1, 2);

        $this->assertEquals(1, $position->x());
        $this->assertEquals(2, $position->y());
    }

    public function test_it_compares_positions(): void
    {
        $position1 = new Position(1, 2);
        $position2 = new Position(1, 2);
        $position3 = new Position(2, 1);

        $this->assertTrue($position1->equals($position2));
        $this->assertFalse($position1->equals($position3));
    }
}