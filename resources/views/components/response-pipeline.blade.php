@props([
    'activeStep' => null, // 1: Dilaporkan, 2: Diperiksa, 3: Diverifikasi, 4: Ditindaklanjuti, 5: Dikonfirmasi (null = overview mode)
    'compact' => false,
    'title' => 'Mekanisme Penanganan Laporan & Respons Kejadian',
    'subtitle' => 'Alur transparan dari penerimaan laporan masyarakat hingga konfirmasi penanganan di lapangan'
])

@php
    $steps = [
        [
            'number' => 1,
            'slug' => 'dilaporkan',
            'title' => '1. Dilaporkan',
            'label' => 'Laporan Diterima',
            'desc' => 'Laporan warga masuk dan menerima nomor tiket pelacakan.',
            'badge' => 'Menunggu Verifikasi',
            'badge_class' => 'bg-amber-100 text-amber-800 border-amber-300',
        ],
        [
            'number' => 2,
            'slug' => 'diperiksa',
            'title' => '2. Diperiksa',
            'label' => 'Pemeriksaan Awal',
            'desc' => 'Petugas memeriksa kejelasan lokasi, waktu kejadian, dan bukti pendukung.',
            'badge' => 'Tinjauan Berkas',
            'badge_class' => 'bg-blue-100 text-blue-800 border-blue-300',
        ],
        [
            'number' => 3,
            'slug' => 'diverifikasi',
            'title' => '3. Diverifikasi',
            'label' => 'Verifikasi Silang',
            'desc' => 'Informasi dicocokkan dengan data rujukan terbuka dan laporan warga lain di sekitar lokasi.',
            'badge' => 'Terverifikasi / Ditolak',
            'badge_class' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
        ],
        [
            'number' => 4,
            'slug' => 'ditindaklanjuti',
            'title' => '4. Ditindaklanjuti',
            'label' => 'Koordinasi & Lapangan',
            'desc' => 'Kebutuhan dipetakan ke 4 kategori utama (Kesehatan, Makan dan Minum, Bansos, Evakuasi) untuk ditindaklanjuti.',
            'badge' => 'Sedang Ditindaklanjuti',
            'badge_class' => 'bg-cyan-100 text-cyan-800 border-cyan-300',
        ],
        [
            'number' => 5,
            'slug' => 'dikonfirmasi',
            'title' => '5. Dikonfirmasi',
            'label' => 'Konfirmasi Bukti Sah',
            'desc' => 'Penanganan atau serah terima bantuan dicatat selesai setelah diverifikasi dengan bukti fisik.',
            'badge' => 'Penyerahan Sah',
            'badge_class' => 'bg-[#173B70] text-white border-[#173B70]',
        ],
    ];

    $currentStepNumber = (int) ($activeStep ?? 0);
@endphp

<div class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-4 sm:p-5 space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-3">
        <div class="space-y-0.5">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-800 border border-slate-200 font-telemetry text-[10px] font-bold uppercase tracking-wider">
                    Alur Kerja Sistem
                </span>
            </div>
            <h3 class="text-sm sm:text-base font-extrabold text-[#202938] tracking-tight">
                {{ $title }}
            </h3>
        </div>
        <p class="text-[11px] sm:text-xs text-[#788397] max-w-md">
            {{ $subtitle }}
        </p>
    </div>

    <!-- Stepper Pipeline Display -->
    <div class="flex overflow-x-auto snap-x snap-mandatory gap-3 pb-2 -mx-4 px-4 sm:mx-0 sm:px-0 scrollbar-none md:grid md:grid-cols-5 relative">
        @foreach($steps as $step)
            @php
                $isPassed = $currentStepNumber > 0 && $step['number'] < $currentStepNumber;
                $isCurrent = $currentStepNumber > 0 && $step['number'] === $currentStepNumber;
                $isPending = $currentStepNumber > 0 && $step['number'] > $currentStepNumber;
                $isOverview = $currentStepNumber === 0;

                if ($isCurrent) {
                    $cardBorder = 'border-[#173B70] bg-blue-50/50 ring-2 ring-[#173B70]/20';
                    $circleClass = 'bg-[#173B70] text-white ring-4 ring-blue-100';
                } elseif ($isPassed) {
                    $cardBorder = 'border-emerald-300 bg-emerald-50/30';
                    $circleClass = 'bg-[#059669] text-white';
                } elseif ($isPending) {
                    $cardBorder = 'border-slate-200 bg-slate-50/40 opacity-70';
                    $circleClass = 'bg-slate-200 text-slate-600';
                } else {
                    // Overview mode
                    $cardBorder = 'border-slate-200 bg-[#F8F9FC] hover:border-[#173B70]/40 transition';
                    $circleClass = 'bg-[#173B70] text-white';
                }
            @endphp

            <div class="rounded-xl border {{ $cardBorder }} p-3 sm:p-3.5 flex flex-col justify-between space-y-2 relative w-[72vw] max-w-[260px] shrink-0 snap-start md:w-auto">
                <!-- Top Row: Step Number & Badge -->
                <div class="flex items-center justify-between gap-1.5">
                    <div class="w-6 h-6 rounded-full {{ $circleClass }} font-telemetry font-bold text-xs flex items-center justify-center shrink-0">
                        @if($isPassed)
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            {{ $step['number'] }}
                        @endif
                    </div>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-telemetry font-bold uppercase border {{ $step['badge_class'] }}">
                        {{ $step['badge'] }}
                    </span>
                </div>

                <!-- Step Title & Label -->
                <div class="space-y-1">
                    <h4 class="font-telemetry text-xs font-bold text-[#202938] leading-snug">
                        {{ $step['title'] }}
                    </h4>
                    <span class="font-telemetry text-[10px] text-[#788397] uppercase tracking-wider block">
                        {{ $step['label'] }}
                    </span>
                    @if(!$compact)
                        <p class="text-[11px] text-[#202938]/80 leading-relaxed pt-0.5">
                            {{ $step['desc'] }}
                        </p>
                    @endif
                </div>

                @if($isCurrent)
                    <div class="pt-1 border-t border-blue-200/60 flex items-center gap-1 text-[10px] font-telemetry text-[#173B70] font-bold">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#173B70] animate-pulse"></span>
                        <span>Posisi Saat Ini</span>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Integrity Disclaimer Footer Note -->
    <div class="bg-slate-50 border border-slate-100 rounded-lg px-3.5 py-2 text-[11px] text-[#788397] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div class="flex items-center gap-1.5">
            <span class="text-[#173B70] font-bold">ℹ</span>
            <span>Status penanganan diperbarui secara bertahap berdasarkan hasil verifikasi tim dan bukti penyaluran di lapangan.</span>
        </div>
        <span class="font-telemetry text-[10px] text-[#788397] font-semibold shrink-0">
            5 Tahap Respons
        </span>
    </div>
</div>
