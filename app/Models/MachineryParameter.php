<?php

namespace App\Models;

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
}
