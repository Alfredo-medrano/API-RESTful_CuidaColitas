<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PetController;

//
// ---------- Healthcheck ----------
//
Route::get('/health', fn () => response()->json(['status' => 'ok']));

//
// ---------- Autenticación (público) ----------
//
Route::prefix('auth')->middleware('api')->group(function () {
    Route::post('register',        [AuthController::class, 'register']);
    Route::post('login',           [AuthController::class, 'login']);
    Route::post('forgot-password', [PasswordResetController::class, 'sendResetLinkEmail']);
    Route::post('reset-password',  [PasswordResetController::class, 'reset']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout',          [AuthController::class, 'logout']);
        Route::post('refresh',         [AuthController::class, 'refresh']);
        Route::post('me',              [AuthController::class, 'me']);
        Route::post('change-password', [PasswordResetController::class, 'changePassword']);
    });
});

//
// ---------- Gestión de mascotas (token requerido) ----------
//
Route::middleware('auth:api')->group(function () {
    Route::get('/mascotas',                [PetController::class, 'index'])->name('pets.index');
    Route::post('/mascotas',               [PetController::class, 'store'])->name('pets.store');
    Route::get('/mascotas/{id}',           [PetController::class, 'show'])->name('pets.show');
    Route::put('/mascotas/{id}',           [PetController::class, 'update'])->name('pets.update');
    Route::post('/mascotas/{id}/foto',     [PetController::class, 'uploadPhoto'])->name('pets.photo');
});

//
// ---------- Gestión de citas (token requerido) ----------
//
Route::middleware('auth:api')->group(function () {
    // Cliente agenda citas
    Route::post('/citas', [AppointmentController::class, 'store'])->name('appointments.store');
});

//
// ---------- Citas – Cliente (rol cliente) ----------
//
Route::middleware(['auth:api','role:cliente'])->prefix('cliente')->group(function () {
    Route::get('citas', [AppointmentController::class, 'clientAppointments'])->name('cliente.citas');
});

//
// ---------- Citas – Veterinario (rol veterinario) ----------
//
Route::middleware(['auth:api','role:veterinario'])->prefix('vet')->group(function () {
    Route::get('citas',            [AppointmentController::class, 'veterinarianAppointments'])->name('vet.citas');
    Route::get('citas/{id}',       [AppointmentController::class, 'veterinarianShow'])->name('vet.citas.show');
    Route::put('citas/{id}/estado',[AppointmentController::class, 'updateStatus'])->name('vet.citas.estado');
});

//
// ---------- Gestión de direcciones (token requerido) ----------
//
Route::middleware('auth:api')
    ->apiResource('direcciones', AddressController::class)
    ->except('show')
    ->names('addresses');

//
// ---------- Administración – Veterinarios (rol admin) ----------
//
Route::middleware(['auth:api','role:admin'])->prefix('admin')->group(function () {
    Route::post('register-veterinarian', [AdminController::class, 'registerVeterinarian'])->name('admin.register-veterinarian');
});
