<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FleetController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\UserController;

// Rutas de autenticación (públicas)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');

// Redirigir raíz a dashboard o login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Rutas protegidas (requieren autenticación)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Empresas
    Route::resource('companies', CompanyController::class);

    // Clientes
    Route::resource('clients', ClientController::class);

    // Flotas
    Route::resource('fleets', FleetController::class);

    // Vehículos
    Route::resource('vehicles', VehicleController::class);

    // Rutas
    Route::resource('routes', RouteController::class);

    // Entregas
    Route::resource('deliveries', DeliveryController::class);

    // Usuarios (solo admin)
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // Logout route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

