<?php

namespace App\Services;

class RoverService
{
    public function generateObstacles(int $x, int $y, string $direction, int $count = 800): array
    {
        $mapsize = 200;
        $obstacles = [];
        $protected = $this->getProtectedCells($x, $y, $direction);

        while (count($obstacles) < $count ) {
            $ox = rand(0, $mapsize - 1);
            $oy = rand(0, $mapsize - 1);

            if (
                ($ox === $x && $oy === $y) || // skip rover position
                in_array([$ox, $oy], $protected) || // keep first moves clear
                in_array([$ox, $oy], $obstacles) // avoid duplicates
            ) {
                continue;
            }

            $obstacles[] = [$ox, $oy];
        }

        return $obstacles;
    }

    private function getProtectedCells(int $x, int $y, string $direction): array
    {
        $cells = [[$x, $y]];
        // keep starting cell free
        switch ($direction) {
            case 'N': $cells[] = [$x, $y - 1]; break;
            case 'S': $cells[] = [$x, $y + 1]; break;
            case 'E': $cells[] = [$x + 1, $y]; break;
            case 'W': $cells[] = [$x - 1, $y]; break;
        }
        return $cells;
    }

    public function executeMission(int $x, int $y, string $direction, string $commands, array $obstacles): array
    {
        $mapSize = 200;
        $steps = [];
        $obstacleEncountered = false;
        $obstaclePosition = null;

        $dirMap = ['N' => [0, -1], 'E' => [1, 0], 'S' => [0, 1], 'W' => [-1, 0]];
        $dirs = ['N', 'E', 'S', 'W'];

        for ($i = 0; $i < strlen($commands); $i++) {
            $cmd = strtoupper($commands[$i]);

            if ($cmd === 'F') {
                [$dx, $dy] = $dirMap[$direction];
                $nx = ($x + $dx + $mapSize) % $mapSize;
                $ny = ($y + $dy + $mapSize) % $mapSize;

                if (in_array([$nx, $ny], $obstacles)) {
                    $obstacleEncountered = true;
                    $obstaclePosition = [$nx, $ny];
                    break;
                }

                $x = $nx;
                $y = $ny;
            }

            if ($cmd === 'L') {
                $index = (array_search($direction, $dirs) - 1 + 4) % 4;
                $direction = $dirs[$index];
            }

            if ($cmd === 'R') {
                $index = (array_search($direction, $dirs) + 1) % 4;
                $direction = $dirs[$index];
            }

            $steps[] = ['x' => $x, 'y' => $y, 'direction' => $direction];
        }

        return [
            'finalPosition' => ['x' => $x, 'y' => $y, 'direction' => $direction],
            'steps' => $steps,
            'obstacleEncountered' => $obstacleEncountered,
            'obstaclePosition' => $obstaclePosition,
            'obstacles' => $obstacles,
        ];
    }
}
