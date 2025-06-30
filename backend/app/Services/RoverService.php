<?php

namespace App\Services;

class RoverService
{
    public function generateObstacles(int $x, int $y, string $direction, int $count = 20): array
    {
        $mapsize = 200;
        $obstacles = [];
        $protected = $this->getProtectedCells($x, $y, $direction);

        while (count($obstacles) < $count ) {
            $ox = rand(0, $mapsize - 1);
            $oy = rand(0, $mapsize - 1);

            if (
                ($ox === $x && $oy === $y) ||       // Avoid obstacle spawn over the Rover
                in_array([$ox, $oy], $protected) || // Avoid blocking first directions ?makes sense?
                in_array([$ox, $oy], $obstacles)    // Avoid obstacles on top of an obstacle
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
        // Protect Rover from initial block
        switch ($direction) {
            case 'N': $cells[] = [$x, $y + 1]; break;
            case 'S': $cells[] = [$x, $y - 1]; break;
            case 'E': $cells[] = [$x + 1, $y]; break;
            case 'W': $cells[] = [$x - 1, $y]; break;
        }
        return $cells;
    }
}
