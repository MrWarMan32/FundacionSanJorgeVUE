<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Therapies;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppointmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return Inertia::render('appointments/index', [
            'appointments' => Appointment::with(['doctor', 'therapy'])->get(),
            'doctor' => User::where('user_type', 'doctor')->select('id', 'name', 'last_name')->get(),
            'therapies' => Therapies::select('id', 'name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $doctorId = $request->id_doctor;
        $therapyId = $request->id_therapy;

        $doctor = $doctorId ? User::find($doctorId) : null;
        $therapy = $therapyId ? Therapies::find($therapyId) : null;

        $doctors = User::where('user_type', 'doctor')->get(['id', 'name', 'last_name']);
        $therapies = Therapies::all(['id', 'name']);

        return Inertia::render('appointments/create', [
            'doctor' => $doctor,
            'therapy' => $therapy,
            'doctors' => $doctors,
            'therapies' => $therapies,
            'weekdays' => ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $start = Carbon::createFromTimeString($request->start_time);
        $end = Carbon::createFromTimeString($request->end_time);
        $interval = $request->interval_minutes;
    
        foreach ($request->selected_days as $day) {
            $current = $start->copy();
            while ($current->lt($end)) {
                $next = $current->copy()->addMinutes($interval);
    
                if ($next->gt($end)) break;
    
                Appointment::create([
                    'id_doctor' => $request->id_doctor,
                    'id_therapy' => $request->id_therapy,
                    'day' => $day,
                    'start_time' => $current->format('H:i'),
                    'end_time' => $next->format('H:i'),
                    'available' => true,
                ]);
    
                $current = $next;
            }
        }
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
        $appointment = Appointment::with(['doctor', 'therapy'])->findOrFail($id);

        $doctors = User::where('user_type', 'doctor')->get(['id', 'name', 'last_name']);
        $therapies = Therapies::all(['id', 'name']);

        return Inertia::render('appointments/edit', [
            'appointment' => $appointment,
            'doctors' => $doctors,
            'therapies' => $therapies,
            'weekdays' => ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_doctor' => 'required|exists:users,id',
            'id_therapy' => 'required|exists:therapies,id',
            'day' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);
    
        $appointment = Appointment::findOrFail($id);
        $appointment->update($request->all());
    
        return redirect()->route('appointments.index')->with('success', 'Horario actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
    }
}
