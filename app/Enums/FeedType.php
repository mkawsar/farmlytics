<?php

namespace App\Enums;

enum FeedType: string
{
    case GRAIN = 'grain';
    case GRASS = 'grass';
    case CONCENTRATE = 'concentrate';
    case FORAGE = 'forage';

    public function label(): string
    {
        return match ($this) {
            self::GRAIN => 'Grain',
            self::GRASS => 'Grass',
            self::CONCENTRATE => 'Concentrate',
            self::FORAGE => 'Forage',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::GRAIN => 'The feed is a grain',
            self::GRASS => 'The feed is a grass',
            self::CONCENTRATE => 'The feed is a concentrate',
            self::FORAGE => 'The feed is a forage',
        };
    }
}
