<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case VET = 'vet';
    case STAFF = 'staff';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::USER => 'User',
            self::VET => 'Vet',
            self::STAFF => 'Staff',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin can view and edit all data',
            self::USER => 'User can view and edit their own data',
            self::VET => 'Vet can view and edit all data',
            self::STAFF => 'Staff can view and edit all data',
        };
    }
}
