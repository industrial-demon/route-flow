<?php

namespace App\Enums;
use App\Enums\UserRoles;

enum UserType: string
{
    case ADMIN = 'ADMIN';

    case SELLER = "SELLER";
    case BUYER = 'BUYER';
    case LOGISTICS  = 'LOGISTICS';

    public static function toArray(): array
    {
        return array_map(fn($item) => $item->value, self::cases());
    }

    public static function registeredTypes(): array
    {
        return array_map(fn($item) => $item->value, [self::SELLER, self::BUYER, self::LOGISTICS]);
    }
}
