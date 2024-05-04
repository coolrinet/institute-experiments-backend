<?php

namespace App\Enums;

use App\Traits\GetEnumValues;

enum MachineryParameterType: string
{
    use GetEnumValues;

    case INPUT = 'input';
    case OUTPUT = 'output';
}
