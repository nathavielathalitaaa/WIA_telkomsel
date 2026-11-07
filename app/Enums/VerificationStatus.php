<?php

namespace App\Enums;

enum VerificationStatus: string
{
    case MenungguVerifikasi = 'menunggu_verifikasi';
    case Terverifikasi = 'terverifikasi';
    case Ditolak = 'ditolak';
    case Duplikat = 'duplikat';
    case TidakValid = 'tidak_valid';

    public function label(): string
    {
        return match ($this) {
            self::MenungguVerifikasi => 'Menunggu Verifikasi',
            self::Terverifikasi => 'Terverifikasi',
            self::Ditolak => 'Ditolak',
            self::Duplikat => 'Duplikat',
            self::TidakValid => 'Tidak Valid',
        };
    }
}
