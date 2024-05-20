<?php

namespace App\Enum;

enum CustomAttributeType: string
{
    case String = 'STRING';

    case Int = 'INT';

    case Float = 'FLOAT';

    case Boolean = 'BOOL';

    case Date = 'DATE';
}