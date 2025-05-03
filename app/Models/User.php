<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'users';
    
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'id_address',
        'id_card',
        'gender',
        'birth_date',
        'age',
        'ethnicity',

        'university_name',
        'degree_title',
        'graduation_year',
        'speciality',
        'certifications',

        'disable_card',
        'id_disable_card',
        'representative_name',
        'representative_last_name',
        'representative_id_card',
        'phone',
        'disability_type',
        'disability_level',
        'disability_grade',
        'cause_disability',
        'diagnosis',

        'password',
        'user_type',
        'status',
        'email_verified_at',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'disable_card' => 'boolean',
        'disability_type' => 'array',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'id_user');
    }

    protected static function booted()
    {
        static::deleting(function ($user) {
            $user->address()->delete();
        });
    }
}
