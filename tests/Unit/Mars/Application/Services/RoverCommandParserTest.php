<?php

declare(strict_types=1);

namespace Tests\Unit\Mars\Application\Services;

use App\Mars\Application\Commands\MoveForwardCommand;
use App\Mars\Application\Commands\TurnLeftCommand;
use App\Mars\Application\Commands\TurnRightCommand;
use App\Mars\Application\Services\RoverCommandParser;
use PHPUnit\Framework\TestCase;

final class RoverCommandParserTest extends TestCase
{
    private RoverCommandParser $parser;

    protected function setUp(): void
    {
        $this->parser = new RoverCommandParser();
    }

    public function test_it_parses_move_forward_command(): void
    {
        $commands = $this->parser->parse('M');

        $this->assertCount(1, $commands);
        $this->assertInstanceOf(MoveForwardCommand::class, $commands[0]);
    }

    public function test_it_parses_turn_left_command(): void
    {
        $commands = $this->parser->parse('L');

        $this->assertCount(1, $commands);
        $this->assertInstanceOf(TurnLeftCommand::class, $commands[0]);
    }

    public function test_it_parses_turn_right_command(): void
    {
        $commands = $this->parser->parse('R');

        $this->assertCount(1, $commands);
        $this->assertInstanceOf(TurnRightCommand::class, $commands[0]);
    }

    public function test_it_parses_multiple_commands(): void
    {
        $commands = $this->parser->parse('MLR');

        $this->assertCount(3, $commands);
        $this->assertInstanceOf(MoveForwardCommand::class, $commands[0]);
        $this->assertInstanceOf(TurnLeftCommand::class, $commands[1]);
        $this->assertInstanceOf(TurnRightCommand::class, $commands[2]);
    }

    public function test_it_parses_commands_case_insensitive(): void
    {
        $commands = $this->parser->parse('mlr');

        $this->assertCount(3, $commands);
        $this->assertInstanceOf(MoveForwardCommand::class, $commands[0]);
        $this->assertInstanceOf(TurnLeftCommand::class, $commands[1]);
        $this->assertInstanceOf(TurnRightCommand::class, $commands[2]);
    }

    public function test_it_throws_exception_for_invalid_command(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid command: X');

        $this->parser->parse('X');
    }
}