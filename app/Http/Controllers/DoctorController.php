<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('doctors/index', [
            'users' => User::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('doctors/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'id_card' => 'required|string|max:50|unique:users',
            'gender' => 'required|in:masculino,femenino,otro',
            'birth_date' => 'required|date',
            'age' => 'required|integer|min:18|max:100',
            'ethnicity' => 'nullable|string|max:100',
            'university_name' => 'required|string|max:255',
            'degree_title' => 'required|string|max:255',
            'graduation_year' => 'required|date',
            'speciality' => 'nullable|string|max:255',
            'certifications' => 'nullable|string|max:1000',
            'phone' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:users',
            'user_type' => 'required|in:doctor',
        ]);
    
        User::create($validated);
    
        return redirect()->route('doctors.index')->with('success', 'Doctor creado exitosamente.');
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
        return Inertia::render('doctors/edit', [
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
            'phone' => ['nullable', 'string', 'max:20'],
            'university_name' => 'required|string|max:255',
            'degree_title' => 'required|string|max:255',
            'graduation_year' => 'required|date',
            'speciality' => 'nullable|string|max:255',
            'certifications' => 'nullable|string|max:1000',
        ]);

        $user->update($validatedData);

        return Redirect::route('doctors.index')->with('success', 'Terapeuta actualizado con éxito.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
