<?php

namespace App\Enums;

enum AidResponseStatus: string
{
    case Direncanakan = 'direncanakan';
    case DalamPenyaluran = 'dalam_penyaluran';
    case Terkonfirmasi = 'terkonfirmasi';

    public function label(): string
    {
        return match ($this) {
            self::Direncanakan => 'Direncanakan',
            self::DalamPenyaluran => 'Dalam Penyaluran',
            self::Terkonfirmasi => 'Penyerahan Dikonfirmasi',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Direncanakan => 'Bantuan baru ditawarkan atau direncanakan oleh pemberi respons (belum dikonfirmasi penyerahannya).',
            self::DalamPenyaluran => 'Pemberi respons menyatakan bantuan sedang dalam proses penyaluran ke lokasi tujuan.',
            self::Terkonfirmasi => 'Penyerahan bantuan telah dikonfirmasi berdasarkan bukti dokumentasi atau catatan konfirmasi.',
        };
    }
}
