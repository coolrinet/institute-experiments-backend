<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Research extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'is_public',
        'machinery_id',
    ];

    protected function casts(): array
    {
        return [
            'last_experiment_date' => 'immutable_date:j F Y',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function machinery(): BelongsTo
    {
        return $this->belongsTo(Machinery::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->as('participants');
    }

    public function parameters(): BelongsToMany
    {
        return $this->belongsToMany(
            MachineryParameter::class,
            'research_machinery_parameter',
            'research_id',
            'parameter_id'
        )
            ->as('parameters');
    }

    public function experiments(): HasMany
    {
        return $this->hasMany(Experiment::class);
    }
}
