<?php

namespace App\Enum;

enum Role: string
{
    case ADMIN = 'ADMIN';
    case STAFF = 'STAFF';
    case MEMBER = 'MEMBER';

    public static function getRoles(): array
    {
        return array_column(self::getRoles(),'value');
    }
}