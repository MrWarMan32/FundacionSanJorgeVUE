<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PatientExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $patientId;

    public function __construct($patientId)
    {
        $this->patientId = $patientId;
    }

    public function collection()
    {
        return User::with('address.province', 'address.canton', 'address.parish')
            ->where('id', $this->patientId)
            ->get([
                'name',
                'last_name',
                'id_card',
                'gender',
                'birth_date',
                'email',
                'ethnicity',
                'disability_type',
                'disability_level',
                'disable_card',
                'id_disable_card',
                'representative_name',
                'representative_last_name',
                'representative_id_card',
                'phone',
                'id_address',
            ])
            ->map(function ($user) {

                $user->address;

                $address = $user->address;

                $fullAddress = $address ? implode(', ', array_filter([
                $address->site,
                $address->principal_street,
                $address->secondary_street,
                $address->reference,
                optional($address->parish)->parroquia,
                optional($address->canton)->name_canton,
                optional($address->province)->name_province,
                ])) : 'No registrada';
                        
                return [
                    'name' => $user->name,
                    'last_name' => $user->last_name,
                    'id_card' => $user->id_card,
                    'gender' => $user->gender,
                    'birth_date' => $user->birth_date,
                    'email' => $user->email,
                    'ethnicity' => $user->ethnicity,
                    'disability_type' => $user->disability_type,
                    'disability_level' => $user->disability_level,
                    'disable_card' => $user->disable_card ? 'Sí' : 'No',
                    'id_disable_card' => $user->id_disable_card,
                    'representative_name' => $user->representative_name,
                    'representative_last_name' => $user->representative_last_name,
                    'representative_id_card' => $user->representative_id_card,
                    'phone' => $user->phone,
                    'address' => $fullAddress,
                ];
        });
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Apellido',
            'Cédula',
            'Género',
            'Fecha de nacimiento',
            'Correo',
            'Etnia',
            'Tipo de discapacidad',
            'Nivel de discapacidad',
            'Posee carnet de discapacidad',
            'N° carnet discapacidad',
            'Nombre del representante',
            'Apellido del representante',
            'Cédula del representante',
            'Teléfono',
            'Dirección',
        ];
    }
}
