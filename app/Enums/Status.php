<?php

namespace App\Enums;

enum Status: string
{
    case ACTIVE = 'active';
    case SOLD = 'sold';
    case DEAD = 'dead';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::SOLD => 'Sold',
            self::DEAD => 'Dead',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ACTIVE => 'The cow is active and healthy',
            self::SOLD => 'The cow has been sold',
            self::DEAD => 'The cow has died',
        };
    }
}
