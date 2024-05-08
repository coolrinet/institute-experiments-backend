<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'is_admin',
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
            'is_admin' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function machineries(): HasMany
    {
        return $this->hasMany(Machinery::class);
    }

    public function machineryParameters(): HasMany
    {
        return $this->hasMany(MachineryParameter::class);
    }

    public function research(): HasMany
    {
        return $this->hasMany(Research::class, 'author_id');
    }

    public function participatoryResearch(): BelongsToMany
    {
        return $this->belongsToMany(Research::class)
            ->as('participatoryResearch');
    }

    public function experiments(): HasMany
    {
        return $this->hasMany(Experiment::class);
    }
}
