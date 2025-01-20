<?php

namespace App\Enum;

enum ReservationStatus: string
{
    case PENDING = 'PENDING';
    case CONFIRMED = 'CONFIRMED';
    case CANCELED = 'CANCELED';

    public static function getStatuses(): array
    {
        return array_column(self::cases(), 'value');
    }
}
