<?php

namespace App\Enums;

enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';

    public function label(): string
    {
        return match ($this) {
            self::MALE => 'Male',
            self::FEMALE => 'Female',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::MALE => 'The animal is male',
            self::FEMALE => 'The animal is female',
        };
    }
}
