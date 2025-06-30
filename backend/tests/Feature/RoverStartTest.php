<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoverStartTest extends TestCase
{
    public function test_generate_obstacles_returns_valid_response()
    {
        $response = $this->postJson('api/rover/start', [
            'x' => 50,
            'y' => 50,
            'direction' => N,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['obstacles']);
        $this->assertCount(20, $response['obstacles']);

        foreach ($response['obstacles'] as $obst) {
            $this->assertNotEquals([50, 50], $obst);
            $this->assertNotEquals([50, 51], $obst);
            $this->assertNotEquals([51, 50], $obst);
            $this->assertNotEquals([49, 50], $obst);
            $this->assertNotEquals([50, 59], $obst);
        }
    }
}
