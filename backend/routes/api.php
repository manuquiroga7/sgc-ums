<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuqueController;
use App\Http\Controllers\CertificadoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\TipoCertificadoController;
use Illuminate\Support\Facades\Route;

// Healthcheck público (para verificar que la API responde)
Route::get('/health', fn () => response()->json([
    'status' => 'ok',
    'app' => config('app.name'),
    'time' => now()->toIso8601String(),
]));

// Autenticación
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Datos maestros (marítimo)
    Route::apiResource('buques', BuqueController::class)
        ->parameters(['buques' => 'buque']);
    Route::apiResource('productos', ProductoController::class)
        ->parameters(['productos' => 'producto']);
    Route::apiResource('tipos-certificado', TipoCertificadoController::class)
        ->parameters(['tipos-certificado' => 'tipoCertificado']);

    // Certificaciones (wizard): reservar número + crear borrador + listado/detalle
    Route::post('certificados/reservar-numero', [CertificadoController::class, 'reservarNumero']);
    Route::post('certificados/liberar-numero', [CertificadoController::class, 'liberarNumero']);
    Route::get('certificados/{certificado}/pdf', [CertificadoController::class, 'pdf']);
    Route::apiResource('certificados', CertificadoController::class)
        ->only(['index', 'store', 'show'])
        ->parameters(['certificados' => 'certificado']);
});
