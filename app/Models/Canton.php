<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Canton extends Model
{
    use HasFactory;

    protected $table = 'cantons';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_province',
        'name_canton',
    ];

    /**
     * Get the province that owns the Canton.
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'id_province');
    }

    /**
     * Get all of the parishes for the Canton.
     */
    public function parishes(): HasMany
    {
        return $this->hasMany(Parish::class, 'id_canton');
    }
}
