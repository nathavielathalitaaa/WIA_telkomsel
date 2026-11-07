<?php

namespace App\Enums;

enum ConfidenceLevel: string
{
    case Rendah = 'rendah';
    case Sedang = 'sedang';
    case Tinggi = 'tinggi';

    public function label(): string
    {
        return match ($this) {
            self::Rendah => 'Keyakinan Rendah',
            self::Sedang => 'Keyakinan Sedang',
            self::Tinggi => 'Keyakinan Tinggi',
        };
    }
}
