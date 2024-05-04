<?php

namespace App\Enums;

use App\Traits\GetEnumValues;

enum MachineryParameterType: string
{
    use GetEnumValues;

    case Input = 'входной';
    case Output = 'выходной';
}
