<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'id_user',
        'id_province',
        'id_canton',
        'id_parish',
        'site',
        'principal_street',
        'secondary_street',
        'reference',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'id_province', 'id');
    }

    public function canton()
    {
        return $this->belongsTo(Canton::class, 'id_canton', 'id');
    }

    public function parish()
    {
        return $this->belongsTo(Parish::class, 'id_parish', 'id');
    }

}
