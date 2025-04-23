<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\CantonController;
use App\Http\Controllers\ParishController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // User Routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Pacientes Routes
    Route::get('/patients', [UserController::class, 'index'])->name('patients.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');


    // Address routes
    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::get('/addresses/create', [AddressController::class, 'create'])->name('addresses.create');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::get('/addresses/{address}', [AddressController::class, 'show'])->name('addresses.show');
    Route::get('/addresses/{address}/edit', [AddressController::class, 'edit'])->name('addresses.edit');
    Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');

    // API Routes for Provinces, Cantons, and Parishes
    Route::get('/api/provinces', [ProvinceController::class, 'index'])->name('api.provinces.index');
    Route::get('/api/provinces/{province}/cantons', [CantonController::class, 'indexByProvince'])->name('api.provinces.cantons');
    Route::get('/api/provinces/{province}', [ProvinceController::class, 'show'])->name('api.provinces.show');//
    Route::get('/api/cantons/{canton}/parishes', [ParishController::class, 'indexByCanton'])->name('api.cantons.parishes');
    Route::get('/api/cantons/{canton}', [CantonController::class, 'show'])->name('api.cantons.show');//
    Route::get('/api/parishes/{parish}', [ParishController::class, 'show'])->name('api.parishes.show');//

    //ruta para convertir un usuario a paciente
    Route::patch('/users/{user}/convert-to-paciente', [UserController::class, 'convertToPaciente'])->name('users.convertToPaciente');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
