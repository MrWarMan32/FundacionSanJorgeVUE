<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Therapies extends Model
{
    protected $table = 'therapies';

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'duration',
    ];

    public function doctors()
    {
        return $this->belongsToMany(User::class, 'doctor_therapies', 'id_therapy', 'id_doctor');
    }

}
