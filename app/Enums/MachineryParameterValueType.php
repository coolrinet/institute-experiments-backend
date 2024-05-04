<?php

namespace App\Enums;

use App\Traits\GetEnumValues;

enum MachineryParameterValueType: string
{
    use GetEnumValues;

    case QUANTITATIVE = 'quantitative';
    case QUALITY = 'quality';
}
