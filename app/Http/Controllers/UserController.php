<?php

namespace App\Http\Controllers;

use App\Models\Shifts;
use App\Models\Therapies;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PatientExport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('users/index', [
            'users' => User::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('users/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:255',
            'last_name' => 'required|string|min:3|max:255',
            'id_card' => 'required|integer|digits_between:8,10|unique:users',
            'gender' => 'required|string|in:masculino,femenino,otro',
            'birth_date' => 'nullable|date|before:today',
            'age' => 'nullable|integer|min:0|max:120',
            'ethnicity' => 'nullable|string|max:255',

            'university_name' => 'nullable|string|max:255',
            'degree_title' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|date',
            'speciality' => 'nullable|string|max:255',
            'certifications' => 'nullable|string|max:1000',

            'disable_card' => 'nullable|boolean',
            'id_disable_card' => 'nullable|integer',
            'representative_name' => 'nullable|string|min:3|max:255',
            'representative_last_name' => 'nullable|string|min:3|max:255',
            'representative_id_card' => 'nullable|integer',
            'phone' => 'nullable|string|digits_between:8,15',
            'disability_type' => 'nullable|array',
            'disability_type.*' => 'nullable|string|max:255',
            'disability_level' => 'nullable|string|max:255',
            'disability_grade' => 'nullable|integer|min:0|max:100',
            'cause_disability' => 'nullable|string|max:255',
            'diagnosis' => 'nullable|string|max:255',

            'password' => 'nullable|string|min:8|confirmed',
            'email' => 'required|string|email|max:255|unique:users',
            //'id_address', //=> 'nullable|integer|exists:addresses,id',
        ]);

        $disabilityTypes = $request->input('disability_type', []);
        
        $encodedDisabilityTypes = json_encode(array_filter(array_map('trim', $disabilityTypes)));

        $userData = [
            'name' => $request->name,
            'last_name' => $request->last_name,
            'id_card' => $request->id_card,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'age' => $request->age,
            'ethnicity' => $request->ethnicity,
            'disable_card' => $request->boolean('disable_card'),
            'id_disable_card' => $request->id_disable_card,
            'representative_name' => $request->representative_name,
            'representative_last_name' => $request->representative_last_name,
            'representative_id_card' => $request->representative_id_card,
            'phone' => $request->phone,
            'disability_type' => $encodedDisabilityTypes,
            'disability_level' => $request->disability_level,
            'disability_grade' => $request->disability_grade,
            'cause_disability' => $request->cause_disability,
            'diagnosis' => $request->diagnosis,
            'email' => $request->email,
            //'user_type' => 'usuario',
            //'status' => 'aspirante',
        ];

        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }

        $user = User::create($userData);

        return redirect()->route('addresses.create', ['id_user' => $user->id]);
        //return redirect()->route('users.index')->with('success', 'User created successfully.');
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
    public function edit( User $user)
    {
        return Inertia::render('users/edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'id_card' => ['required', 'integer', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'gender' => ['nullable', 'string', 'max:50'],
            'birth_date' => ['nullable', 'date'],
            'age' => ['nullable', 'integer', 'min:0'],
            'ethnicity' => ['nullable', 'string', 'max:100'],
            'disable_card' => ['nullable', 'boolean'],
            'id_disable_card' => ['nullable', 'integer'],
            'representative_name' => ['nullable', 'string', 'max:255'],
            'representative_last_name' => ['nullable', 'string', 'max:255'],
            'representative_id_card' => ['nullable', 'integer'],
            'phone' => ['nullable', 'string', 'max:20'],
            'disability_type' => ['nullable', 'array'],
            'disability_type.*' => ['string'],
            'disability_level' => ['nullable', 'string', 'max:100'],
            'disability_grade' => ['nullable', 'integer', 'min:0', 'max:100'],
            'cause_disability' => ['nullable', 'string', 'max:255'],
            'diagnosis' => ['nullable', 'string', 'max:255'],
        ]);

        $validatedData['disability_type'] = json_encode(array_filter(array_map('trim', $validatedData['disability_type'] ?? [])));

        $user->update($validatedData);

        if ($user->status === 'aspirante') {
            return Redirect::route('users.index')->with('success', 'Aspirante actualizado con éxito.');
        }if ($user->status === 'paciente') {
            return Redirect::route('patients.index')->with('success', 'Paciente actualizado con éxito.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
    }

    /**
     * Convert a user to a patient.
     */
    public function convertToPaciente(Request $request, User $user)
    {
        if ($user->status === 'aspirante') {
            $user->update(['status' => 'paciente']);
            return Redirect::route('users.index')->with('success', 'Aspirante convertido a paciente con éxito.');
        }

        return Redirect::route('users.index')->with('error', 'El usuario no es un aspirante.');
    }

    public function generateGeneral($id)
    {
        // 1. Buscar el paciente
        $patient = User::findOrFail($id);

        // 2. Buscar las citas (shifts) del paciente con relaciones cargadas
        $shifts = Shifts::with(['therapy', 'doctor', 'appointment'])
            ->where('id_patient', $patient->id)
            ->get();

        // 3. Pasar los datos a la vista del certificado
        $pdf = Pdf::loadView('pdf.generalcertificate', [
            'patient' => $patient,
            'shifts' => $shifts,
        ]);

        return $pdf->stream("certificado_general_'{$patient->id}.pdf");
    }

    public function export($id)
    {
        $user = User::findOrFail($id);
        $filename = $user->name. ' '. $user->last_name.'.xlsx';

        return Excel::download(new PatientExport($id), $filename);
    }
}
