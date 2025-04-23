<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Parish extends Model
{
    use HasFactory;

    protected $table = 'parishes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_canton',
        'parroquia',
    ];

    /**
     * Get the canton that owns the Parish.
     */
    public function canton(): BelongsTo
    {
        return $this->belongsTo(Canton::class, 'id_canton');
    }
}
