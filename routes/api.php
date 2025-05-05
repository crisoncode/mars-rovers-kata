<?php
use App\Http\Controllers\MarsRoverController;


Route::get('/test', function () {
    return response()->json(['message' => 'Rover API is working!']);
});

// Get the current rover position and direction
Route::get('/rover/position', [MarsRoverController::class, 'getPosition']);

// Send commands to the rover
Route::post('/rover/command', [MarsRoverController::class, 'executeCommands']);

// Configure the Mars map settings
Route::post('/mars/configure', [MarsRoverController::class, 'configureMap']);