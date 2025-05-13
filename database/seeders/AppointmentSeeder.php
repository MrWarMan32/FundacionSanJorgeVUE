<?php

namespace Database\Seeders;

use App\Models\Therapies;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunos IDs de pacientes, doctores y terapias existentes
        $doctors = User::where('user_type', 'doctor')->pluck('id')->toArray();
        $therapies = Therapies::pluck('id')->toArray();

        $daysOfWeek = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes'];
        $startTime = Carbon::parse('08:00');
        $endTime = Carbon::parse('12:00');
        $intervalMinutes = 20;

        foreach ($daysOfWeek as $day) {
            $currentTime = $startTime->copy();
            while ($currentTime->lt($endTime)) {
                // Seleccionar IDs aleatorios si tienes múltiples pacientes, doctores y terapias
                $patientId = $patients ? $patients[array_rand($patients)] : null;
                $doctorId = $doctors ? $doctors[array_rand($doctors)] : null;
                $therapyId = $therapies ? $therapies[array_rand($therapies)] : null;

                Appointment::create([
                    'id_doctor' => $doctorId,
                    'id_therapy' => $therapyId,
                    'day' => $day,
                    'start_time' => $currentTime->toTimeString(),
                    'end_time' => $currentTime->copy()->addMinutes($intervalMinutes)->toTimeString(),
                    'available' => true,
                ]);

                $currentTime->addMinutes($intervalMinutes);
            }
        }
    }
}
