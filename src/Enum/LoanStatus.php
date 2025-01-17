<?php

namespace App\Enum;

enum LoanStatus: string
{
    case ACTIVE = 'ACTIVE';
    case COMPLETED = 'COMPLETED';
    case OVERDUE = 'OVERDUE';

    public static function getStatuses(): array
    {
        return array_column(self::cases(), 'value');
    }
}
