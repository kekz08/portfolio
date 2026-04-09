<?php

namespace App\Enums;

enum UserRole: string
{
    case CLIENT = 'client';
    case USER = 'user';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::CLIENT, self::USER => 'Client',
            self::ADMIN => 'Admin',
        };
    }
}
