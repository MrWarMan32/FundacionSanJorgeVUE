<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor_Therapies extends Model
{
    protected $table = 'doctor_therapies';

    protected $fillable = [
        'id_doctor',
        'id_therapy',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'id_doctor');
    }

    public function therapy()
    {
        return $this->belongsTo(Therapies::class, 'id_therapy');
    }
}
