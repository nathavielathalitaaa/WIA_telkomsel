@props(['incidents', 'title' => 'Kejadian Berdasarkan Wilayah', 'showAllUrl' => null])

@php
    $incidentsCollection = collect($incidents ?? []);
    $allUrl = $showAllUrl ?: route('public.monitor');
@endphp

<section aria-label="Kejadian Bencana Berdasarkan Wilayah" class="space-y-3">
    <!-- Header Komponen: Terinspirasi dari Peringatan Dini Wilayah BMKG -->
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse shrink-0"></span>
            <h2 class="font-telemetry text-xs sm:text-sm font-bold uppercase tracking-wider text-[#202938]">
                {{ $title }}
            </h2>
            <span class="text-[11px] text-slate-400 font-telemetry hidden sm:inline">
                • Geser horizontal untuk melihat wilayah lain
            </span>
        </div>

        <div class="flex items-center gap-2">
            <!-- Tombol Geser untuk Pengguna Desktop -->
            <div class="hidden sm:flex items-center gap-1">
                <button type="button"
                        onclick="document.getElementById('regional-carousel-track')?.scrollBy({ left: -320, behavior: 'smooth' })"
                        class="p-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition shadow-2xs"
                        aria-label="Geser ke kiri"
                        title="Geser ke kiri">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button type="button"
                        onclick="document.getElementById('regional-carousel-track')?.scrollBy({ left: 320, behavior: 'smooth' })"
                        class="p-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition shadow-2xs"
                        aria-label="Geser ke kanan"
                        title="Geser ke kanan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            <a href="{{ $allUrl }}"
               class="text-xs font-telemetry font-bold text-[#173B70] hover:underline inline-flex items-center gap-1 shrink-0">
                <span>Lihat Semua</span>
                <span aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>

    @if($incidentsCollection->isEmpty())
        <!-- Empty State Informatif -->
        <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-5 text-center text-xs text-slate-500 space-y-1">
            <p class="font-medium text-slate-700">Tidak ada kejadian aktif yang tercatat untuk berbagai wilayah saat ini.</p>
            <p class="text-[11px] text-slate-400">Data kejadian akan otomatis tampil di sini begitu laporan diverifikasi oleh tim.</p>
        </div>
    @else
        <!-- Horizontal Scroll Track: Mobile Edge Peek & Desktop Side-by-Side -->
        <div id="regional-carousel-track"
             class="flex overflow-x-auto snap-x snap-mandatory gap-3 pb-2 -mx-4 px-4 sm:mx-0 sm:px-0 scrollbar-none scroll-smooth"
             role="region" aria-label="Daftar Kartu Kejadian Berdasarkan Wilayah">
            @foreach($incidentsCollection as $inc)
                @php
                    $sevVal = $inc->severity instanceof \App\Enums\IncidentSeverity ? $inc->severity->value : (string) $inc->severity;
                    $isUrgent = in_array($sevVal, ['kritis', 'tinggi']);
                    $regionName = $inc->wilayahAdministratif?->nama ?: $inc->location_name;
                    $typeTitle = ucwords(str_replace('_', ' ', $inc->incident_type));
                    $timeStr = $inc->occurred_at ? $inc->occurred_at->format('d M Y, H:i') . ' WIB' : '-';
                    $verifLabel = $inc->verification_status instanceof \App\Enums\VerificationStatus ? $inc->verification_status->label() : (string) $inc->verification_status;
                    $followLabel = $inc->follow_up_status instanceof \App\Enums\FollowUpStatus ? $inc->follow_up_status->label() : (string) $inc->follow_up_status;

                    // Visual Alert styling (Inspirasi BMKG Nowcasting):
                    // - Urgensi Tinggi/Kritis: Background kuning muda & border kuning lebih gelap
                    // - Normal / Sedang: Background putih bersih & border slate
                    $cardBg = $isUrgent
                        ? 'bg-[#FEFCE8] border-2 border-[#EAB308] text-[#713F12]'
                        : 'bg-white border border-slate-200/90 text-[#202938]';
                    $badgeBg = $isUrgent
                        ? 'bg-[#FEF08A] text-[#854D0E] border border-[#EAB308]'
                        : 'bg-slate-100 text-slate-700 border border-slate-200';
                    $ctaBtn = $isUrgent
                        ? 'bg-[#CA8A04] hover:bg-[#A16207] text-white'
                        : 'bg-[#173B70] hover:bg-[#0F2A52] text-white';
                @endphp
                <article class="w-[84vw] max-w-[320px] sm:w-[310px] shrink-0 snap-start rounded-xl {{ $cardBg }} p-4 shadow-2xs hover:shadow-xs transition flex flex-col justify-between space-y-3 group">
                    <div class="space-y-2">
                        <!-- Top Header: Level Urgensi & Sifat Data -->
                        <div class="flex items-center justify-between gap-1.5 text-[10px] font-telemetry">
                            <span class="px-2 py-0.5 rounded font-bold uppercase {{ $badgeBg }}">
                                @if($isUrgent)
                                    Peringatan: {{ strtoupper($sevVal) }}
                                @else
                                    Situasi: {{ strtoupper($sevVal ?: 'TERCATAT') }}
                                @endif
                            </span>

                            @if($inc->is_simulation)
                                <span class="px-1.5 py-0.5 rounded bg-slate-200/80 text-slate-700 font-bold uppercase text-[9px] border border-slate-300">
                                    Simulasi
                                </span>
                            @else
                                <span class="px-1.5 py-0.5 rounded bg-[#173B70] text-white font-bold uppercase text-[9px]">
                                    Resmi BNPB
                                </span>
                            @endif
                        </div>

                        <!-- Nama Wilayah & Judul Kejadian -->
                        <div>
                            <span class="font-telemetry text-xs font-bold text-[#173B70] uppercase tracking-wide block truncate" title="{{ $regionName }}">
                                📍 {{ $regionName }}
                            </span>
                            <h3 class="font-bold text-sm text-[#202938] leading-snug line-clamp-2 mt-0.5 group-hover:text-[#173B70] transition" title="{{ $inc->title }}">
                                {{ $inc->title }}
                            </h3>
                        </div>

                        <!-- Jenis Kejadian, Waktu, & Status Verifikasi/Tindak Lanjut -->
                        <div class="space-y-1 text-[11px] text-slate-500 pt-1 border-t border-slate-100/80">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 font-telemetry text-[10px] uppercase">Jenis:</span>
                                <span class="font-semibold text-slate-700 truncate max-w-[170px] text-right">{{ $typeTitle }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 font-telemetry text-[10px] uppercase">Waktu:</span>
                                <span class="font-telemetry font-medium text-slate-600">{{ $timeStr }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 font-telemetry text-[10px] uppercase">Status:</span>
                                <span class="font-telemetry font-semibold text-slate-700 text-[10px] truncate max-w-[170px] text-right">
                                    {{ $verifLabel }} • {{ $followLabel }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom CTA: Lihat Kejadian -->
                    <div class="pt-2 border-t border-slate-100/80">
                        <a href="{{ route('public.incidents.show', $inc->slug) }}"
                           class="w-full py-1.5 px-3 rounded-lg {{ $ctaBtn }} font-telemetry text-xs font-bold uppercase tracking-wider text-center inline-flex items-center justify-center gap-1.5 shadow-2xs transition">
                            <span>Lihat Kejadian</span>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</section>
