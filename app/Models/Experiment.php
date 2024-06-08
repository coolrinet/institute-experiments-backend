<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Experiment extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::created(function (Experiment $experiment) {
            $research = $experiment->research;

            if (is_null($research->last_experiment_date)
                || $experiment->date->gt($research->last_experiment_date)) {
                $research->last_experiment_date = $experiment->date;
                $research->save();
            }
        });

        static::updated(function (Experiment $experiment) {
            $research = $experiment->research;

            if (is_null($research->last_experiment_date)
                || $experiment->date->gt($research->last_experiment_date)) {
                $research->last_experiment_date = $experiment->date;
                $research->save();
            }
        });

        static::deleted(function (Experiment $experiment) {
            $research = $experiment->research;
            $latestDate = $research->experiments()->max('last_experiment_date');
            $research->last_experiment_date = $latestDate;
            $research->save();
        });
    }

    public $timestamps = false;

    protected $fillable = [
        'name',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'immutable_date:j F Y',
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
            ->withPivot('value')
            ->using(QuantitativeParameter::class);
    }

    public function qualityInputs(): BelongsToMany
    {
        return $this->belongsToMany(
            MachineryParameter::class,
            'experiment_quality_inputs',
            'experiment_id',
            'parameter_id'
        )
            ->withPivot('value');
    }

    public function quantitativeOutputs(): BelongsToMany
    {
        return $this->belongsToMany(
            MachineryParameter::class,
            'experiment_quantitative_outputs',
            'experiment_id',
            'parameter_id'
        )
            ->withPivot('value')
            ->using(QuantitativeParameter::class);
    }

    public function qualityOutputs(): BelongsToMany
    {
        return $this->belongsToMany(
            MachineryParameter::class,
            'experiment_quality_outputs',
            'experiment_id',
            'parameter_id'
        )
            ->withPivot('value');
    }
}
