@props(['compact' => false])

<div class="bg-[#FFFBEB] border-l-4 border-[#D97706] border-y border-r border-[#FCD34D] rounded-lg px-4 py-2.5 text-[#92400E] {{ $compact ? 'text-xs' : 'text-xs sm:text-sm' }} no-print" role="region" aria-label="Pemberitahuan Simulasi Prototype">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5">
        <div class="flex items-start sm:items-center gap-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded bg-[#D97706] text-white font-telemetry text-[10px] font-bold uppercase tracking-wider shrink-0 mt-0.5 sm:mt-0">
                Mode Simulasi Prototype
            </span>
            <p class="font-medium leading-snug text-[#78350F]">
                Seluruh kejadian dan laporan pada halaman ini adalah <strong>data simulasi</strong>. TanggapTerang menyajikan informasi kejadian &amp; tindak lanjut dokumentasi, <strong>bukan layanan distribusi bantuan</strong> atau pusat komando darurat.
            </p>
        </div>
    </div>
</div>
