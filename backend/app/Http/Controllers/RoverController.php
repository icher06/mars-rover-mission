<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RoverSerivce;

class RoverController extends Controller
{
    public function start(Request $request, RoverService $roverService)
    {
        $valid_data = $request->validate([
            'x' => 'required|integer|min:0|max:199',
            'y' => 'required|integer|min:0|max:199',
            'direction' => 'required|in:N,S,E,W'
        ]);

        $obstacles = $roverService->generateObstacles(
            $valid_data['x'],
            $valid_data['y'],
            $valid_data['direction'],
        );

        return response()->json(['obstacles' => $obstacles]);
    }
}
