<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $filable = [
        'id_user',
        'id_province',
        'id_canton',
        'id_parish',
        'site',
        'principal_street',
        'secondary_street',
        'reference',
    ];
}
