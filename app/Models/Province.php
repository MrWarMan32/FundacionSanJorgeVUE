<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    protected $table = 'provinces';

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name_province',
    ];

    /**
     * Get all of the cantons for the Province.
     */
    public function cantons(): HasMany
    {
        return $this->hasMany(Canton::class, 'id_province');
    }
}
