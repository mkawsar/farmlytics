<?php

namespace App\Enums;

enum IncomeType: string
{
    case MILK_SALE = 'milk_sale';
    case ANIMAL_SALE = 'animal_sale';
    case DUNG_SALE = 'dung_sale';
    case BIOGAS_SALE = 'biogas_sale';
    case CALF_SALE = 'calf_sale';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::MILK_SALE => 'Milk sale',
            self::ANIMAL_SALE => 'Animal sale',
            self::DUNG_SALE => 'Dung sale',
            self::BIOGAS_SALE => 'Biogas sale',
            self::CALF_SALE => 'Calf sale',
            self::OTHER => 'Other',
        };
    }
}
