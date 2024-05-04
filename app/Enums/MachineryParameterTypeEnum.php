<?php

namespace App\Enums;

use App\Traits\GetEnumValues;

enum MachineryParameterTypeEnum: string
{
    use GetEnumValues;

    case INPUT = 'input';
    case OUTPUT = 'output';
}
