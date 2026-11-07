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
            <a href="{{ route('public.monitor') }}" class="hover:text-[#0F172A] font-bold inline-flex items-center gap-1 shrink-0">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Kembali ke Peta Pantau</span>
            </a>
            <span class="shrink-0">/</span>
            <span class="text-slate-400 truncate min-w-0">{{ $incident->title }}</span>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if(\Illuminate\Support\Facades\Route::has('public.sitrep'))
                <a href="{{ route('public.sitrep', ['incident' => $incident->slug]) }}"
                   class="px-3 py-1.5 rounded-lg border border-[#CBD5E1] bg-white hover:bg-slate-50 text-[#0F172A] font-telemetry text-xs font-bold uppercase tracking-wider transition">
                    Cetak Ringkasan SitRep
                </a>
            @endif
            <a href="{{ route('public.aid-responses.create', ['incident' => $incident->slug]) }}"
               class="px-3 py-1.5 rounded-lg border border-blue-300 bg-blue-50 hover:bg-blue-100 text-blue-900 font-telemetry text-xs font-bold uppercase tracking-wider transition">
                + Catat Respons Bantuan
            </a>
            <a href="{{ route('public.reports.create') }}"
               class="px-3.5 py-1.5 rounded-lg btn-brand-gradient font-telemetry text-xs font-bold uppercase tracking-wider shadow-xs">
                + Tambah Laporan Warga
            </a>
        </div>
    </nav>

    <!-- Primary Incident Header Card -->
    <section class="bg-white border border-[#E2E8F0] rounded-lg p-5 sm:p-6 space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-4">
            <div class="flex flex-wrap items-center gap-2">
                @if($incident->is_simulation)
                    <span class="px-2.5 py-1 rounded bg-[#0F172A] text-white font-telemetry text-xs font-bold uppercase tracking-wider">
                        Data Simulasi
                    </span>
                @endif
                <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-800 border border-slate-300 font-telemetry text-xs font-bold uppercase tracking-wider">
                    {{ $typeLabel }}
                </span>
                <x-severity-badge :severity="$incident->severity" />
            </div>

            <div class="font-telemetry text-xs text-slate-500 flex flex-wrap items-center gap-3">
                <span>
                    Waktu Kejadian:
                    <strong class="text-[#0F172A]">{{ $incident->occurred_at?->format('d M Y, H:i') ?? '-' }} WIB</strong>
                </span>
                <span>•</span>
                <span>
                    Pembaruan Terakhir:
                    <strong class="text-[#0F172A]">{{ $incident->updated_at?->format('d M Y, H:i') ?? '-' }} WIB</strong>
                </span>
            </div>
        </div>

        <div class="space-y-3">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-[#0F172A] leading-tight">
                {{ $incident->title }}
            </h1>

            <!-- Administrative Location Info -->
            <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div class="space-y-1">
                    <div class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        Lokasi Kejadian &amp; Wilayah Administratif
                    </div>
                    <div class="text-sm font-bold text-[#0F172A] flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#DC2626] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                    Ringkasan Informasi Kejadian
                </h2>
                <p class="text-sm sm:text-base text-[#0F172A] leading-relaxed whitespace-pre-line">{{ $incident->description }}</p>
            </div>
        </div>

        <!-- Dual Separated Status Panel: Verification vs Follow-up -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
            <!-- Panel 1: Status Verifikasi Informasi -->
            <div class="bg-[#F8FAFC] border-l-4 border-[#2563EB] border-y border-r border-[#E2E8F0] rounded-lg p-4 space-y-2 min-w-0">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                        1. Status Verifikasi Informasi
                    </span>
                    <x-verification-badge :status="$incident->verification_status" />
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Menunjukkan hasil pemeriksaan tim verifikator TanggapTerang terhadap konsistensi laporan warga dan rujukan sumber informasi terbuka.
                </p>
            </div>

            <!-- Panel 2: Status Tindak Lanjut Tim TanggapTerang -->
            <div class="bg-[#F8FAFC] border-l-4 border-[#059669] border-y border-r border-[#E2E8F0] rounded-lg p-4 space-y-2 min-w-0">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                        2. Status Tindak Lanjut Tim TanggapTerang
                    </span>
                    <x-followup-badge :status="$incident->follow_up_status" />
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Mencatat perkembangan pemantauan, peninjauan informasi, dan dokumentasi oleh tim internal TanggapTerang.
                </p>
            </div>
        </div>

        <!-- Mandatory Institutional Notice regarding Follow-Up -->
        <div class="bg-[#EFF6FF] border border-[#93C5FD] rounded-lg p-3.5 text-xs text-[#1E40AF] leading-relaxed">
            <strong class="font-telemetry uppercase tracking-wider">Catatan Transparansi Tindak Lanjut:</strong>
            Status tindak lanjut di atas merupakan catatan internal tim TanggapTerang dalam memverifikasi dan mendokumentasikan informasi kejadian.
            <strong>Ketiadaan catatan tindak lanjut pada TanggapTerang tidak berarti instansi atau pihak berwenang eksternal tidak sedang menangani kejadian di lapangan.</strong>
        </div>
    </section>

    <!-- Main Content Grid: Left (Map + Open Sources) & Right (Connected Reports + Audit Timeline) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        <!-- Left Column (5 cols) -->
        <div class="lg:col-span-5 space-y-5 min-w-0">
            <!-- Incident Location Map -->
            <section class="bg-white border border-[#E2E8F0] rounded-lg overflow-hidden">
                <div class="p-4 border-b border-[#E2E8F0] bg-[#F8FAFC] flex items-center justify-between">
                    <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                        Peta Titik Kejadian
                    </h2>
                    @if($hasCoordinates)
                        <span class="font-telemetry text-[11px] text-slate-600">
                            {{ number_format((float) $incident->latitude, 4) }}, {{ number_format((float) $incident->longitude, 4) }}
                        </span>
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
            <section class="bg-white border border-[#E2E8F0] rounded-lg p-4 sm:p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                            Sumber Informasi Terbuka &amp; Rujukan
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Sumber informasi yang dirujuk untuk memvalidasi kejadian ini.
                        </p>
                    </div>
                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-800 font-telemetry text-xs font-bold">
                        {{ $incident->incidentSources->count() }} Sumber
                    </span>
                </div>

                @if($incident->incidentSources->isEmpty())
                    <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-4 text-xs text-slate-500 text-center">
                        Belum ada rujukan sumber informasi terbuka eksternal yang ditautkan pada kejadian ini.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($incident->incidentSources as $incSource)
                            <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-3.5 space-y-2">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    @if($incSource->source)
                                        <x-source-badge :type="$incSource->source->source_type" :isSimulation="(bool) $incSource->source->is_simulation" />
                                    @endif
                                    @if($incSource->published_at)
                                        <span class="font-telemetry text-[11px] text-slate-500">
                                            {{ $incSource->published_at->format('d M Y, H:i') }} WIB
                                        </span>
                                    @endif
                                </div>

                                <div class="text-sm font-bold text-[#0F172A]">
                                    {{ $incSource->headline ?: ($incSource->source?->name ?? 'Sumber Informasi') }}
                                </div>

                                @if($incSource->source)
                                    <div class="text-xs font-semibold text-slate-600">
                                        Lembaga / Kanal: {{ $incSource->source->name }}
                                    </div>
                                @endif

                                @if($incSource->notes)
                                    <p class="text-xs text-slate-600 leading-relaxed">
                                        {{ $incSource->notes }}
                                    </p>
                                @endif

                                @if($incSource->reference_url)
                                    <div class="pt-1">
                                        <a href="{{ $incSource->reference_url }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="font-telemetry text-xs font-bold text-[#2563EB] hover:underline inline-flex items-center gap-1">
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
            <section class="bg-white border border-[#E2E8F0] rounded-lg p-4 sm:p-5 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                            Laporan Masyarakat yang Terhubung
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Identitas pribadi dan kontak pelapor disembunyikan untuk menjaga privasi warga.
                        </p>
                    </div>
                    <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-800 font-telemetry text-xs font-bold">
                        {{ $incident->reports->count() }} Laporan Warga
                    </span>
                </div>

                @if($incident->reports->isEmpty())
                    <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-6 text-center space-y-2">
                        <p class="text-sm font-semibold text-slate-700">Belum Ada Laporan Masyarakat yang Terhubung</p>
                        <p class="text-xs text-slate-500">
                            Kejadian ini dicatat melalui pemantauan sumber terbuka atau laporan warga masih dalam antrean penautan.
                        </p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($incident->reports as $report)
                            <article class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-4 space-y-2.5">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2 py-0.5 rounded bg-[#0F172A] text-white font-telemetry text-xs font-bold">
                                            {{ $report->ticket_number }}
                                        </span>
                                        <x-source-badge type="laporan_masyarakat" />
                                        <x-verification-badge :status="$report->verification_status" />
                                    </div>
                                    <span class="font-telemetry text-xs text-slate-500">
                                        Dilaporkan: {{ $report->created_at?->format('d M Y, H:i') }} WIB
                                    </span>
                                </div>

                                <h3 class="text-sm sm:text-base font-bold text-[#0F172A]">
                                    {{ $report->title }}
                                </h3>

                                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                    {{ $report->description }}
                                </p>

                                <div class="pt-2 border-t border-slate-200/80 flex flex-wrap items-center justify-between gap-2 text-xs font-telemetry text-slate-500">
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
                                           class="text-[#2563EB] hover:underline font-bold">
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
            <section class="bg-white border border-[#E2E8F0] rounded-lg p-4 sm:p-5 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-800 border border-blue-200 font-telemetry text-[10px] font-bold uppercase">
                                Laporan Warga
                            </span>
                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 font-telemetry text-[10px] font-bold uppercase">
                                Respons Bantuan Masyarakat
                            </span>
                            <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                                Kebutuhan &amp; Respons Bantuan Masyarakat
                            </h2>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Membedakan secara jelas antara <strong>Tawaran / Rencana Bantuan</strong> dan <strong>Laporan Bantuan Sudah Diberikan</strong> (terpisah dari tindak lanjut internal tim).
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('public.aid-responses.create', ['incident' => $incident->slug, 'submission_type' => 'tawaran_rencana']) }}"
                           class="px-3 py-1.5 rounded-lg btn-brand-gradient font-telemetry text-[11px] font-bold uppercase tracking-wider shadow-xs">
                            + Ajukan Tawaran Bantuan
                        </a>
                        <a href="{{ route('public.aid-responses.create', ['incident' => $incident->slug, 'submission_type' => 'laporan_penyerahan']) }}"
                           class="px-3 py-1.5 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white font-telemetry text-[11px] font-bold uppercase tracking-wider shadow-xs">
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
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($activeIncidentStats as $catStat)
                            <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-3.5 space-y-2.5">
                                <div class="flex items-center justify-between gap-2 border-b border-slate-200/80 pb-1.5">
                                    <span class="text-xs font-bold text-[#0F172A]">{{ $catStat['label'] }}</span>
                                    <span class="font-telemetry text-[10px] font-semibold text-blue-800">
                                        {{ $catStat['factual_status'] }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-[11px] font-telemetry">
                                    <div class="bg-white border border-slate-200 rounded p-2">
                                        <span class="text-[10px] text-slate-500 block uppercase">1. Kebutuhan Tercatat</span>
                                        <strong class="text-[#2563EB] text-xs">{{ $catStat['needs_count'] }} laporan warga</strong>
                                    </div>
                                    <div class="bg-white border border-slate-200 rounded p-2">
                                        <span class="text-[10px] text-slate-500 block uppercase">2. Respons Tercatat</span>
                                        <strong class="text-[#0F172A] text-xs">{{ $catStat['responses_total'] }} entri respons</strong>
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
                    <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-5 text-center space-y-2">
                        <p class="text-sm font-semibold text-slate-700">Belum Ada Respons Bantuan Tercatat pada Kejadian Ini</p>
                        <p class="text-xs text-slate-500">
                            Ketiadaan catatan pada tabel ini hanya menunjukkan belum adanya respons bantuan yang dilaporkan ke dalam sistem TanggapTerang.
                        </p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($incident->aidResponses as $aid)
                            @php
                                $aidStatusVal = $aid->status instanceof \BackedEnum ? $aid->status->value : (string) $aid->status;
                                $isDeliveredSubmission = $aid->isReportedDelivered();
                            @endphp
                            <article class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-4 space-y-2.5">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2 py-0.5 rounded bg-slate-900 text-white font-telemetry text-xs font-bold">
                                            {{ $aid->reference_code }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase {{ $isDeliveredSubmission ? 'bg-indigo-50 text-indigo-900 border border-indigo-200' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                                            {{ $aid->submissionTypeLabel() }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded bg-white border border-slate-200 font-telemetry text-[10px] font-bold uppercase text-slate-700">
                                            {{ $needCategories[$aid->need_category] ?? $aid->need_category }}
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
                                    <h3 class="text-sm sm:text-base font-bold text-[#0F172A]">
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
                                                <strong class="font-telemetry uppercase text-[10px] text-blue-800 block">Catatan Pemeriksaan Verifikator:</strong>
                                                {{ $aid->verification_notes }}
                                            </div>
                                        @endif
                                        @if($aid->confirmation_notes)
                                            <div>
                                                <strong class="font-telemetry uppercase text-[10px] text-slate-500 block">Catatan Status / Konfirmasi Petugas:</strong>
                                                {{ $aid->confirmation_notes }}
                                            </div>
                                        @endif
                                        @if($aid->evidence_url)
                                            <div>
                                                <a href="{{ $aid->evidence_url }}"
                                                   target="_blank"
                                                   rel="noopener noreferrer"
                                                   class="inline-flex items-center gap-1 font-telemetry text-[11px] font-bold text-[#2563EB] hover:underline">
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

                <div class="bg-[#FFFBEB] border border-[#FCD34D] rounded-lg p-3 text-xs text-[#92400E] space-y-1">
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
            <section class="bg-white border border-[#E2E8F0] rounded-lg p-4 sm:p-5 space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                        Riwayat Verifikasi, Tindak Lanjut Internal, &amp; Respons Bantuan
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
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
        });
    </script>
    @endpush
@endif
