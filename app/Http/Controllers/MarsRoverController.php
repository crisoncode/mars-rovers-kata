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

/**
 * @OA\Info(title="Rover Mars API", version="1.0")
 */
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
     * @OA\Get(
     *     path="/api/rover/position",
     *     summary="Get the current rover position and direction",
     *     tags={"Rover"},
     *     @OA\Response(
     *         response=200,
     *         description="Current position and direction",
     *         @OA\JsonContent(
     *             @OA\Property(property="position", type="object",
     *                 @OA\Property(property="x", type="integer", example=1),
     *                 @OA\Property(property="y", type="integer", example=2)
     *             ),
     *             @OA\Property(property="direction", type="string", example="N")
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/rover/command",
     *     summary="Send commands to the rover",
     *     tags={"Rover"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"commands"},
     *             @OA\Property(property="commands", type="string", example="FFRL")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Result after executing commands",
     *         @OA\JsonContent(
     *             @OA\Property(property="result", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="finalState", type="object")
     *         )
     *     )
     * )
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