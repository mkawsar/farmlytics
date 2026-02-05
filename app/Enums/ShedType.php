<?php

namespace App\Enums;

enum ShedType: string
{
    case MILKING = 'milking';
    case CALF = 'calf';
    case QUARANTINE = 'quarantine';

    public function label(): string
    {
        return match ($this) {
            self::MILKING => 'Milking',
            self::CALF => 'Calf',
            self::QUARANTINE => 'Quarantine',
        };
    }
}
