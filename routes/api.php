<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AddressController;

/* ---------- Healthcheck ---------- */
Route::get('/health', fn () => response()->json(['status' => 'ok']));

/* ---------- Autenticación ---------- */
Route::prefix('auth')->middleware('api')->group(function () {

    /* Público (sin token) */
    Route::post('register',        [AuthController::class, 'register']);
    Route::post('login',           [AuthController::class, 'login']);
    Route::post('forgot-password', [PasswordResetController::class, 'sendResetLinkEmail']);
    Route::post('reset-password',  [PasswordResetController::class, 'reset']);

    /* Protegido (requiere token) */
    Route::middleware('auth:api')->group(function () {
        Route::post('logout',          [AuthController::class, 'logout']);
        Route::post('refresh',         [AuthController::class, 'refresh']);
        Route::post('me',              [AuthController::class, 'me']);
        Route::post('change-password', [PasswordResetController::class, 'changePassword']); // 👈 nuevo
    });
});

/* ---------- Gestión de direcciones (token requerido, cualquier rol) ---------- */
Route::middleware('auth:api')
     ->apiResource('direcciones', AddressController::class)
     ->except('show')
     ->names('addresses');

/* ---------- Rutas protegidas por rol ---------- */
Route::middleware(['auth:api', 'role:admin'])->prefix('admin')->group(function () {
    Route::post('register-veterinarian', [AdminController::class, 'registerVeterinarian']);
});

Route::middleware(['auth:api', 'role:veterinario'])->prefix('vet')->group(function () {
    // Endpoints exclusivos de veterinarios
});

Route::middleware(['auth:api', 'role:cliente'])->prefix('cliente')->group(function () {
    // Endpoints exclusivos de clientes
});
