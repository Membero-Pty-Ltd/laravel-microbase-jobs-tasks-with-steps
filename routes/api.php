<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\PilotTaskTestController;

Route::get('/hello', function () {
    return response()->json(['ok' => true, 'message' => 'hello']);
});

// Pilot task test (any valid Sanctum Access)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/pilot-task-test', [PilotTaskTestController::class, 'create']);
    Route::get('/pilot-task-test', [PilotTaskTestController::class, 'show']);
});

// CREATE
Route::prefix('create')
    ->middleware(['auth:sanctum', 'access.role.create'])
    ->group(function () {
        Route::get('/hello', function () {
            return response()->json(['ok' => true, 'message' => 'hello', 'role' => 'create']);
        });
    });

// CREATE-MIRROR
Route::prefix('create-mirror')
    ->middleware(['auth:sanctum', 'access.role.create-mirror'])
    ->group(function () {
        Route::get('/hello', function () {
            return response()->json(['ok' => true, 'message' => 'hello', 'role' => 'create-mirror']);
        });
    });

// MIRROR
Route::prefix('mirror')
    ->middleware(['auth:sanctum', 'access.role.mirror'])
    ->group(function () {
        Route::get('/hello', function () {
            return response()->json(['ok' => true, 'message' => 'hello', 'role' => 'mirror']);
        });
    });

Route::fallback(function (Request $request) {
    return response()->json([
        'ok' => false,
        'error' => 'Not Found',
        'path' => $request->path(),
    ], Response::HTTP_NOT_FOUND);
});