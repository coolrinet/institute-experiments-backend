<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function scopeFilterByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    public function scopeFilterByParameterType(Builder $query, string $parameterType): Builder
    {
        return $query->where('parameter_type', $parameterType);
    }

    public function scopeFilterByValueType(Builder $query, string $valueType): Builder
    {
        return $query->where('value_type', $valueType);
    }

    public function scopeFilterByUserId(Builder $query, mixed $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeFilterByMachineryId(Builder $query, mixed $machineryId): Builder
    {
        return $query->where('machinery_id', $machineryId);
    }
}
