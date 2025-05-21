<?php

namespace App\Http\Controllers;

use App\Models\Shifts;

use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
     public function index()
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'total_patients' => User::where('status', 'paciente')->where('user_type', 'usuario')->count(),
                'male_patients' => User::where('gender', 'masculino')->where('user_type', 'usuario')->where('status', 'paciente')->count(),
                'female_patients' => User::where('gender', 'femenino')->where('user_type', 'usuario')->where('status', 'paciente')->count(),

                'total_users' => User::where('status', 'aspirante')->where('user_type', 'usuario')->count(),
                'male_users' => User::where('gender', 'masculino')->where('user_type', 'usuario')->where('status', 'aspirante')->count(),
                'female_users' => User::where('gender', 'femenino')->where('user_type', 'usuario')->where('status', 'aspirante')->count(),

                'total_doctors' => User::where('user_type', 'doctor')->count(),

                'total_patients_fisica' => Shifts::where('id_therapy', '1')->distinct('id_patient')->count(),
            ]
        ]);
    }
}
