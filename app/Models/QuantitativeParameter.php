<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class QuantitativeParameter extends Pivot
{
    protected function casts(): array
    {
        return [
            'value' => 'double',
        ];
    }
}
