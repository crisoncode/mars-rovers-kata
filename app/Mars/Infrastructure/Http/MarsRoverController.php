<?php

declare(strict_types=1);

namespace App\Mars\Infrastructure\Http;

use App\Mars\Application\Services\RoverCommandParser;
use App\Mars\Domain\Entities\PlanetMap;
use App\Mars\Domain\Entities\Rover;
use App\Mars\Domain\Exceptions\ObstacleDetectedException;
use App\Mars\Domain\Exceptions\OutOfBoundsException;
use App\Mars\Domain\Repositories\MapRepository;
use App\Mars\Domain\Repositories\RoverRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;


/**
 * @OA\Info(title="Rover Mars API", version="1.0")
 *
 * @OA\Schema(
 *     schema="MapConfiguration",
 *     @OA\Property(property="width", type="integer", example=100, description="Width of the map"),
 *     @OA\Property(property="height", type="integer", example=100, description="Height of the map"),
 *     @OA\Property(property="obstacleProbability", type="number", format="float", example=0.2, description="Probability of obstacles (0-1)")
 * )
 */
class MarsRoverController extends BaseController
{
    private PlanetMap $planetMap;
    private Rover $rover;
    private RoverCommandParser $commandParser;
    private MapRepository $mapRepository;
    private RoverRepository $roverRepository;

    public function __construct(MapRepository $mapRepository, RoverRepository $roverRepository)
    {
        $this->mapRepository = $mapRepository;
        $this->roverRepository = $roverRepository;
        $this->planetMap = $this->mapRepository->get();

        // Get existing rover or throw an error if not initialized
        $rover = $this->roverRepository->get();
        if ($rover === null) {
            // If no rover exists, we need to initialize the system
            $this->mapRepository->initialize();
            $this->roverRepository->initialize($this->planetMap);
            $rover = $this->roverRepository->get();
        }

        $this->rover = $rover;
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
        // Always get the latest rover state from repository
        $rover = $this->roverRepository->get();
        if ($rover === null) {
            // If no rover exists, initialize the system
            $this->mapRepository->initialize();
            $this->roverRepository->initialize($this->planetMap);
            $rover = $this->roverRepository->get();
        }

        return response()->json([
            'position' => [
                'x' => $rover->position()->x(),
                'y' => $rover->position()->y(),
            ],
            'direction' => $rover->direction()->value,
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
     *             @OA\Property(property="commands", type="string", example="MRML")
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
        // Always get the latest rover state from the repository
        $this->rover = $this->roverRepository->get();
        if ($this->rover === null) {
            // If no rover exists, initialize the system
            $this->mapRepository->initialize();
            $this->roverRepository->initialize($this->planetMap);
            $this->rover = $this->roverRepository->get();
        }

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

        // Save both the map and rover state
        $this->mapRepository->save($this->planetMap);
        $this->roverRepository->save($this->rover);

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

    /**
     * @OA\Post(
     *     path="/api/mars/configure",
     *     summary="Configure the Mars map settings",
     *     tags={"Mars"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/MapConfiguration")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Map configured successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Map configured successfully"),
     *             @OA\Property(property="config", type="object", ref="#/components/schemas/MapConfiguration")
     *         )
     *     )
     * )
     */
    public function configureMap(Request $request): JsonResponse
    {
        // Get configuration parameters from request
        $width = $request->input('width');
        $height = $request->input('height');
        $obstacleProbability = $request->input('obstacleProbability');

        // Validate obstacle probability if provided
        if ($obstacleProbability !== null) {
            if ($obstacleProbability < 0 || $obstacleProbability > 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Obstacle probability must be between 0 and 1'
                ], 400);
            }
        }

        try {
            // Configure the map with custom settings
            $this->mapRepository->configureMap($width, $height, $obstacleProbability);

            // Initialize the rover with the new map
            $this->planetMap = $this->mapRepository->get();
            $this->roverRepository->initialize($this->planetMap);
            $this->rover = $this->roverRepository->get();

            return response()->json([
                'success' => true,
                'message' => 'Map configured successfully',
                'config' => [
                    'width' => $width ?? PlanetMap::DEFAULT_WIDTH,
                    'height' => $height ?? PlanetMap::DEFAULT_HEIGHT,
                    'obstacleProbability' => $obstacleProbability ?? 0.1,
                ]
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
