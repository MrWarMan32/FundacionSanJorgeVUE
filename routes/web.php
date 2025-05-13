<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\Doctor_TherapiesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ShiftsController;
use App\Http\Controllers\TherapiesController;
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
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Pacientes Routes
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');

    //doctor routes
    Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
    Route::get('/doctors/create', [DoctorController::class, 'create'])->name('doctors.create');
    Route::post('/doctors', [DoctorController::class, 'store'])->name('doctors.store');
    Route::get('/doctors/{user}/edit', [DoctorController::class, 'edit'])->name('doctors.edit');
    Route::put('/doctors/{user}', [DoctorController::class, 'update'])->name('doctors.update');

    // Doctor_Therapies Routes
    Route::get('/doctor_therapies', [Doctor_TherapiesController::class, 'index'])->name('doctor_therapies.index');
    Route::get('/doctor_therapies/create', [Doctor_TherapiesController::class, 'create'])->name('doctor_therapies.create');
    Route::post('/doctor_therapies', [Doctor_TherapiesController::class, 'store'])->name('doctor_therapies.store');
    Route::get('/doctor_therapies/{doctor_therapy}/edit', [Doctor_TherapiesController::class, 'edit'])->name('doctor_therapies.edit');
    Route::put('/doctor_therapies/{doctor_therapy}', [Doctor_TherapiesController::class, 'update'])->name('doctor_therapies.update');
    Route::delete('/doctor_therapies/{doctor_therapy}', [Doctor_TherapiesController::class, 'destroy'])->name('doctor_therapies.destroy');

    //emilinar doctores desde la tabla doctors_therapies
    Route::delete('/doctor_therapies/delete_doctor/{id}', [Doctor_TherapiesController::class, 'deleteDoctorWithAssignment'])
    ->name('doctor_therapies.deleteDoctorWithAssignment');

    // Address routes
    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::get('/addresses/create', [AddressController::class, 'create'])->name('addresses.create');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::get('/addresses/{id_user}/edit', [AddressController::class, 'edit'])->name('addresses.edit');
    Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');

    // Therapies Routes
    Route::get('/therapies', [TherapiesController::class, 'index'])->name('therapies.index');
    Route::get('/therapies/create', [TherapiesController::class, 'create'])->name('therapies.create');
    Route::post('/therapies', [TherapiesController::class, 'store'])->name('therapies.store');
    Route::get('/therapies/{therapy}/edit', [TherapiesController::class, 'edit'])->name('therapies.edit');
    Route::put('/therapies/{therapy}', [TherapiesController::class, 'update'])->name('therapies.update');
    Route::delete('/therapies/{therapy}', [TherapiesController::class, 'destroy'])->name('therapies.destroy');

    //Appointments Routes
    Route::get('/appointments', [AppointmentsController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AppointmentsController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentsController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointments}/edit', [AppointmentsController::class, 'edit'])->name('appointments.edit');
    Route::put('/appointments/{appointments}', [AppointmentsController::class, 'update'])->name('appointments.update');




    // Shifts Routes
    Route::get('/shifts', [ShiftsController::class, 'index'])->name('shifts.index');
    Route::get('/shifts/create', [ShiftsController::class, 'create'])->name('shifts.create');
    Route::post('/shifts', [ShiftsController::class, 'store'])->name('shifts.store');
    Route::get('/shifts/{shift}/edit', [ShiftsController::class, 'edit'])->name('shifts.edit');
    Route::put('/shifts/{shift}', [ShiftsController::class, 'update'])->name('shifts.update');
    Route::delete('/shifts/{shift}', [ShiftsController::class, 'destroy'])->name('shifts.destroy');


    //ruta para convertir un usuario a paciente
    Route::patch('/users/{user}/convert-to-paciente', [UserController::class, 'convertToPaciente'])->name('users.convertToPaciente');
    //ruta para convertir un paciente a usuario 
    Route::patch('/users/{user}/convert-to-aspirante', [PatientController::class, 'convertToAspirante'])->name('patients.convertToAspirante');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
