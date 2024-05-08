<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MachineryParameter extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'parameter_type',
        'value_type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function machinery(): BelongsTo
    {
        return $this->belongsTo(Machinery::class);
    }

    public function research(): BelongsToMany
    {
        return $this->belongsToMany(
            Research::class,
            'research_machinery_parameter',
            'parameter_id',
            'research_id'
        )
            ->as('research');
    }

    public function experiments(): BelongsToMany
    {
        return $this->belongsToMany(Experiment::class);
    }
}
