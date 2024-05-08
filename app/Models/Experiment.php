<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Experiment extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'immutable_date',
        ];
    }

    public function research(): BelongsTo
    {
        return $this->belongsTo(Research::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quantitativeInputs(): BelongsToMany
    {
        return $this->belongsToMany(
            MachineryParameter::class,
            'experiment_quantitative_inputs',
            'experiment_id',
            'parameter_id'
        )
            ->using(QuantitativeParameter::class)
            ->as('quantitativeInputs');
    }

    public function qualityInputs(): BelongsToMany
    {
        return $this->belongsToMany(
            MachineryParameter::class,
            'experiment_quality_inputs',
            'experiment_id',
            'parameter_id'
        )
            ->withPivot('value')
            ->as('qualityInputs');
    }

    public function quantitativeOutputs(): BelongsToMany
    {
        return $this->belongsToMany(
            MachineryParameter::class,
            'experiment_quantitative_outputs',
            'experiment_id',
            'parameter_id'
        )
            ->using(QuantitativeParameter::class)
            ->as('quantitativeOutputs');
    }

    public function qualityOutputs(): BelongsToMany
    {
        return $this->belongsToMany(
            MachineryParameter::class,
            'experiment_quality_outputs',
            'experiment_id',
            'parameter_id'
        )
            ->withPivot('value')
            ->as('qualityOutputs');
    }
}
