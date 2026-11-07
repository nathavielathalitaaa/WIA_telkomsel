<?php

namespace App\Enums;

enum SourceType: string
{
    case LaporanMasyarakat = 'laporan_masyarakat';
    case MediaMassa = 'media_massa';
    case MediaSosial = 'media_sosial';
    case InstansiResmi = 'instansi_resmi';
    case Lainnya = 'lainnya';

    public function label(bool $isSimulation = true): string
    {
        return match ($this) {
            self::LaporanMasyarakat => 'Laporan Warga',
            self::MediaMassa => 'Sumber Informasi Terbuka (Media)',
            self::MediaSosial => 'Sumber Informasi Terbuka (Publik)',
            self::InstansiResmi => $isSimulation ? 'Sumber Pemerintah (Simulasi)' : 'Data Pemerintah Terbuka',
            self::Lainnya => 'Sumber Informasi Terbuka',
        };
    }
}
