<?php

namespace App\Enums;

use App\Traits\GetEnumValues;

enum MachineryParameterValueType: string
{
    use GetEnumValues;

    case Quantitative = 'количественный';
    case Quality = 'качественный';
}
