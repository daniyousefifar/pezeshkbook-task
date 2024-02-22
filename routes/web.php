<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Welcome Routes...
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index']);

// Register Routes...
Route::post('/register', [\App\Http\Controllers\RegisterController::class, 'register']);

// Labyrinths Routes...
Route::prefix('labyrinth')->middleware('auth.basic')->group(function () {

    // Get All Labyrinths Routes...
    Route::get('/', [\App\Http\Controllers\LabyrinthController::class, 'index']);

    // Create Labyrinth Routes...
    Route::post('/', [\App\Http\Controllers\LabyrinthController::class, 'store']);

    // Show Labyrinth Routes...
    Route::get('/{id}', [\App\Http\Controllers\LabyrinthController::class, 'show']);

    // Specify the Type of Labyrinth Blocks Routes...
    Route::put('/{id}/playfield/{x}/{y}/{type}', [\App\Http\Controllers\LabyrinthController::class, 'playfield'])->where([
        'x' => '[0-9]+',
        'y' => '[0-9]+',
        'type' => 'empty|filled'
    ]);

    // Specify Labyrinth Start Block Routes...
    Route::put('/{id}/start/{x}/{y}', [\App\Http\Controllers\LabyrinthController::class, 'start'])->where([
        'x' => '[0-9]+',
        'y' => '[0-9]+'
    ]);

    // Specify Labyrinth Start Block Routes...
    Route::put('/{id}/end/{x}/{y}', [\App\Http\Controllers\LabyrinthController::class, 'end'])->where([
        'x' => '[0-9]+',
        'y' => '[0-9]+'
    ]);

    // Get Labyrinth Solution Routes...
    Route::get('/{id}/solution', [\App\Http\Controllers\LabyrinthController::class, 'solution']);

});
