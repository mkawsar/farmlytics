<?php

namespace App\Enums;

enum FarmType: string
{
    case DAIRY = 'dairy';
    case FATTENING = 'fattening';
    case MIXED = 'mixed';

    public function label(): string
    {
        return match ($this) {
            self::DAIRY => 'Dairy',
            self::FATTENING => 'Fattening',
            self::MIXED => 'Mixed',
        };
    }
}
