<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointments';

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_doctor',
        'id_patient',
        'id_therapy',
        'day',
        'start_time',
        'end_time',
        'available', 
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'id_doctor');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'id_patient');
    }

    public function therapy()
    {
        return $this->belongsTo(Therapies::class, 'id_therapy');
    }
}
