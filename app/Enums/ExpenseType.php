<?php

namespace App\Enums;

enum ExpenseType: string
{
    case FODDER = 'fodder';
    case CONCENTRATE = 'concentrate';
    case MEDICINE = 'medicine';
    case COW_PURCHASE = 'cow_purchase';
    case LABOUR = 'labour';
    case ELECTRICITY = 'electricity';
    case WATER = 'water';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::FODDER => 'Fodder',
            self::CONCENTRATE => 'Concentrate',
            self::MEDICINE => 'Medicine',
            self::COW_PURCHASE => 'Cow purchase',
            self::LABOUR => 'Labour',
            self::ELECTRICITY => 'Electricity',
            self::WATER => 'Water',
            self::OTHER => 'Other',
        };
    }

    /** Whether this expense is typically monthly and allocated across animals (no single animal_id). */
    public function isAllocatable(): bool
    {
        return match ($this) {
            self::LABOUR, self::ELECTRICITY, self::WATER => true,
            default => false,
        };
    }
}
