<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Canton;
use App\Models\Parish;
use App\Models\Province;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addresses = Address::with(['user', 'province', 'canton', 'parish'])->get();

        return inertia('addresses/index', [
            'address' => $addresses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $userId = $request->input('id_user');

        return Inertia::render('addresses/create', [
            'users' => User::where('status', ['aspirante', 'paciente'])->get(),
            'provinces' => Province::all(),
            'cantons' => Canton::all(),
            'parishes' => Parish::all(),
            'id_user' => $userId,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_user' => 'required|exists:users,id',
            'id_province' => 'required|exists:provinces,id',
            'id_canton' => 'required|exists:cantons,id',
            'id_parish' => 'required|exists:parishes,id',
            'site' => 'nullable|string|max:255',
            'principal_street' => 'nullable|string|max:255',
            'secondary_street' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255',
        ]);
    
        $address = Address::create($validated);

        $user = User::findOrFail($validated['id_user']);
        $user->id_address = $address->id;
        $user->save();
    
        return redirect()->route('users.index');
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
    public function edit($id_user)
    {
        $user = User::findOrFail($id_user);
        $address = $user->address;
    
        if (!$address) {
            return redirect()->route('addresses.create', ['id_user' => $user->id])
                             ->with('warning', 'Este usuario no tiene dirección. Por favor crea una.');
        }
    
        return Inertia::render('addresses/edit', [
            'address' => $address,
            'provinces' => Province::all(),
            'cantons' => Canton::all(),
            'parishes' => Parish::all(),
            //'user' => $user,
            'user' => $address->user,
            'users' => User::where('user_type', 'usuario')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  Address $address)
    {
        $validated = $request->validate([
            'id_province' => 'required|exists:provinces,id',
            'id_canton' => 'required|exists:cantons,id',
            'id_parish' => 'required|exists:parishes,id',
            'site' => 'nullable|string',
            'principal_street' => 'nullable|string',
            'secondary_street' => 'nullable|string',
            'reference' => 'nullable|string',
        ]);
    
        $address->update($validated);
    
        return redirect()->route('users.index')->with('success', 'Dirección actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

}
