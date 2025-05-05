<?php

declare(strict_types=1);

namespace App\Mars\Infrastructure\Repositories;

use App\Mars\Domain\Entities\PlanetMap;
use App\Mars\Domain\Entities\Rover;
use App\Mars\Domain\Enums\Direction;
use App\Mars\Domain\Repositories\RoverRepository;
use App\Mars\Domain\ValueObjects\Position;
use Illuminate\Support\Facades\Cache;

final class InMemoryRoverRepository implements RoverRepository
{
    private const ROVER_CACHE_KEY = 'rover';

    public function save(Rover $rover): void
    {
        // Store rover data as an array instead of the object itself
        $roverData = [
            'position' => [
                'x' => $rover->position()->x(),
                'y' => $rover->position()->y(),
            ],
            'direction' => $rover->direction()->value,
        ];

        Cache::forever(self::ROVER_CACHE_KEY, $roverData);
    }

    public function get(): ?Rover
    {
        $roverData = Cache::get(self::ROVER_CACHE_KEY);

        if ($roverData === null) {
            return null;
        }

        // Reconstruct the rover from the stored data
        $position = new Position(
            $roverData['position']['x'] ?? 0,
            $roverData['position']['y'] ?? 0
        );

        $direction = match ($roverData['direction'] ?? 'N') {
            'N' => Direction::NORTH,
            'E' => Direction::EAST,
            'S' => Direction::SOUTH,
            'W' => Direction::WEST,
            default => Direction::NORTH
        };

        // We need to get the map from the MapRepository
        // This creates a dependency on the MapRepository
        $mapRepository = app()->make(InMemoryMapRepository::class);
        $map = $mapRepository->get();

        return new Rover($position, $direction, $map);
    }

    public function initialize(PlanetMap $map): void
    {
        // Create a default rover and save it
        $rover = Rover::createDefault($map);
        $this->save($rover);
    }
}
