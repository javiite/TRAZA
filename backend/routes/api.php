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
    // Crear (solo Admin/Gerencia/Administracion)
    Route::post('/equipment', [EquipmentController::class, 'store']);
       //  ->middleware('role:Admin|Gerencia|Administracion'); autenticacion de usuarios con roles 

    Route::put('/equipment/{equipment}', [EquipmentController::class, 'update']);
     // ->middleware('role:Admin|Gerencia|Administracion'); autenticacion de usuarios con roles
     
    Route::delete('/equipment/{equipment}', [\App\Http\Controllers\EquipmentController::class, 'destroy']);

    // ->middleware('role:Admin|Gerencia|Administracion');
     // 📂 Papelera
    Route::get('/equipment/trash', [EquipmentController::class, 'trash']);            // listar papelera
    Route::patch('/equipment/{id}/restore', [EquipmentController::class, 'restore']); // restaurar
    Route::delete('/equipment/{id}/force', [EquipmentController::class, 'forceDelete']); // borrar definitivo

   Route::get('/equipment/{id}', [EquipmentController::class, 'show']); // detalle
       // >middleware('role:Admin|Gerencia|Administracion|ChoferTecnico')
// Nota: lectura para cualquier usuario autenticado (sin middleware de rol extra)
    



});
