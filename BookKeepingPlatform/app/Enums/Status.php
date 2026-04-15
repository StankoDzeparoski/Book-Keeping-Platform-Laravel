<?php

namespace App\Enums;

//	status; (Available, assigned, repair, lost:Enum)

enum Status: string
{
    //
    case AVAILABLE = 'Available';
    case ASSIGNED = 'Assigned';
    case REPAIR = 'Repair';
    case LOST = 'Lost';

}
