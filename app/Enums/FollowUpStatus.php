<?php

namespace App\Enums;

enum FollowUpStatus: string
{
    case BelumDitindaklanjuti = 'belum_ditindaklanjuti';
    case SedangDitindaklanjuti = 'sedang_ditindaklanjuti';
    case Selesai = 'selesai';
    case Dipantau = 'dipantau';

    public function label(): string
    {
        return match ($this) {
            self::BelumDitindaklanjuti => 'Belum Ditindaklanjuti',
            self::SedangDitindaklanjuti => 'Sedang Ditindaklanjuti',
            self::Selesai => 'Selesai Ditindaklanjuti',
            self::Dipantau => 'Dalam Pemantauan',
        };
    }
}
