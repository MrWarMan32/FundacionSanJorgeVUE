<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Exports\PatientExport;
use Maatwebsite\Excel\Facades\Excel;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('patients/index', [
            'users' => User::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function edit(User $user)
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

    public function convertToAspirante(User $user)
    {
        if ($user->status === 'paciente') {
            $user->update(['status' => 'aspirante']);
            return Redirect::route('patients.index')->with('success', 'Aspirante convertido a paciente con éxito.');
        }

        return Redirect::route('patients.index')->with('error', 'El usuario no es un aspirante.');
    }

    public function export($id)
    {
        $patient = User::findOrFail($id);
        $filename = $patient->name. ' '. $patient->last_name.'.xlsx';

        return Excel::download(new PatientExport($id), $filename);
    }
}
