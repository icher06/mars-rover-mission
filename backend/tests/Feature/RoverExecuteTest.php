<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoverExecuteTest extends TestCase
{
    public function test_rover_moves_without_obstacles()
    {
        $response = $this->postJson('/api/rover/execute', [
            'x' => 0,
            'y' => 0,
            'direction' => 'N',
            'commands' => 'FFRFF',
            'obstacles' => []
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'finalPosition' => ['x' => 2, 'y' => 198, 'direction' => 'E'],
            'obstacleEncountered' => false
        ]);
        $this->assertCount(5, $response['steps']);
    }

    public function test_rover_stops_on_obstacle()
    {
        $response = $this->postJson('/api/rover/execute', [
            'x' => 0,
            'y' => 0,
            'direction' => 'N',
            'commands' => 'FFF',
            'obstacles' => [[0, 198]]
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'finalPosition' => ['x' => 0, 'y' => 199, 'direction' => 'N'],
            'obstacleEncountered' => true,
            'obstaclePosition' => [0, 198],
            'obstacles' => [[0, 198]]
        ]);
    }
}
