<?php

namespace App\Traits;

trait GetEnumValues
{
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
