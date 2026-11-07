<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Operator = 'operator';
    case Verifikator = 'verifikator';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Operator => 'Operator Tindak Lanjut',
            self::Verifikator => 'Verifikator Informasi',
        };
    }
}
