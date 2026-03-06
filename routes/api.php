<?php

declare(strict_types=1);

use App\Http\Controllers\PilotTaskTestController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

Route::get('/hello', static fn (): JsonResponse => response()->json(['ok' => true, 'message' => 'hello']));

Route::middleware(['auth:sanctum'])->group(function (): void {
    Route::post('/pilot-task-test', [PilotTaskTestController::class, 'create']);
    Route::get('/pilot-task-test', [PilotTaskTestController::class, 'show']);
});

Route::prefix('create')
    ->middleware(['auth:sanctum', 'access.role.create'])
    ->group(function (): void {
        Route::get('/hello', static fn (): JsonResponse => response()->json(['ok' => true, 'message' => 'hello', 'role' => 'create']));
    });

Route::prefix('create-mirror')
    ->middleware(['auth:sanctum', 'access.role.create-mirror'])
    ->group(function (): void {
        Route::get('/hello', static fn (): JsonResponse => response()->json(['ok' => true, 'message' => 'hello', 'role' => 'create-mirror']));
    });

Route::prefix('mirror')
    ->middleware(['auth:sanctum', 'access.role.mirror'])
    ->group(function (): void {
        Route::get('/hello', static fn (): JsonResponse => response()->json(['ok' => true, 'message' => 'hello', 'role' => 'mirror']));
    });

Route::fallback(static fn (Request $request): JsonResponse => response()->json([
    'ok' => false,
    'error' => 'Not Found',
    'path' => $request->path(),
], Response::HTTP_NOT_FOUND));
