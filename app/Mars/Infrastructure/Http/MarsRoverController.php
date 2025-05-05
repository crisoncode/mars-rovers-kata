<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mars\Application\Services\RoverCommandParser;
use App\Mars\Domain\Entities\Plateau;
use App\Mars\Domain\Entities\Rover;
use App\Mars\Domain\Enums\Direction;
use App\Mars\Domain\Exceptions\ObstacleDetectedException;
use App\Mars\Domain\Exceptions\OutOfBoundsException;
use App\Mars\Domain\ValueObjects\Position;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarsRoverController extends Controller
{
    private Plateau $plateau;
    private Rover $rover;
    private RoverCommandParser $commandParser;

    public function __construct()
    {
        $this->plateau = new Plateau(200, 200);
        $this->rover = new Rover(
            new Position(1, 2),
            Direction::NORTH,
            $this->plateau
        );
        $this->commandParser = new RoverCommandParser();
    }

    /**
     * GET /api/rover/position
     * Returns the current position and direction of the rover.
     */
    public function getPosition(): JsonResponse
    {
        return response()->json([
            'position' => [
                'x' => $this->rover->position()->x(),
                'y' => $this->rover->position()->y(),
            ],
            'direction' => $this->rover->direction()->value,
        ]);
    }

    /**
     * POST /api/rover/command
     * Accepts a command string (e.g., FFRL) and executes it.
     * Returns the new position and direction, or error if any command fails.
     */
    public function executeCommands(Request $request): JsonResponse
    {
        $commands = $request->input('commands', '');
        $commandObjects = $this->commandParser->parse($commands);

        $result = [];
        foreach ($commandObjects as $command) {
            try {
                $command->execute($this->rover);
                $result[] = [
                    'success' => true,
                    'position' => [
                        'x' => $this->rover->position()->x(),
                        'y' => $this->rover->position()->y(),
                    ],
                    'direction' => $this->rover->direction()->value,
                ];
            } catch (ObstacleDetectedException $e) {
                $result[] = [
                    'success' => false,
                    'error' => 'Obstacle detected',
                    'position' => [
                        'x' => $e->position()->x(),
                        'y' => $e->position()->y(),
                    ],
                ];
                break;
            } catch (OutOfBoundsException $e) {
                $result[] = [
                    'success' => false,
                    'error' => 'Out of bounds',
                ];
                break;
            }
        }

        return response()->json([
            'result' => $result,
            'finalState' => [
                'position' => [
                    'x' => $this->rover->position()->x(),
                    'y' => $this->rover->position()->y(),
                ],
                'direction' => $this->rover->direction()->value,
            ],
        ]);
    }
}