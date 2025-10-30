<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EquipmentController;

// Rutas públicas (sin token)
Route::get('/ping', fn () => response()->json(['pong' => true]));
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas por token (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);

    // Logout (opcional, pero útil)
    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'ok']);
    });

    // Nuevo: listado de equipos
    Route::get('/equipment', [EquipmentController::class, 'index']);
});
