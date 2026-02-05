<?php

namespace App\Enums;

enum Group: string
{
    case LACTATING = 'lactating';
    case DRY = 'dry';
    case PREGNANT = 'pregnant';
    case FATTENING = 'fattening';
    case CALF = 'calf';

    public function label(): string
    {
        return match ($this) {
            self::LACTATING => 'Lactating',
            self::DRY => 'Dry',
            self::PREGNANT => 'Pregnant',
            self::FATTENING => 'Fattening',
            self::CALF => 'Calf',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::LACTATING => 'The cow is lactating',
            self::DRY => 'The cow is dry',
            self::PREGNANT => 'The cow is pregnant',
            self::FATTENING => 'The cow is fattening',
            self::CALF => 'The calf is a calf',
        };
    }
}
