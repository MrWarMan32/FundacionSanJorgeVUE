<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shifts extends Model
{
    protected $table = 'shifts';

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
        'id_appointment',
        'is_recurring',
        'id_parent_shift',
        'date',
        'notes',    
    ];
}
