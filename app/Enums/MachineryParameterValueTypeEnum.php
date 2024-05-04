<?php

namespace App\Enums;

use App\Traits\GetEnumValues;

enum MachineryParameterValueTypeEnum: string
{
    use GetEnumValues;

    case QUANTITATIVE = 'quantitative';
    case QUALITY = 'quality';
}
