<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoverController;

Route::post('/rover/start', [RoverController::class, 'start']);
Route::post('/rover/execute', [RoverController::class, 'execute']);

