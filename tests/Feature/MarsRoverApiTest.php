<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarsRoverApiTest extends TestCase
{
    /**
     * Test the API health check endpoint.
     */
    public function test_api_health_check(): void
    {
        $response = $this->get('/api/test');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Rover API is working!'
            ]);
    }

    /**
     * Test the map configuration endpoint.
     */
    public function test_configure_map(): void
    {
        $response = $this->postJson('/api/mars/configure', [
            'width' => 100,
            'height' => 150,
            'obstacleProbability' => 0.2
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Map configured successfully',
                'config' => [
                    'width' => 100,
                    'height' => 150,
                    'obstacleProbability' => 0.2
                ]
            ]);
    }

    /**
     * Test the map configuration endpoint with invalid obstacle probability.
     */
    public function test_configure_map_with_invalid_obstacle_probability(): void
    {
        $response = $this->postJson('/api/mars/configure', [
            'width' => 100,
            'height' => 150,
            'obstacleProbability' => 2.0 // Invalid: greater than 1
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Obstacle probability must be between 0 and 1'
            ]);
    }

    /**
     * Test getting the rover position.
     */
    public function test_get_rover_position(): void
    {
        // First configure a map to ensure we have a consistent state
        $this->postJson('/api/mars/configure', [
            'width' => 100,
            'height' => 100,
            'obstacleProbability' => 0.1
        ]);

        $response = $this->getJson('/api/rover/position');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'position' => [
                    'x',
                    'y'
                ],
                'direction'
            ]);
    }

    /**
     * Test executing rover commands.
     */
    public function test_execute_rover_commands(): void
    {
        // First configure a map to ensure we have a consistent state
        $this->postJson('/api/mars/configure', [
            'width' => 100,
            'height' => 100,
            'obstacleProbability' => 0.0 // No obstacles for predictable movement
        ]);

        // Get initial position
        $initialPosition = $this->getJson('/api/rover/position')->json();

        // Execute commands: move forward twice
        $response = $this->postJson('/api/rover/command', [
            'commands' => 'MM'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'result',
                'finalState' => [
                    'position' => [
                        'x',
                        'y'
                    ],
                    'direction'
                ]
            ]);

        // Verify the rover moved according to its direction
        $finalState = $response->json('finalState');
        $this->assertNotEquals(
            $initialPosition['position'], 
            $finalState['position'], 
            'Rover position should change after moving forward'
        );
    }

    /**
     * Test rover rotation commands.
     */
    public function test_rover_rotation_commands(): void
    {
        // First configure a map to ensure we have a consistent state
        $this->postJson('/api/mars/configure', [
            'width' => 100,
            'height' => 100,
            'obstacleProbability' => 0.0 // No obstacles for predictable movement
        ]);

        // Get initial direction
        $initialPosition = $this->getJson('/api/rover/position')->json();
        $initialDirection = $initialPosition['direction'];

        // Execute commands: rotate left, then right, then right again
        $response = $this->postJson('/api/rover/command', [
            'commands' => 'LRR'
        ]);

        $response->assertStatus(200);
        
        $finalState = $response->json('finalState');
        
        // After L, R, R the rover should be facing a different direction than initial
        // The exact direction depends on the initial direction, but it should be different
        $this->assertNotEquals(
            $initialDirection, 
            $finalState['direction'], 
            'Rover direction should change after rotation commands'
        );
    }

    /**
     * Test a complete sequence of rover movements.
     */
    public function test_complete_rover_movement_sequence(): void
    {
        // First configure a map to ensure we have a consistent state
        $this->postJson('/api/mars/configure', [
            'width' => 100,
            'height' => 100,
            'obstacleProbability' => 0.0 // No obstacles for predictable movement
        ]);

        // Execute a sequence of commands
        $response = $this->postJson('/api/rover/command', [
            'commands' => 'MMRMMRMM'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'result' => [
                    '*' => [
                        'success',
                        'position' => [
                            'x',
                            'y'
                        ],
                        'direction'
                    ]
                ],
                'finalState' => [
                    'position' => [
                        'x',
                        'y'
                    ],
                    'direction'
                ]
            ]);

        // Verify we have the correct number of result steps
        $this->assertCount(8, $response->json('result'), 'Should have 8 result steps for 8 commands');
    }
}
