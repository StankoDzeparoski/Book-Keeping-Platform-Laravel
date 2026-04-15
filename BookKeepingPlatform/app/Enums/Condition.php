<?php

namespace App\Enums;

//condition; (new, used, broken:Enum)

enum Condition: string
{
    //
    case NEW = 'new';

    case USED = 'used';

    case BROKEN = 'broken';
}
