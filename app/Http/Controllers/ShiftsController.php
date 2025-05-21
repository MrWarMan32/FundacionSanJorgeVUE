<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Shifts;
use App\Models\Therapies;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class ShiftsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shifts = Shifts::with(['patient', 'doctor', 'therapy', 'appointment'])
        ->get();

        return Inertia::render('shifts/index', [
            'shifts' => $shifts,
        ]);
    }

    public function indexregister()
    {
        $shifts = Shifts::with(['patient', 'doctor', 'therapy', 'appointment'])
        ->get();

        return Inertia::render('shifts/register', [
            'shifts' => $shifts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
        $patients = User::where('status', 'paciente')->get(['id', 'name', 'last_name']);
        $doctors = User::where('user_type', 'doctor')->get(['id', 'name', 'last_name']);
        $therapies = Therapies::all(['id', 'name']);
        $appointments = Appointment::all(['id', 'day','start_time', 'end_time', 'id_doctor', 'id_therapy', 'available']);

        return inertia('shifts/create', compact('patients', 'therapies', 'doctors', 'appointments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_patient' => 'required|exists:users,id',
            'id_therapy' => 'required|exists:therapies,id',
            'id_doctor' => 'required|exists:users,id',
            'id_appointment' => 'required|exists:appointments,id',
            'date' => 'required|date',
        ]);
    
        $appointment = Appointment::findOrFail($request->id_appointment);
    
        // Crear la cita (shift)
        Shifts::create([
            'id_patient' => $request->id_patient,
            'id_doctor' => $request->id_doctor,
            'id_therapy' => $request->id_therapy,
            'id_appointment' => $request->id_appointment,
            'start_time' => $appointment->start_time,
            'end_time' => $appointment->end_time,
            'date' => $request->date,
            'status' => 'pendiente',
            'is_recurring' => true,
        ]);
    
        // Actualizar la disponibilidad del horario
        $appointment = Appointment::find($request->id_appointment);
        if ($appointment) {
            $appointment->available = 0;
            $appointment->id_patient = $request->id_patient;
            $appointment->save();
        }
    
        return redirect()->route('shifts.index')->with('success', 'Cita creada correctamente.');
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
    public function edit(Shifts $shift)
    {
        $shift->load(['patient', 'doctor', 'therapy']);

        return Inertia::render('shifts/edit', [
            'shift' => $shift,
            'patients' => User::where('status', 'paciente')->select('id', 'name', 'last_name')->get(),
            'doctors' => User::where('user_type', 'doctor')->select('id', 'name', 'last_name')->get(),
            'therapies' => Therapies::select('id', 'name')->get(),
            'appointments' => Appointment::select('id', 'day', 'start_time', 'end_time', 'id_doctor', 'id_therapy', 'available')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shifts $shift)
    {
        
        $validated = $request->validate([
            'id_patient' => 'required|exists:users,id',
            'id_doctor' => 'required|exists:users,id',
            'id_therapy' => 'required|exists:therapies,id',
            'id_appointment' => 'required|exists:appointments,id',
            'date' => 'required|date',
            'is_recurring' => 'required|boolean',
        ]);

         //Si cambió el appointment, actualizamos su disponibilidad
        if ($shift->id_appointment != $request->id_appointment) {
            Appointment::where('id', $shift->id_appointment)
                ->update([
                    'available' => 1,
                    'id_patient' => null,
                ]);
                Appointment::where('id', $request->id_appointment)
                ->update([
                    'available' => 0,
                    'id_patient' => $request->id_patient,
                ]);
        }

        $shift->update($validated);

        return redirect()->route('shifts.index')->with('success', 'Cita actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shifts $shift)
    {
         // Obtener la cita relacionada
        $appointment = $shift->appointment;

        // Restaurar disponibilidad del horario
        if ($appointment) {
            $appointment->available = 1;
            $appointment->id_patient = null;
            $appointment->save();
        }

        // Eliminar la cita
        $shift->delete();

        return redirect()->route('shifts.index')->with('success', 'Cita eliminada correctamente.');

    }

    //Generacion de Nuevas Citas Basadas en las Anteriores
    public function generateWeekly()
    {
        $shifts = Shifts::where(function ($query) {
            $query->where('is_recurring', 1)->where('status', 'pendiente');
        })->orWhere(function ($query) {
            $query->where('is_recurring', 0)->whereNotNull('id_parent_shift')->where('status', 'pendiente');
        })->get();

        foreach ($shifts as $shift) {
            $original = $shift;
            $dateToUse = \Carbon\Carbon::parse($shift->date)->addWeek();

            // Si fue editada por emergencia
            if (!$shift->is_recurring && $shift->id_parent_shift) {
                $original = Shifts::find($shift->id_parent_shift);
                if (!$original) continue;

                // Usar fecha de la edición
                $dateToUse = \Carbon\Carbon::parse($shift->date)->addWeek();
            }

            // Crear la nueva cita
            Shifts::create([
                'id_patient' => $original->id_patient,
                'id_therapy' => $original->id_therapy,
                'id_doctor' => $original->id_doctor,
                'id_appointment' => $original->id_appointment,
                'date' => $dateToUse->toDateString(),
                'status' => 'pendiente',
                'is_recurring' => 1,
                'id_parent_shift' => $original->id,
            ]);

            //Marcar como completadas asegurando guardado
            if ($original->status !== 'completada') {
                $original->status = 'completada';
                $original->save();
            }

            if ($shift->status !== 'completada') {
                $shift->status = 'completada';
                $shift->save();
            }
        }

        return redirect()->route('shifts.index')->with('success', 'Citas generadas con éxito.');
    }

    //Generacion de certificado de uns cita
    public function generateCertificate($id)
    {
        $shift = Shifts::with(['patient', 'therapy', 'doctor' ,'appointment'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.certificate', [
            'shift' => $shift,
            'patient' => $shift->patient,
            'therapy' => $shift->therapy,
            'doctor' => $shift->doctor,
            'appointment' => $shift->appointment,
            'date' => Carbon::parse($shift->date)->translatedFormat('d \d\e F \d\e Y'),
        ]);

        return $pdf->stream("certificado_{$shift->patient->name}_{$shift->patient->last_name}.pdf");
    }

}
