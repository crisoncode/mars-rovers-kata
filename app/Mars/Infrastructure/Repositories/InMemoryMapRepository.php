<?php

declare(strict_types=1);

namespace App\Mars\Infrastructure\Repositories;

use App\Mars\Domain\Entities\PlanetMap;
use App\Mars\Domain\Repositories\MapRepository;
use App\Mars\Domain\ValueObjects\Position;
use Illuminate\Support\Facades\Cache;

final class InMemoryMapRepository implements MapRepository
{
    private const MAP_CACHE_KEY = 'planet_map';

    public function save(PlanetMap $map): void
    {
        // Store map data as an array instead of the object itself
        $mapData = [
            'width' => $map->width(),
            'height' => $map->height(),
            'obstacles' => $this->serializeObstacles($map)
        ];
        Cache::forever(self::MAP_CACHE_KEY, $mapData);
    }

    /**
     * Serialize obstacles from the map into a simple array format
     */
    private function serializeObstacles(PlanetMap $map): array
    {
        $obstacles = [];

        // Loop through possible positions to find obstacles
        for ($x = 0; $x <= $map->width(); $x++) {
            for ($y = 0; $y <= $map->height(); $y++) {
                $position = new Position($x, $y);
                if ($map->hasObstacleAt($position)) {
                    $obstacles[] = ['x' => $x, 'y' => $y];
                }
            }
        }

        return $obstacles;
    }

    public function get(): PlanetMap
    {
        $mapData = Cache::get(self::MAP_CACHE_KEY);
        if ($mapData === null) {
            $map = new PlanetMap();
            $this->save($map);
            return $map;
        }

        // Reconstruct the map from the stored data
        $map = new PlanetMap($mapData['width'] ?? null, $mapData['height'] ?? null);

        // Add obstacles from the serialized data
        if (isset($mapData['obstacles']) && is_array($mapData['obstacles'])) {
            foreach ($mapData['obstacles'] as $obstacle) {
                if (isset($obstacle['x']) && isset($obstacle['y'])) {
                    $position = new Position($obstacle['x'], $obstacle['y']);
                    $map->addObstacle($position);
                }
            }
        }

        return $map;
    }

    public function initialize(): void
    {
        // Create a fresh map and initialize it
        $map = new PlanetMap();
        $map->initialize();
        $this->save($map);
    }
    
    public function configureMap(?int $width = null, ?int $height = null, ?float $obstacleProbability = null): void
    {
        // Create a map with custom settings
        $map = new PlanetMap($width, $height, $obstacleProbability);
        $map->initialize();
        $this->save($map);
    }

    public function hasObstacleAt(Position $position): bool
    {
        return $this->get()->hasObstacleAt($position);
    }

    public function addObstacle(Position $position): void
    {
        $this->get()->addObstacle($position);
    }


}
