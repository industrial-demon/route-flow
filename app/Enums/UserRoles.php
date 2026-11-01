<?php

namespace App\Enums;

enum UserRoles: string
{
    case SUPER_ADMIN = 'SUPER_ADMIN';
    
    case BAYER = 'BAYER';
    case SELLER = "SELLER";
    case OUTSOURCE_LOGIST  = "OUTSOURCE_LOGIST";
}
