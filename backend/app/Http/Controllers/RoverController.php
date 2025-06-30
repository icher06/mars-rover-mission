<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RoverService;

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

    public function execute(Request $request, RoverService $roverService)
    {
        $validated = $request->validate([
            'x' => 'required|integer|min:0|max:199',
            'y' => 'required|integer|min:0|max:199',
            'direction' => 'required|in:N,S,E,W',
            'commands' => 'required|string|regex:/^[flrFLR]+$/i',
            'obstacles' => 'present|array',
            'obstacles.*' => 'array|size:2'
        ]);

        $result = $roverService->executeMission(
            $validated['x'],
            $validated['y'],
            strtoupper($validated['direction']),
            strtoupper($validated['commands']),
            $validated['obstacles']
        );

        return response()->json($result);
    }
}
