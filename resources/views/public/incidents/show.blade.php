@extends('layouts.app', [
    'title' => $incident->title . ' — Detail Kejadian TanggapTerang',
    'activeTab' => 'pantau',
])

@php
    $hasCoordinates = $incident->latitude !== null && $incident->longitude !== null;
    $typeLabel = $categories[$incident->incident_type] ?? ucwords(str_replace('_', ' ', $incident->incident_type));
@endphp

@section('content')
    <!-- Breadcrumb & Back Action -->
    <nav aria-label="Breadcrumb" class="flex flex-wrap items-center justify-between gap-3 min-w-0">
        <div class="flex items-center gap-2 text-xs font-telemetry text-slate-600 min-w-0 max-w-full">
            <a href="{{ route('public.monitor') }}" class="hover:text-[#173B70] font-bold inline-flex items-center gap-1 shrink-0">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Kembali ke Peta Pantau</span>
            </a>
            <span class="shrink-0">/</span>
            <span class="text-[#788397] truncate min-w-0">{{ $incident->title }}</span>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if(\Illuminate\Support\Facades\Route::has('public.sitrep'))
                <a href="{{ route('public.sitrep', ['incident' => $incident->slug]) }}"
                   class="px-3 py-1.5 rounded-lg border border-slate-200/80 bg-white hover:bg-slate-50 text-[#202938] font-telemetry text-xs font-bold uppercase tracking-wider transition shadow-2xs">
                    Cetak Ringkasan SitRep
                </a>
            @endif
            <a href="{{ route('public.aid-responses.create', ['incident' => $incident->slug]) }}"
               class="px-3 py-1.5 rounded-lg border border-blue-200/70 bg-blue-50/70 hover:bg-blue-100 text-[#173B70] font-telemetry text-xs font-bold uppercase tracking-wider transition shadow-2xs">
                + Catat Respons Bantuan
            </a>
            <a href="{{ route('public.reports.create') }}"
               class="px-3.5 py-1.5 rounded-lg btn-brand-gradient font-telemetry text-xs font-bold uppercase tracking-wider shadow-xs">
                + Tambah Laporan Warga
            </a>
        </div>
    </nav>

    <!-- Desktop Sticky Section Quick Nav -->
    <div class="hidden lg:flex items-center gap-2 p-1.5 bg-white rounded-xl shadow-xs border border-slate-100/90 font-telemetry text-xs">
        <span class="px-2 font-bold uppercase tracking-wider text-slate-400">Navigasi Halaman:</span>
        <a href="#section-ringkasan" class="px-3 py-1.5 rounded text-slate-700 hover:bg-slate-100 font-bold transition">Ringkasan</a>
        <a href="#section-peta" class="px-3 py-1.5 rounded text-slate-700 hover:bg-slate-100 font-bold transition">Peta Titik</a>
        <a href="#section-sumber" class="px-3 py-1.5 rounded text-slate-700 hover:bg-slate-100 font-bold transition">Sumber Rujukan ({{ $incident->sources->count() }})</a>
        <a href="#section-laporan" class="px-3 py-1.5 rounded text-slate-700 hover:bg-slate-100 font-bold transition">Laporan Warga ({{ $incident->reports->count() }})</a>
        <a href="#section-bantuan" class="px-3 py-1.5 rounded text-slate-700 hover:bg-slate-100 font-bold transition">Respons Bantuan ({{ $incident->aidResponses->count() }})</a>
        <a href="#section-timeline" class="px-3 py-1.5 rounded text-slate-700 hover:bg-slate-100 font-bold transition">Garis Waktu Audit</a>
    </div>

    <!-- Primary Incident Header Card -->
    <section id="section-ringkasan" class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 sm:p-6 space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-4">
            <div class="flex flex-wrap items-center gap-2">
                @if($incident->is_simulation)
                    <span class="px-2.5 py-1 rounded bg-slate-800 text-white font-telemetry text-xs font-bold uppercase tracking-wider">
                        Data Simulasi
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded bg-[#173B70] text-white font-telemetry text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Data Historis Resmi (Pusdatin BNPB / DIBI)
                    </span>
                @endif
                <span class="px-2.5 py-1 rounded bg-slate-100 text-[#202938] border border-slate-200/80 font-telemetry text-xs font-bold uppercase tracking-wider">
                    {{ $typeLabel }}
                </span>
                <x-severity-badge :severity="$incident->severity" />
            </div>

            <div class="font-telemetry text-xs text-[#788397] flex flex-wrap items-center gap-3">
                <span>
                    Waktu Kejadian:
                    <strong class="text-[#202938]">{{ $incident->occurred_at?->format('d M Y, H:i') ?? '-' }} WIB</strong>
                </span>
                <span>•</span>
                <span>
                    Pembaruan Terakhir:
                    <strong class="text-[#202938]">{{ $incident->updated_at?->format('d M Y, H:i') ?? '-' }} WIB</strong>
                </span>
            </div>
        </div>

        @if(!$incident->is_simulation)
            <div class="bg-blue-50/70 border border-blue-200/80 rounded-xl p-3.5 flex items-start gap-2.5 text-xs text-[#173B70]">
                <svg class="w-4 h-4 text-[#173B70] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="space-y-0.5">
                    <div class="font-telemetry font-bold uppercase tracking-wider text-[#173B70]">
                        Atribusi Sumber Data Kejadian Historis (BNPB / DIBI)
                    </div>
                    <p class="leading-relaxed text-[#202938]/85">
                        Informasi kejadian ini tercatat secara resmi pada arsip <strong>Pusat Data, Informasi dan Komunikasi Kebencanaan (Pusdatin) BNPB</strong> melalui portal <strong>Data Informasi Bencana Indonesia (DIBI)</strong>. Sesuai prinsip integritas data kebencanaan, informasi ini tidak dihubungkan secara otomatis dengan laporan masyarakat atau catatan respons crowdsourcing fiktif.
                    </p>
                </div>
            </div>
        @endif

        <div class="space-y-3">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-[#202938] leading-tight">
                {{ $incident->title }}
            </h1>

            <!-- Administrative Location Info -->
            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div class="space-y-1">
                    <div class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-[#788397]">
                        Lokasi Kejadian &amp; Wilayah Administratif
                    </div>
                    <div class="text-sm font-bold text-[#202938] flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#D94350] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>{{ $incident->location_name }}</span>
                    </div>
                    @if(!empty($administrativeHierarchy))
                        <div class="flex flex-wrap items-center gap-1.5 text-xs font-telemetry text-slate-600 pt-0.5">
                            @foreach($administrativeHierarchy as $idx => $item)
                                @if($idx > 0)
                                    <span class="text-slate-400">&rarr;</span>
                                @endif
                                <span class="px-1.5 py-0.5 rounded bg-white border border-slate-200">
                                    {{ $item['tingkat'] }}: <strong>{{ $item['nama'] }}</strong>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="font-telemetry text-xs text-slate-600 shrink-0">
                    @if($hasCoordinates)
                        <div class="px-2.5 py-1.5 rounded bg-white border border-slate-200">
                            <span class="text-[10px] uppercase text-slate-400 block">Koordinat Terdaftar</span>
                            <strong>{{ number_format((float) $incident->latitude, 5) }}, {{ number_format((float) $incident->longitude, 5) }}</strong>
                        </div>
                    @else
                        <div class="px-2.5 py-1.5 rounded bg-slate-100 border border-slate-200 text-slate-500">
                            Koordinat spesifik belum tersedia
                        </div>
                    @endif
                </div>
            </div>

            <!-- Incident Description -->
            <div class="prose prose-slate max-w-none pt-1">
                <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#788397] mb-1.5">
                    Ringkasan Informasi Kejadian
                </h2>
                <p class="text-sm sm:text-base text-[#202938] leading-relaxed whitespace-pre-line">{{ $incident->description }}</p>
            </div>
        </div>

        @php
            $vStatusVal = $incident->verification_status instanceof \BackedEnum ? $incident->verification_status->value : (string) $incident->verification_status;
            $fStatusVal = $incident->follow_up_status instanceof \BackedEnum ? $incident->follow_up_status->value : (string) $incident->follow_up_status;

            if ($vStatusVal === 'menunggu_verifikasi') {
                $incidentStep = 2; // Pemeriksaan Awal
            } elseif ($fStatusVal === 'selesai') {
                $incidentStep = 5; // Dikonfirmasi
            } elseif (in_array($fStatusVal, ['sedang_ditindaklanjuti', 'dipantau'])) {
                $incidentStep = 4; // Ditindaklanjuti
            } elseif ($vStatusVal === 'terverifikasi') {
                $incidentStep = 3; // Diverifikasi
            } else {
                $incidentStep = 1;
            }
        @endphp

        <!-- Alur Penanganan Kejadian Faktual -->
        <x-response-pipeline
            :activeStep="$incidentStep"
            title="Posisi Penanganan Kejadian pada Alur Respons"
            subtitle="Menunjukkan progres faktual pemrosesan kejadian dari verifikasi awal hingga konfirmasi bukti penanganan" />

        <!-- Status Verifikasi & Tindak Lanjut -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
            <!-- Panel 1: Status Verifikasi Informasi -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-2 min-w-0">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-slate-800">
                        1. Status Verifikasi Informasi
                    </span>
                    <x-verification-badge :status="$incident->verification_status" />
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Hasil pemeriksaan tim verifikator atas kebenaran laporan warga dan kesesuaian data sumber terbuka.
                </p>
            </div>

            <!-- Panel 2: Status Tindak Lanjut Tim TanggapTerang -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-2 min-w-0">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-slate-800">
                        2. Status Tindak Lanjut Tim TanggapTerang
                    </span>
                    <x-followup-badge :status="$incident->follow_up_status" />
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Catatan perkembangan pemantauan dan dokumentasi tim TanggapTerang di lapangan.
                </p>
            </div>
        </div>

        <div class="bg-blue-50/60 border border-blue-200/80 rounded-xl p-3.5 text-xs text-blue-950 leading-relaxed flex items-start gap-2">
            <span class="font-semibold shrink-0">Catatan:</span>
            <span>Ketiadaan catatan tindak lanjut pada TanggapTerang tidak berarti instansi atau pihak berwenang eksternal tidak sedang menangani kejadian di lapangan.</span>
        </div>
    </section>

    <!-- Main Content Grid: Left (Map + Open Sources) & Right (Connected Reports + Audit Timeline) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        <!-- Left Column (5 cols) -->
        <div class="lg:col-span-5 space-y-5 min-w-0">
            <!-- Incident Location Map -->
            <section id="section-peta" class="bg-white rounded-2xl shadow-xs border border-slate-100/90 overflow-hidden">
                <div class="p-4 border-b border-slate-100 bg-[#F8FAFC] flex items-center justify-between">
                    <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                        Peta Titik Kejadian
                    </h2>
                    @if($hasCoordinates)
                        <div class="flex items-center gap-2">
                            <button type="button"
                                    id="btn-recenter-incident-map"
                                    class="px-2 py-1 rounded bg-white border border-slate-200 hover:bg-slate-50 text-[10px] font-telemetry font-bold text-[#202938] cursor-pointer inline-flex items-center gap-1 transition"
                                    data-tooltip="Pusatkan kembali tampilan peta ke titik koordinat kejadian">
                                <svg class="w-3.5 h-3.5 text-[#173B70]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <span>Pusatkan</span>
                            </button>
                            <span class="font-telemetry text-[11px] text-slate-600">
                                {{ number_format((float) $incident->latitude, 4) }}, {{ number_format((float) $incident->longitude, 4) }}
                            </span>
                        </div>
                    @endif
                </div>

                @if($hasCoordinates)
                    <div id="incident-detail-map" class="w-full h-[280px] bg-slate-100 z-10"></div>
                @else
                    <div class="p-6 text-center space-y-2 bg-slate-50">
                        <p class="text-sm font-semibold text-slate-700">Koordinat Peta Tidak Tersedia</p>
                        <p class="text-xs text-slate-500">
                            Kejadian ini belum memiliki titik koordinat lintang/bujur yang terverifikasi. Sistem tidak menampilkan koordinat perkiraan palsu.
                        </p>
                    </div>
                @endif
            </section>

            <!-- Connected Open Sources -->
            <section id="section-sumber" class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-4 sm:p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                            Sumber Informasi Terbuka &amp; Rujukan
                        </h2>
                        <p class="text-xs text-[#788397] mt-0.5">
                            Sumber informasi yang dirujuk untuk memvalidasi kejadian ini.
                        </p>
                    </div>
                    <span class="px-2 py-0.5 rounded bg-slate-100 text-[#202938] font-telemetry text-xs font-bold">
                        {{ $incident->incidentSources->count() }} Sumber
                    </span>
                </div>

                @if($incident->incidentSources->isEmpty())
                    <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-4 text-xs text-[#788397] text-center">
                        Belum ada rujukan sumber informasi terbuka eksternal yang ditautkan pada kejadian ini.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($incident->incidentSources as $incSource)
                            <div class="bg-[#F8F9FC] border border-slate-100/90 rounded-xl p-3.5 space-y-2">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    @if($incSource->source)
                                        <x-source-badge :type="$incSource->source->source_type" :isSimulation="(bool) $incSource->source->is_simulation" />
                                    @endif
                                    @if($incSource->published_at)
                                        <span class="font-telemetry text-[11px] text-[#788397]">
                                            {{ $incSource->published_at->format('d M Y, H:i') }} WIB
                                        </span>
                                    @endif
                                </div>

                                <div class="text-sm font-bold text-[#202938]">
                                    {{ $incSource->headline ?: ($incSource->source?->name ?? 'Sumber Informasi') }}
                                </div>

                                @if($incSource->source)
                                    <div class="text-xs font-semibold text-slate-600">
                                        Lembaga / Kanal: {{ $incSource->source->name }}
                                    </div>
                                @endif

                                @if($incSource->notes)
                                    <p class="text-xs text-[#202938]/80 leading-relaxed">
                                        {{ $incSource->notes }}
                                    </p>
                                @endif

                                @if($incSource->reference_url)
                                    <div class="pt-1">
                                        <a href="{{ $incSource->reference_url }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="font-telemetry text-xs font-bold text-[#173B70] hover:underline inline-flex items-center gap-1">
                                            <span>Buka Tautan Referensi</span>
                                            <span>&rarr;</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        <!-- Right Column (7 cols): Connected Citizen Reports & Public Audit Timeline -->
        <div class="lg:col-span-7 space-y-5 min-w-0">
            <!-- Connected Citizen Reports (Sanitized — NO Reporter PII) -->
            <section id="section-laporan" class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-4 sm:p-5 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                            Laporan Masyarakat yang Terhubung
                        </h2>
                        <p class="text-xs text-[#788397] mt-0.5">
                            Identitas pribadi dan kontak pelapor disembunyikan untuk menjaga privasi warga.
                        </p>
                    </div>
                    <span class="px-2.5 py-1 rounded bg-slate-100 text-[#202938] font-telemetry text-xs font-bold">
                        {{ $incident->reports->count() }} Laporan Warga
                    </span>
                </div>

                @if($incident->reports->isEmpty())
                    <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-6 text-center space-y-2">
                        <p class="text-sm font-semibold text-slate-700">Belum Ada Laporan Masyarakat yang Terhubung</p>
                        <p class="text-xs text-[#788397] max-w-md mx-auto">
                            @if(!$incident->is_simulation)
                                Kejadian ini bersumber dari data arsip historis resmi BNPB / DIBI. Laporan warga crowdsourcing tidak dihubungkan untuk menjaga integritas data riil.
                            @else
                                Kejadian ini dicatat melalui pemantauan sumber terbuka atau laporan warga masih dalam antrean penautan.
                            @endif
                        </p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($incident->reports as $report)
                            <article class="bg-[#F8F9FC] border border-slate-100/90 rounded-xl p-4 space-y-2.5">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2 py-0.5 rounded bg-[#173B70] text-white font-telemetry text-xs font-bold">
                                            {{ $report->ticket_number }}
                                        </span>
                                        <x-source-badge type="laporan_masyarakat" />
                                        <x-verification-badge :status="$report->verification_status" />
                                    </div>
                                    <span class="font-telemetry text-xs text-[#788397]">
                                        Dilaporkan: {{ $report->created_at?->format('d M Y, H:i') }} WIB
                                    </span>
                                </div>

                                <h3 class="text-sm sm:text-base font-bold text-[#202938]">
                                    {{ $report->title }}
                                </h3>

                                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                    {{ $report->description }}
                                </p>

                                <div class="pt-2 border-t border-slate-200/80 flex flex-wrap items-center justify-between gap-2 text-xs font-telemetry text-[#788397]">
                                    <span>Lokasi Laporan: <strong class="text-slate-700">{{ $report->location_name }}</strong></span>
                                    <div class="flex flex-wrap items-center gap-3">
                                        @php
                                            $mappedNeedCat = \App\Models\AidResponse::mapReportCategoryToNeedCategory((string) $report->category);
                                        @endphp
                                        <a href="{{ route('public.aid-responses.create', ['incident' => $incident->slug, 'report' => $report->id, 'need_category' => $mappedNeedCat, 'submission_type' => 'tawaran_rencana']) }}"
                                           class="text-emerald-700 hover:underline font-bold">
                                            + Tawaran Bantuan
                                        </a>
                                        <a href="{{ route('public.aid-responses.create', ['incident' => $incident->slug, 'report' => $report->id, 'need_category' => $mappedNeedCat, 'submission_type' => 'laporan_penyerahan']) }}"
                                           class="text-amber-800 hover:underline font-bold">
                                            + Lapor Bantuan Diserahkan
                                        </a>
                                        <a href="{{ route('public.track', ['ticket' => $report->ticket_number]) }}"
                                           class="text-[#173B70] hover:underline font-bold">
                                            Lihat Status Tiket &rarr;
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            <!-- Respons Bantuan Masyarakat / Komunitas (Terpisah dari Tindak Lanjut Internal) -->
            <section id="section-bantuan" class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-4 sm:p-5 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2 py-0.5 rounded bg-blue-50 text-[#173B70] border border-blue-200/60 font-telemetry text-[10px] font-bold uppercase">
                                Laporan Warga
                            </span>
                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200/60 font-telemetry text-[10px] font-bold uppercase">
                                Respons Bantuan Masyarakat
                            </span>
                            <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                                Kebutuhan &amp; Respons Bantuan Masyarakat
                            </h2>
                        </div>
                        <p class="text-xs text-[#788397] mt-0.5">
                            Membedakan secara jelas antara <strong>Tawaran / Rencana Bantuan</strong> dan <strong>Laporan Bantuan Sudah Diberikan</strong> (terpisah dari tindak lanjut internal tim).
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('public.aid-responses.create', ['incident' => $incident->slug, 'submission_type' => 'tawaran_rencana']) }}"
                           class="px-3 py-1.5 rounded-lg btn-brand-gradient font-telemetry text-[11px] font-bold uppercase tracking-wider shadow-xs">
                            + Ajukan Tawaran Bantuan
                        </a>
                        <a href="{{ route('public.aid-responses.create', ['incident' => $incident->slug, 'submission_type' => 'laporan_penyerahan']) }}"
                           class="px-3 py-1.5 rounded-lg bg-[#173B70] hover:bg-[#0F2A52] text-white font-telemetry text-[11px] font-bold uppercase tracking-wider shadow-xs">
                            + Laporkan Bantuan Sudah Diberikan
                        </a>
                    </div>
                </div>

                <!-- Ringkasan Terpisah: (1) Kebutuhan Tercatat, (2) Respons Bantuan & Satuan, (3) Stacked Bar 3 Warna Status -->
                @php
                    $activeIncidentStats = collect($categoryStats ?? [])->filter(
                        fn ($item) => $item['needs_count'] > 0 || $item['responses_total'] > 0
                    );
                @endphp
                @if($activeIncidentStats->isNotEmpty())
                    <div class="flex overflow-x-auto snap-x snap-mandatory gap-3 pb-2 -mx-4 px-4 sm:mx-0 sm:px-0 scrollbar-none sm:grid sm:grid-cols-2">
                        @foreach($activeIncidentStats as $catStat)
                            <div class="bg-[#F8FAFC] border border-slate-100 rounded-xl p-3.5 space-y-2.5 w-[84vw] max-w-[340px] shrink-0 snap-start sm:w-auto">
                                <div class="flex items-center justify-between gap-2 border-b border-slate-200/80 pb-1.5">
                                    <span class="text-xs font-bold text-[#202938]">{{ $catStat['label'] }}</span>
                                    <span class="font-telemetry text-[10px] font-semibold text-[#173B70]">
                                        {{ $catStat['factual_status'] }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-[11px] font-telemetry">
                                    <div class="bg-white border border-slate-200/70 rounded p-2">
                                        <span class="text-[10px] text-[#788397] block uppercase">1. Kebutuhan Tercatat</span>
                                        <strong class="text-[#173B70] text-xs">{{ $catStat['needs_count'] }} laporan warga</strong>
                                    </div>
                                    <div class="bg-white border border-slate-200/70 rounded p-2">
                                        <span class="text-[10px] text-[#788397] block uppercase">2. Respons Tercatat</span>
                                        <strong class="text-[#202938] text-xs">{{ $catStat['responses_total'] }} entri respons</strong>
                                    </div>
                                </div>

                                @if(!empty($catStat['quantity_labels']))
                                    <div class="space-y-1">
                                        <span class="font-telemetry text-[10px] uppercase text-slate-500 block">Rincian Kuantitas per Satuan (Tidak Dijumlahkan Lintas Satuan):</span>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($catStat['quantity_labels'] as $qtyLabel)
                                                <span class="px-1.5 py-0.5 rounded bg-white border border-slate-200 font-telemetry text-[10px] text-slate-700">
                                                    {{ $qtyLabel }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- 3. Status Respons Bantuan: Stacked Bar 3 Warna -->
                                <div class="space-y-1 font-telemetry text-[10px]">
                                    <div class="flex items-center justify-between text-slate-600">
                                        <span>3. Status Respons Bantuan</span>
                                        <span>
                                            Amber: {{ $catStat['direncanakan_count'] }} • Biru: {{ $catStat['dalam_penyaluran_count'] }} • Hijau: {{ $catStat['terkonfirmasi_count'] }}
                                        </span>
                                    </div>
                                    <div class="w-full h-2.5 bg-slate-200 rounded-full overflow-hidden flex">
                                        @if($catStat['direncanakan_count'] > 0)
                                            <div class="h-full bg-amber-500" style="width: {{ $catStat['pct_direncanakan'] }}%" title="Direncanakan: {{ $catStat['direncanakan_count'] }}"></div>
                                        @endif
                                        @if($catStat['dalam_penyaluran_count'] > 0)
                                            <div class="h-full bg-[#2563EB]" style="width: {{ $catStat['pct_dalam_penyaluran'] }}%" title="Dalam Penyaluran: {{ $catStat['dalam_penyaluran_count'] }}"></div>
                                        @endif
                                        @if($catStat['terkonfirmasi_count'] > 0)
                                            <div class="h-full bg-[#059669]" style="width: {{ $catStat['pct_terkonfirmasi'] }}%" title="Penyerahan Dikonfirmasi: {{ $catStat['terkonfirmasi_count'] }}"></div>
                                        @endif
                                    </div>
                                </div>

                                <div class="pt-1.5 border-t border-slate-200/70 flex flex-wrap items-center justify-between gap-2 font-telemetry text-[10px]">
                                    <a href="{{ route('public.aid-responses.create', ['incident' => $incident->slug, 'need_category' => $catStat['key'], 'submission_type' => 'tawaran_rencana']) }}"
                                       class="text-blue-700 hover:underline font-bold">
                                        + Tawaran Bantuan Kategori Ini
                                    </a>
                                    <a href="{{ route('public.aid-responses.create', ['incident' => $incident->slug, 'need_category' => $catStat['key'], 'submission_type' => 'laporan_penyerahan']) }}"
                                       class="text-emerald-800 hover:underline font-bold">
                                        + Lapor Bantuan Sudah Diberikan
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($incident->aidResponses->isEmpty())
                    <div class="bg-[#F8FAFC] border border-slate-100 rounded-xl p-5 text-center space-y-2">
                        <p class="text-sm font-semibold text-slate-700">Belum Ada Respons Bantuan Tercatat pada Kejadian Ini</p>
                        <p class="text-xs text-slate-500 max-w-md mx-auto">
                            @if(!$incident->is_simulation)
                                Kejadian historis resmi tidak mencatat respons bantuan relawan crowdsourcing pada sistem prototype ini. Penanganan darurat telah diselesaikan oleh BNPB/BPBD.
                            @else
                                Ketiadaan catatan pada tabel ini hanya menunjukkan belum adanya respons bantuan yang dilaporkan ke dalam sistem TanggapTerang.
                            @endif
                        </p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($incident->aidResponses as $aid)
                            @php
                                $aidStatusVal = $aid->status instanceof \BackedEnum ? $aid->status->value : (string) $aid->status;
                                $isDeliveredSubmission = $aid->isReportedDelivered();
                            @endphp
                            <article class="bg-[#F8FAFC] border border-slate-100/90 rounded-xl p-4 space-y-2.5">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2 py-0.5 rounded bg-[#173B70] text-white font-telemetry text-xs font-bold">
                                            {{ $aid->reference_code }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase {{ $isDeliveredSubmission ? 'bg-indigo-50 text-indigo-900 border border-indigo-200' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                                            {{ $aid->submissionTypeLabel() }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded bg-white border border-slate-200 font-telemetry text-[10px] font-bold uppercase text-slate-700">
                                            {{ \App\Models\AidResponse::categoryLabel($aid->need_category) }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 font-telemetry text-[10px] font-bold uppercase">
                                            Respons Bantuan Masyarakat
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <div class="flex items-center gap-1">
                                            <span class="font-telemetry text-[9px] uppercase font-bold text-slate-500">Pemeriksaan:</span>
                                            <x-verification-badge :status="$aid->verification_status" />
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <span class="font-telemetry text-[9px] uppercase font-bold text-slate-500">Operasional:</span>
                                            <x-aid-status-badge :status="$aid->status" />
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-1">
                                    <h3 class="text-sm sm:text-base font-bold text-[#202938]">
                                        {{ $aid->aid_item_title }}
                                    </h3>
                                    <span class="font-telemetry text-xs font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded">
                                        Satuan/Kuantitas: {{ $aid->quantity_label }}
                                    </span>
                                </div>

                                @if($aid->aid_description)
                                    <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                        {{ $aid->aid_description }}
                                    </p>
                                @endif

                                @if($aid->verification_notes || $aid->confirmation_notes || $aid->evidence_url)
                                    <div class="bg-white border border-slate-200 rounded p-2.5 text-xs text-slate-700 space-y-1.5">
                                        @if($aid->verification_notes)
                                            <div>
                                                <strong class="font-telemetry uppercase text-[10px] text-[#173B70] block">Catatan Pemeriksaan Verifikator:</strong>
                                                {{ $aid->verification_notes }}
                                            </div>
                                        @endif
                                        @if($aid->confirmation_notes)
                                            <div>
                                                <strong class="font-telemetry uppercase text-[10px] text-[#788397] block">Catatan Status / Konfirmasi Petugas:</strong>
                                                {{ $aid->confirmation_notes }}
                                            </div>
                                        @endif
                                        @if($aid->evidence_url)
                                            <div>
                                                <a href="{{ $aid->evidence_url }}"
                                                   target="_blank"
                                                   rel="noopener noreferrer"
                                                   class="inline-flex items-center gap-1 font-telemetry text-[11px] font-bold text-[#173B70] hover:underline">
                                                    <span>Lihat Tautan Bukti Dokumentasi</span>
                                                    <span aria-hidden="true">&nearr;</span>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <div class="pt-2 border-t border-slate-200/80 flex flex-wrap items-center justify-between gap-2 text-xs font-telemetry text-slate-500">
                                    <span>
                                        Pemberi Respons: <strong class="text-slate-800">{{ $aid->publicResponderName() }}</strong>
                                        • Lokasi Penyaluran: <strong class="text-slate-700">{{ $aid->target_location }}</strong>
                                        @if($aid->report)
                                            • Tiket Kebutuhan: <strong class="text-blue-700">{{ $aid->report->ticket_number }}</strong>
                                        @endif
                                        • <a href="{{ route('public.track', ['ticket' => $aid->reference_code]) }}" class="text-emerald-700 font-bold hover:underline">Lacak Tiket Respons &rarr;</a>
                                    </span>
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                                        @if($aid->delivered_at)
                                            <span class="text-indigo-800">Waktu Penyerahan (Dilaporkan): <strong>{{ $aid->delivered_at->format('d M Y, H:i') }} WIB</strong></span>
                                        @endif
                                        <span>Dicatat: <strong class="text-slate-700">{{ $aid->recorded_at?->format('d M Y, H:i') }} WIB</strong></span>
                                        @if($aid->confirmed_at)
                                            <span class="text-emerald-700">Dikonfirmasi: <strong>{{ $aid->confirmed_at->format('d M Y, H:i') }} WIB</strong></span>
                                        @elseif($aid->updated_at && $aid->recorded_at && $aid->updated_at->gt($aid->recorded_at))
                                            <span class="text-blue-700">Diperbarui: <strong>{{ $aid->updated_at->format('d M Y, H:i') }} WIB</strong></span>
                                        @else
                                            <span class="text-slate-400">Pembaruan: Belum ada perubahan</span>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif

                <div class="bg-[#FFFBEB] border border-amber-200/90 rounded-xl p-3.5 text-xs text-[#92400E] space-y-1">
                    <div>
                        <strong class="font-telemetry uppercase">Keterbatasan Cakupan Data:</strong>
                        Data ini hanya mencakup kebutuhan dan respons bantuan yang tercatat pada TanggapTerang.
                    </div>
                    <div>
                        <strong class="font-telemetry uppercase">Catatan Konfirmasi &amp; Bukti:</strong>
                        Tautan bukti dokumentasi yang diinput tidak otomatis membuktikan bantuan telah diterima; status tetap merupakan catatan konfirmasi oleh petugas TanggapTerang.
                    </div>
                </div>
            </section>

            <!-- Public Verification, Follow-Up & Aid Response Audit Timeline -->
            <section id="section-timeline" class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-4 sm:p-5 space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                        Riwayat Verifikasi, Tindak Lanjut Internal, &amp; Respons Bantuan
                    </h2>
                    <p class="text-xs text-[#788397] mt-0.5">
                        Jejak waktu transparan perubahan status verifikasi informasi, tindak lanjut tim TanggapTerang, dan respons bantuan masyarakat.
                    </p>
                </div>

                <x-audit-timeline
                    :verificationHistories="$incident->verificationHistories"
                    :followUpRecords="$incident->followUpRecords"
                    :aidResponses="$incident->aidResponses"
                    :isInternal="false"
                />
            </section>
        </div>
    </div>
@endsection

@if($hasCoordinates)
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mapEl = document.getElementById('incident-detail-map');
            if (!mapEl || typeof window.L === 'undefined') return;

            const lat = {{ (float) $incident->latitude }};
            const lng = {{ (float) $incident->longitude }};

            const map = window.L.map(mapEl, {
                scrollWheelZoom: false,
            }).setView([lat, lng], 14);

            window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            window.L.circleMarker([lat, lng], {
                radius: 10,
                fillColor: '#DC2626',
                color: '#FFFFFF',
                weight: 3,
                opacity: 1,
                fillOpacity: 0.95,
            }).addTo(map).bindPopup(@json('[KEJADIAN] ' . $incident->title)).openPopup();

            @php
                $incidentAidPoints = $incident->aidResponses
                    ->filter(fn ($a) => $a->latitude !== null && $a->longitude !== null)
                    ->map(fn ($a) => [
                        'lat' => (float) $a->latitude,
                        'lng' => (float) $a->longitude,
                        'label' => '[RESPONS BANTUAN] ' . $a->aid_item_title . ' (' . $a->quantity_label . ') — ' . $a->publicResponderName(),
                    ])
                    ->values();
            @endphp
            const aidPoints = @json($incidentAidPoints);
            aidPoints.forEach((pt) => {
                window.L.circleMarker([pt.lat, pt.lng], {
                    radius: 7,
                    fillColor: '#059669',
                    color: '#FFFFFF',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.95,
                }).addTo(map).bindPopup(pt.label);
            });

            const recenterBtn = document.getElementById('btn-recenter-incident-map');
            if (recenterBtn) {
                recenterBtn.addEventListener('click', () => {
                    map.setView([lat, lng], 14, { animate: true });
                });
            }
        });
    </script>
    @endpush
@endif
