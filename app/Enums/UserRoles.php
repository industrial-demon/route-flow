<?php

namespace App\Enums;

use ErrorException;

enum UserRoles: string
{
    case SUPER_ADMIN = 'SUPER_ADMIN';

    case BUYER_OWNER = 'BUYER_OWNER';
    case SELLER_OWNER = "SELLER_OWNER";
    case LLOGISTICS_OWNER = "LOGISTICS_OWNER";


    public static function roleFromType(UserType $userType): UserRoles
    {
        return  match ($userType) {
            UserType::ADMIN => self::SUPER_ADMIN,
            UserType::SELLER => self::SELLER_OWNER,
            UserType::BUYER => self::BUYER_OWNER,
            UserType::LOGISTICS => self::LLOGISTICS_OWNER,
            default =>  throw new ErrorException('BAD!!!')
        };
    }

    public static function toArray(): array
    {
        return array_map(fn($item) => $item->value, self::cases());
    }
}
