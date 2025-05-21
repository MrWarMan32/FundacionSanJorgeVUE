<?php

namespace App\Http\Controllers;

use App\Models\Doctor_Therapies;
use App\Models\Therapies;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Doctor_TherapiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctorTherapies = Doctor_Therapies::with(['doctor', 'therapy'])->get();

        // IDs de doctores que ya tienen terapia asignada
        $assignedDoctorIds = Doctor_Therapies::pluck('id_doctor')->toArray();

        // Doctores que aún no tienen una terapia asignada
        $unassignedDoctors = User::where('user_type', 'doctor')
        ->whereNotIn('id', $assignedDoctorIds)
        ->get();

        return Inertia::render('doctor_therapies/index', [
            'doctor_therapies' => $doctorTherapies,
            'unassignedDoctors' => $unassignedDoctors,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $selectedDoctorId = $request->input('selectedDoctorId');
        $doctors = User::where('user_type', 'doctor')->get(['id', 'name', 'last_name']);
        $therapies = Therapies::all(['id', 'name']);

        return Inertia::render('doctor_therapies/create', [
            'doctors' => $doctors,
            'therapies' => $therapies,
            'selectedDoctorId' => $selectedDoctorId,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_doctor' => 'required|exists:users,id',
            'id_therapy' => 'required|exists:therapies,id',
        ]);
    
        Doctor_Therapies::create($request->only('id_doctor', 'id_therapy'));
    
        return redirect()->route('doctor_therapies.index')->with('success', 'Terapia asignada correctamente.');
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
    public function edit($id)
    {
        $doctorTherapy = Doctor_Therapies::findOrFail($id);
        $doctors = User::where('user_type', 'doctor')->get();
        $therapies = Therapies::all();

        return Inertia::render('doctor_therapies/edit', [
            'doctor_therapies' => $doctorTherapy,
            'doctors' => $doctors,
            'therapies' => $therapies,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_doctor' => 'required|exists:users,id',
            'id_therapy' => 'required|exists:therapies,id',
        ]);
    
        $doctorTherapy = Doctor_Therapies::findOrFail($id);
        $doctorTherapy->update($validated);
    
        return redirect()->route('doctor_therapies.index')->with('success', 'Asignación actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function destroyDoctor($id)
    {
        $doctor = User::findOrFail($id);

        if ($doctor->user_type === 'doctor') {
            $doctor->delete();
            return redirect()->route('doctor_therapies.index')->with('success', 'Doctor eliminado correctamente.');
        }

        return redirect()->route('doctor_therapies.index')->with('error', 'El usuario no es un doctor.');
    }
}
