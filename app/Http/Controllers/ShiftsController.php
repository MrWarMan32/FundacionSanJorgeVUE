<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Shifts;
use App\Models\Therapies;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShiftsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shifts = Shifts::with(['patient', 'doctor', 'therapy'])
        ->get();

        return Inertia::render('shifts/index', [
            'shifts' => $shifts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $doctors = User::where('user_type', 'doctor')->get(['id', 'name', 'last_name']);
        $therapies = Therapies::all(['id', 'name']);
        $appointments = Appointment::all();

        return Inertia::render('shifts/create', [
            'doctors' => $doctors,
            'therapies' => $therapies
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
