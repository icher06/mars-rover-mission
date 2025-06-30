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
            'direction' => 'N',
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

    public function test_generate_obstacles_from_map_corners()
    {
        $cases = [
            [0, 0, 'S'],
            [0, 199, 'N'],
            [199, 0, 'S'],
            [199, 199, 'N'],
        ];

        foreach ($cases as [$x, $y, $direction]) {
            $response = $this->postJson('api/rover/start', [
                'x' => $x,
                'y' => $y,
                'direction' => $direction,
            ]);

            $response->assertStatus(200);
            $response->assertJsonStructure(['obstacles']);
            $this->assertCount(20, $response['obstacles']);

            foreach ($response['obstacles'] as $obst) {
                $this->assertNotEquals([$x, $y], $obst);
                if ($direction === 'N' && $y < 199) {
                    $this->assertNotEquals([$x, $y + 1], $obst);
                } elseif ($direction === 'S' && $y > 0) {
                    $this->assertNotEquals([$x, $y - 1], $obst);
                }
            }
        }
    }
}
