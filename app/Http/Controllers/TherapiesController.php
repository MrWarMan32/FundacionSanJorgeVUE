<?php

namespace App\Http\Controllers;

use App\Models\Therapies;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TherapiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('therapies/index', [
            'therapy' => Therapies::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('therapies/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated =  $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'duration' => 'required|string|max:255',
        ]);

        Therapies::create($validated);

        return redirect()->route('therapies.index')->with('success', 'Terapia creada correctamente.');
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
    public function edit(Therapies $therapy)
    {
        return Inertia::render('therapies/edit', [
            'therapy' => $therapy,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Therapies $therapy)
    {
        $validated =  $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'duration' => 'required|string|max:255',
        ]);

        $therapy->update($validated);

        return redirect()->route('therapies.index')->with('success', 'Terapia actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Therapies $therapy)
    {
        $therapy->delete();
    }
}
