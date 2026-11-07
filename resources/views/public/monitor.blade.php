@extends('layouts.app', [
    'title' => 'Pantau Sebaran Kejadian — TanggapTerang',
    'activeTab' => 'pantau',
])

@section('content')
    <!-- Page Header & Primary CTA -->
    <section class="bg-white border border-[#E2E8F0] rounded-lg p-4 sm:p-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="space-y-1.5">
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-2 py-0.5 rounded bg-[#0F172A] text-white font-telemetry text-[10px] font-bold uppercase tracking-wider">
                    Pemantauan Publik
                </span>
                <span class="font-telemetry text-xs text-slate-500 font-semibold uppercase tracking-wider">
                    Diperbarui: {{ now()->format('d M Y, H:i') }} WIB
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#0F172A] tracking-tight">
                Peta &amp; Daftar Informasi Kejadian Masyarakat
            </h1>
            <p class="text-sm text-slate-600 max-w-3xl leading-relaxed">
                Menyajikan informasi kejadian yang dikumpulkan dari laporan masyarakat dan sumber informasi terbuka, lengkap dengan pemisahan antara <strong class="text-[#0F172A]">Status Verifikasi Informasi</strong> dan <strong class="text-[#0F172A]">Status Tindak Lanjut Tim TanggapTerang</strong>.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <a href="{{ route('public.track') }}"
               class="px-3.5 py-2.5 rounded-lg border border-[#CBD5E1] bg-white hover:bg-slate-50 text-[#0F172A] font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[44px] inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span>Lacak Tiket</span>
            </a>
            <a href="{{ route('public.aid-responses.create') }}"
               class="px-3.5 py-2.5 rounded-lg border border-blue-300 bg-blue-50 hover:bg-blue-100 text-blue-900 font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[44px] inline-flex items-center gap-1.5">
                <span class="font-extrabold text-sm leading-none">+</span>
                <span>Catat Respons Bantuan</span>
            </a>
            <a href="{{ route('public.reports.create') }}"
               class="px-4 py-2.5 rounded-lg btn-brand-gradient font-telemetry text-xs font-bold uppercase tracking-wider min-h-[44px] inline-flex items-center gap-1.5 shadow-xs">
                <span class="font-extrabold text-sm leading-none">+</span>
                <span>Kirim Laporan Masyarakat</span>
            </a>
        </div>
    </section>

    <!-- Bento Summary Cards (Database Simulation Metrics) -->
    <section aria-label="Ringkasan Data Kejadian Simulasi" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white border border-[#E2E8F0] rounded-lg p-3.5 flex flex-col justify-between">
            <span class="font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Kejadian</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="font-telemetry text-2xl font-bold text-[#0F172A]">{{ $summary['total_incidents'] }}</span>
                <span class="font-telemetry text-[10px] text-slate-500 uppercase">Simulasi</span>
            </div>
        </div>

        <div class="bg-white border border-[#93C5FD] rounded-lg p-3.5 flex flex-col justify-between">
            <span class="font-telemetry text-[11px] font-bold uppercase tracking-wider text-[#2563EB]">Terverifikasi</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="font-telemetry text-2xl font-bold text-[#2563EB]">{{ $summary['terverifikasi'] }}</span>
                <span class="font-telemetry text-[10px] text-[#1E40AF] uppercase">Valid</span>
            </div>
        </div>

        <div class="bg-white border border-[#FCD34D] rounded-lg p-3.5 flex flex-col justify-between">
            <span class="font-telemetry text-[11px] font-bold uppercase tracking-wider text-[#D97706]">Dalam Pemeriksaan</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="font-telemetry text-2xl font-bold text-[#D97706]">{{ $summary['dalam_pemeriksaan'] }}</span>
                <span class="font-telemetry text-[10px] text-[#92400E] uppercase">Tinjauan</span>
            </div>
        </div>

        <div class="bg-white border border-[#6EE7B7] rounded-lg p-3.5 flex flex-col justify-between">
            <span class="font-telemetry text-[11px] font-bold uppercase tracking-wider text-[#059669]">Ditindaklanjuti</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="font-telemetry text-2xl font-bold text-[#059669]">{{ $summary['sedang_ditindaklanjuti'] }}</span>
                <span class="font-telemetry text-[10px] text-[#065F46] uppercase">Aktif/Pantau</span>
            </div>
        </div>

        <div class="bg-white border border-[#FCA5A5] rounded-lg p-3.5 flex flex-col justify-between">
            <span class="font-telemetry text-[11px] font-bold uppercase tracking-wider text-[#DC2626]">Dampak Tinggi/Kritis</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="font-telemetry text-2xl font-bold text-[#DC2626]">{{ $summary['kritis_tinggi'] }}</span>
                <span class="font-telemetry text-[10px] text-[#991B1B] uppercase">Prioritas</span>
            </div>
        </div>

        <div class="bg-white border border-[#E2E8F0] rounded-lg p-3.5 flex flex-col justify-between">
            <span class="font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-500">Respons Bantuan</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="font-telemetry text-lg font-bold text-[#0F172A]">{{ $summary['total_aid_responses'] ?? 0 }} ({{ $summary['confirmed_aid_responses'] ?? 0 }} Sah)</span>
                <span class="font-telemetry text-[10px] text-emerald-700 uppercase">Masyarakat</span>
            </div>
        </div>
    </section>

    <!-- Search & Filter Bar -->
    <section aria-label="Filter dan Pencarian Kejadian" class="bg-white border border-[#E2E8F0] rounded-lg p-4">
        <form method="GET" action="{{ route('public.monitor') }}" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                <!-- Keyword Search -->
                <div class="md:col-span-4">
                    <label for="filter-q" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Cari Kejadian / Lokasi
                    </label>
                    <input type="text"
                           id="filter-q"
                           name="q"
                           value="{{ $filters['q'] }}"
                           placeholder="Nama kejadian, sungai, jalan, kecamatan..."
                           class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A] placeholder:text-slate-400 focus:outline-none focus:border-[#0F172A]">
                </div>

                <!-- Wilayah / Lokasi Filter -->
                <div class="md:col-span-3">
                    <label for="filter-wilayah" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Wilayah / Kawasan
                    </label>
                    <select id="filter-wilayah"
                            name="wilayah"
                            class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A] focus:outline-none focus:border-[#0F172A]">
                        <option value="">Semua Wilayah</option>
                        @foreach($availableLocations as $loc)
                            <option value="{{ $loc }}" @selected($filters['wilayah'] === $loc)>{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Verification Status Filter -->
                <div class="md:col-span-3">
                    <label for="filter-verification" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Status Verifikasi Informasi
                    </label>
                    <select id="filter-verification"
                            name="verification_status"
                            class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A] focus:outline-none focus:border-[#0F172A]">
                        <option value="">Semua Status Verifikasi</option>
                        @foreach(\App\Enums\VerificationStatus::cases() as $vCase)
                            @if($vCase !== \App\Enums\VerificationStatus::Ditolak)
                                <option value="{{ $vCase->value }}" @selected($filters['verification_status'] === $vCase->value)>
                                    {{ $vCase->label() }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- Follow-Up Status Filter -->
                <div class="md:col-span-2">
                    <label for="filter-followup" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Status Tindak Lanjut
                    </label>
                    <select id="filter-followup"
                            name="follow_up_status"
                            class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A] focus:outline-none focus:border-[#0F172A]">
                        <option value="">Semua Tindak Lanjut</option>
                        @foreach(\App\Enums\FollowUpStatus::cases() as $fCase)
                            <option value="{{ $fCase->value }}" @selected($filters['follow_up_status'] === $fCase->value)>
                                {{ $fCase->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-1 items-end">
                <!-- Incident Category Filter -->
                <div class="sm:col-span-3">
                    <label for="filter-type" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Jenis Kejadian
                    </label>
                    <select id="filter-type"
                            name="incident_type"
                            class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A] focus:outline-none focus:border-[#0F172A]">
                        <option value="">Semua Jenis Kejadian</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" @selected($filters['incident_type'] === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Severity Filter -->
                <div class="sm:col-span-3">
                    <label for="filter-severity" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Tingkat Dampak
                    </label>
                    <select id="filter-severity"
                            name="severity"
                            class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A] focus:outline-none focus:border-[#0F172A]">
                        <option value="">Semua Tingkat Dampak</option>
                        @foreach(\App\Enums\IncidentSeverity::cases() as $sCase)
                            <option value="{{ $sCase->value }}" @selected($filters['severity'] === $sCase->value)>
                                {{ $sCase->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Aid Response Status Filter -->
                <div class="sm:col-span-3">
                    <label for="filter-aid-status" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Status Respons Bantuan
                    </label>
                    <select id="filter-aid-status"
                            name="aid_status"
                            class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A] focus:outline-none focus:border-[#0F172A]">
                        <option value="">Semua Status Respons</option>
                        @foreach(\App\Enums\AidResponseStatus::cases() as $aCase)
                            <option value="{{ $aCase->value }}" @selected(($filters['aid_status'] ?? '') === $aCase->value)>
                                {{ $aCase->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="sm:col-span-3 flex items-center justify-end gap-2">
                    @if(array_filter($filters))
                        <a href="{{ route('public.monitor') }}"
                           class="px-3 py-2.5 rounded-lg border border-[#CBD5E1] bg-white hover:bg-slate-50 text-slate-700 font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[42px] inline-flex items-center">
                            Reset
                        </a>
                    @endif
                    <button type="submit"
                            class="px-4 py-2.5 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[42px] inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span>Filter</span>
                    </button>
                </div>
            </div>
        </form>
    </section>

    <!-- Main Split View: Map & Latest Incidents List -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        <!-- Left/Top Column: Interactive Leaflet Map -->
        <section class="lg:col-span-5 lg:sticky lg:top-20 bg-white border border-[#E2E8F0] rounded-lg overflow-hidden">
            <div class="p-3.5 border-b border-[#E2E8F0] space-y-2 bg-[#F8FAFC]">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                            Peta Kejadian, Kebutuhan, &amp; Respons Bantuan
                        </h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            {{ $mapIncidents->count() }} kejadian • {{ ($mapReportNeeds ?? collect())->count() }} laporan kebutuhan • {{ ($mapAidResponses ?? collect())->count() }} respons bantuan
                        </p>
                    </div>
                    <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-800 font-telemetry text-[10px] font-bold uppercase shrink-0">
                        Simulasi GIS
                    </span>
                </div>

                <!-- Map Layer Filter Pills -->
                <div class="flex flex-wrap items-center gap-1.5 pt-1" role="group" aria-label="Filter Layer Peta">
                    <button type="button" data-map-layer="all" class="map-layer-btn px-2 py-1 rounded bg-[#0F172A] text-white font-telemetry text-[10px] font-bold uppercase cursor-pointer">
                        Semua Layer
                    </button>
                    <button type="button" data-map-layer="incidents" class="map-layer-btn px-2 py-1 rounded bg-white border border-slate-300 text-slate-700 font-telemetry text-[10px] font-bold uppercase cursor-pointer">
                        Kejadian ({{ $mapIncidents->count() }})
                    </button>
                    <button type="button" data-map-layer="needs" class="map-layer-btn px-2 py-1 rounded bg-white border border-slate-300 text-slate-700 font-telemetry text-[10px] font-bold uppercase cursor-pointer">
                        Kebutuhan ({{ ($mapReportNeeds ?? collect())->count() }})
                    </button>
                    <button type="button" data-map-layer="aid" class="map-layer-btn px-2 py-1 rounded bg-white border border-slate-300 text-slate-700 font-telemetry text-[10px] font-bold uppercase cursor-pointer">
                        Respons Bantuan ({{ ($mapAidResponses ?? collect())->count() }})
                    </button>
                </div>
            </div>

            <div id="monitor-map" class="w-full h-[360px] sm:h-[440px] bg-slate-100 z-10"></div>

            <!-- Map Legend -->
            <div class="p-3 border-t border-[#E2E8F0] bg-white space-y-2">
                <div class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    Legenda Peta Kejadian &amp; Respons Bantuan
                </div>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5 text-xs font-telemetry">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-[#DC2626] inline-block"></span>
                        <span>Kejadian Kritis</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-[#D97706] inline-block"></span>
                        <span>Kejadian Tinggi</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#1E40AF] ring-2 ring-blue-200 inline-block"></span>
                        <span>Laporan Kebutuhan</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-xs bg-[#059669] border border-white inline-block"></span>
                        <span>Respons Bantuan Tercatat</span>
                    </span>
                </div>
                <p class="text-[11px] text-slate-500 leading-snug">
                    Catatan: Data hanya menampilkan titik yang memiliki koordinat valid pada sistem dan tidak merepresentasikan seluruh kondisi di lapangan.
                </p>
            </div>
        </section>

        <!-- Right/Bottom Column: Latest Incidents Feed -->
        <section class="lg:col-span-7 space-y-4" aria-label="Daftar Kejadian Terbaru">
            <div class="bg-white border border-[#E2E8F0] rounded-lg px-4 py-3 flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                        Daftar Kejadian Terbaru
                    </h2>
                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-telemetry text-xs font-bold">
                        {{ $incidents->total() }} Kejadian
                    </span>
                </div>
                <span class="font-telemetry text-[11px] text-slate-500">
                    Diurutkan dari waktu kejadian terbaru
                </span>
            </div>

            @if($incidents->isEmpty())
                <div class="bg-white border border-[#E2E8F0] rounded-lg p-8 text-center space-y-3">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-500 mx-auto flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-[#0F172A]">Tidak Ada Kejadian yang Sesuai Filter</h3>
                    <p class="text-sm text-slate-600 max-w-md mx-auto">
                        Belum ada data kejadian yang cocok dengan kata kunci atau kriteria filter yang Anda pilih.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('public.monitor') }}"
                           class="px-4 py-2 rounded-lg bg-[#0F172A] text-white font-telemetry text-xs font-bold uppercase tracking-wider inline-flex items-center">
                            Tampilkan Semua Kejadian
                        </a>
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($incidents as $incident)
                        <x-incident-card :incident="$incident" />
                    @endforeach
                </div>

                @if($incidents->hasPages())
                    <div class="bg-white border border-[#E2E8F0] rounded-lg p-4">
                        {{ $incidents->links() }}
                    </div>
                @endif
            @endif
        </section>
    </div>

    <!-- Diagram Alur Transparansi Informasi & Klasifikasi Sumber Data -->
    <x-transparency-flow-diagram />

    <!-- Visualisasi Terpisah: Kebutuhan Tercatat, Respons Bantuan Masyarakat, & Status Respons -->
    <section aria-label="Transparansi Kebutuhan dan Respons Bantuan Masyarakat" class="bg-white border border-[#E2E8F0] rounded-lg p-4 sm:p-6 space-y-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-200 pb-4">
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-800 border border-blue-200 font-telemetry text-[10px] font-bold uppercase tracking-wider">
                        Laporan Warga
                    </span>
                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 font-telemetry text-[10px] font-bold uppercase tracking-wider">
                        Respons Bantuan Masyarakat
                    </span>
                    <span class="font-telemetry text-xs text-slate-500">
                        Transparansi Kebutuhan &amp; Respons Bantuan Tercatat
                    </span>
                </div>
                <h2 class="text-lg sm:text-xl font-extrabold text-[#0F172A] tracking-tight">
                    Pemantauan Kebutuhan Tercatat, Respons Bantuan Masyarakat, &amp; Status Penyaluran
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 max-w-3xl leading-relaxed">
                    Visualisasi faktual yang memisahkan jumlah laporan kebutuhan warga, jumlah entri serta rincian satuan respons bantuan masyarakat/komunitas, dan proporsi status respons bantuan yang tercatat di sistem TanggapTerang.
                </p>
            </div>

            <div class="shrink-0 flex flex-wrap items-center gap-2">
                <a href="{{ route('public.aid-responses.create', ['submission_type' => 'tawaran_rencana']) }}"
                   class="px-3.5 py-2 rounded-lg btn-brand-gradient font-telemetry text-xs font-bold uppercase tracking-wider min-h-[42px] inline-flex items-center gap-1.5 shadow-xs">
                    <span class="font-extrabold text-sm leading-none">+</span>
                    <span>Ajukan Tawaran Bantuan</span>
                </a>
                <a href="{{ route('public.aid-responses.create', ['submission_type' => 'laporan_penyerahan']) }}"
                   class="px-3.5 py-2 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white font-telemetry text-xs font-bold uppercase tracking-wider min-h-[42px] inline-flex items-center gap-1.5 shadow-xs">
                    <span class="font-extrabold text-sm leading-none">+</span>
                    <span>Laporkan Bantuan Sudah Diberikan</span>
                </a>
            </div>
        </div>

        @php
            $statsCollection = collect($categoryStats ?? []);
            $maxNeedsCount = max((int) $statsCollection->max('needs_count'), 1);
            $maxResponsesCount = max((int) $statsCollection->max('responses_total'), 1);
        @endphp

        <!-- 3 Bagian Visualisasi Terpisah -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
            <!-- BAGIAN 1: KEBUTUHAN TERCATAT -->
            <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-4 space-y-3">
                <div class="border-b border-slate-200 pb-2.5">
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-900 font-telemetry text-[10px] font-bold uppercase">
                            1. Laporan Warga
                        </span>
                        <span class="font-telemetry text-[11px] text-slate-500">Satuan: Laporan</span>
                    </div>
                    <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] mt-1.5">
                        Kebutuhan Tercatat per Kategori
                    </h3>
                    <p class="text-[11px] text-slate-600 mt-0.5">
                        Jumlah laporan warga yang mencantumkan kebutuhan mendesak pada setiap kategori.
                    </p>
                </div>

                <div class="space-y-3">
                    @foreach($statsCollection as $catStat)
                        @php
                            $needBarWidth = $catStat['needs_count'] > 0
                                ? max(10, (int) round(($catStat['needs_count'] / $maxNeedsCount) * 100))
                                : 0;
                        @endphp
                        <div class="space-y-1 text-[11px] font-telemetry">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-bold text-[#0F172A]">{{ $catStat['label'] }}</span>
                                <span class="font-bold text-[#2563EB]">{{ $catStat['needs_count'] }} laporan</span>
                            </div>
                            <div class="w-full h-2.5 bg-slate-200 rounded-full overflow-hidden">
                                <div class="h-full bg-[#2563EB] rounded-full" style="width: {{ $needBarWidth }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- BAGIAN 2: RESPONS BANTUAN TERCATAT & RINCIAN SATUAN TERPISAH -->
            <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-4 space-y-3">
                <div class="border-b border-slate-200 pb-2.5">
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-900 font-telemetry text-[10px] font-bold uppercase">
                            2. Respons Bantuan Masyarakat
                        </span>
                        <span class="font-telemetry text-[11px] text-slate-500">Satuan: Entri &amp; Unit Asli</span>
                    </div>
                    <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] mt-1.5">
                        Respons Bantuan Tercatat per Kategori
                    </h3>
                    <p class="text-[11px] text-slate-600 mt-0.5">
                        Jumlah entri respons dan rincian kapasitas/satuan ditampilkan terpisah tanpa menjumlahkan lintas satuan.
                    </p>
                </div>

                <div class="space-y-3">
                    @foreach($statsCollection as $catStat)
                        @php
                            $respBarWidth = $catStat['responses_total'] > 0
                                ? max(10, (int) round(($catStat['responses_total'] / $maxResponsesCount) * 100))
                                : 0;
                        @endphp
                        <div class="space-y-1.5 text-[11px] font-telemetry border-b border-slate-200/70 pb-2.5 last:border-b-0 last:pb-0">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-bold text-[#0F172A]">{{ $catStat['label'] }}</span>
                                <span class="font-bold text-slate-800">{{ $catStat['responses_total'] }} entri respons</span>
                            </div>
                            <div class="w-full h-2.5 bg-slate-200 rounded-full overflow-hidden">
                                <div class="h-full bg-slate-700 rounded-full" style="width: {{ $respBarWidth }}%"></div>
                            </div>
                            @if(!empty($catStat['quantity_labels']))
                                <div class="flex flex-wrap gap-1 pt-0.5">
                                    @foreach($catStat['quantity_labels'] as $qtyLabel)
                                        <span class="px-1.5 py-0.5 rounded bg-white border border-slate-200 text-[10px] text-slate-700">
                                            {{ $qtyLabel }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-[10px] text-slate-400 block">Belum ada rincian kuantitas tercatat</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- BAGIAN 3: STATUS RESPONS BANTUAN (STACKED BAR 3 WARNA) -->
            <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-4 space-y-3">
                <div class="border-b border-slate-200 pb-2.5">
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-900 font-telemetry text-[10px] font-bold uppercase">
                            3. Proporsi Status Respons
                        </span>
                        <span class="font-telemetry text-[11px] text-slate-500">Stacked Bar 3 Warna</span>
                    </div>
                    <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] mt-1.5">
                        Distribusi Status Respons Bantuan
                    </h3>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[10px] font-telemetry mt-1.5">
                        <span class="inline-flex items-center gap-1">
                            <span class="w-2.5 h-2.5 rounded-xs bg-amber-500 inline-block"></span>
                            <span>Direncanakan</span>
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <span class="w-2.5 h-2.5 rounded-xs bg-[#2563EB] inline-block"></span>
                            <span>Dalam Penyaluran</span>
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <span class="w-2.5 h-2.5 rounded-xs bg-[#059669] inline-block"></span>
                            <span>Penyerahan Dikonfirmasi</span>
                        </span>
                    </div>
                </div>

                <div class="space-y-3">
                    @foreach($statsCollection as $catStat)
                        <div class="space-y-1 text-[11px] font-telemetry border-b border-slate-200/70 pb-2.5 last:border-b-0 last:pb-0">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-bold text-[#0F172A]">{{ $catStat['label'] }}</span>
                                <span class="text-[10px] text-slate-600">{{ $catStat['factual_status'] }}</span>
                            </div>

                            @if($catStat['responses_total'] > 0)
                                <!-- 3-Color Stacked Bar: Amber (Direncanakan), Biru (Dalam Penyaluran), Hijau (Penyerahan Dikonfirmasi) -->
                                <div class="w-full h-3 bg-slate-200 rounded-full overflow-hidden flex" role="img" aria-label="Status respons kategori {{ $catStat['label'] }}">
                                    @if($catStat['direncanakan_count'] > 0)
                                        <div class="h-full bg-amber-500"
                                             style="width: {{ $catStat['pct_direncanakan'] }}%"
                                             title="Direncanakan: {{ $catStat['direncanakan_count'] }} ({{ $catStat['pct_direncanakan'] }}%)"></div>
                                    @endif
                                    @if($catStat['dalam_penyaluran_count'] > 0)
                                        <div class="h-full bg-[#2563EB]"
                                             style="width: {{ $catStat['pct_dalam_penyaluran'] }}%"
                                             title="Dalam Penyaluran: {{ $catStat['dalam_penyaluran_count'] }} ({{ $catStat['pct_dalam_penyaluran'] }}%)"></div>
                                    @endif
                                    @if($catStat['terkonfirmasi_count'] > 0)
                                        <div class="h-full bg-[#059669]"
                                             style="width: {{ $catStat['pct_terkonfirmasi'] }}%"
                                             title="Penyerahan Dikonfirmasi: {{ $catStat['terkonfirmasi_count'] }} ({{ $catStat['pct_terkonfirmasi'] }}%)"></div>
                                    @endif
                                </div>
                                <div class="flex flex-wrap items-center justify-between gap-1 text-[10px] text-slate-600">
                                    <span class="text-amber-800">Direncanakan: {{ $catStat['direncanakan_count'] }}</span>
                                    <span class="text-blue-800">Penyaluran: {{ $catStat['dalam_penyaluran_count'] }}</span>
                                    <span class="text-emerald-800">Dikonfirmasi: {{ $catStat['terkonfirmasi_count'] }}</span>
                                </div>
                            @else
                                <div class="w-full h-3 bg-slate-200 rounded-full overflow-hidden"></div>
                                <span class="text-[10px] text-slate-400 block">Belum ada respons tercatat pada kategori ini</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Tabel Kebutuhan & Respons Bantuan Tercatat -->
        <div class="space-y-3 pt-2">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                    Daftar Respons Bantuan Masyarakat / Organisasi yang Tercatat ({{ ($publicAidResponses ?? collect())->count() }})
                </h3>
                <span class="font-telemetry text-[11px] text-slate-500">
                    Kontak pribadi pemberi respons disembunyikan demi privasi
                </span>
            </div>

            @if(($publicAidResponses ?? collect())->isEmpty())
                <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-6 text-center text-sm text-slate-600">
                    Belum ada respons bantuan yang tercatat pada sistem.
                </div>
            @else
                <div class="overflow-x-auto border border-[#E2E8F0] rounded-lg">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] font-telemetry text-[11px] font-bold uppercase text-slate-600">
                                <th class="py-2.5 px-3">Kode &amp; Waktu Pencatatan / Pembaruan</th>
                                <th class="py-2.5 px-3">Kejadian &amp; Lokasi Tujuan</th>
                                <th class="py-2.5 px-3">Kategori &amp; Jenis Bantuan</th>
                                <th class="py-2.5 px-3">Pemberi Respons &amp; Klasifikasi</th>
                                <th class="py-2.5 px-3">Status &amp; Tautan Dokumentasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-xs">
                            @foreach($publicAidResponses as $aid)
                                @php
                                    $aidStatusVal = $aid->status instanceof \BackedEnum ? $aid->status->value : (string) $aid->status;
                                    $isDeliveredSubmission = $aid->isReportedDelivered();
                                @endphp
                                <tr class="hover:bg-slate-50 align-top">
                                    <td class="py-3 px-3 whitespace-nowrap space-y-1">
                                        <span class="font-telemetry font-bold text-[#0F172A] block">{{ $aid->reference_code }}</span>
                                        <span class="px-1.5 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase inline-block {{ $isDeliveredSubmission ? 'bg-indigo-50 text-indigo-900 border border-indigo-200' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                                            {{ $aid->submissionTypeLabel() }}
                                        </span>
                                        @if($aid->delivered_at)
                                            <div class="font-telemetry text-[11px] text-indigo-800">
                                                <span class="font-semibold">Diserahkan (Dilaporkan):</span> {{ $aid->delivered_at->format('d M Y, H:i') }} WIB
                                            </div>
                                        @endif
                                        <div class="font-telemetry text-[11px] text-slate-600">
                                            <span class="text-slate-500">Dicatat:</span> {{ $aid->recorded_at?->format('d M Y, H:i') }} WIB
                                        </div>
                                        <div class="font-telemetry text-[11px] text-slate-600">
                                            @if($aid->confirmed_at)
                                                <span class="text-emerald-700 font-semibold">Dikonfirmasi:</span> {{ $aid->confirmed_at->format('d M Y, H:i') }} WIB
                                            @elseif($aid->updated_at && $aid->recorded_at && $aid->updated_at->gt($aid->recorded_at))
                                                <span class="text-blue-700 font-semibold">Diperbarui:</span> {{ $aid->updated_at->format('d M Y, H:i') }} WIB
                                            @else
                                                <span class="text-slate-400">Pembaruan: Belum ada perubahan status</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 px-3">
                                        @if($aid->incident)
                                            <a href="{{ route('public.incidents.show', $aid->incident->slug) }}" class="font-bold text-[#2563EB] hover:underline block">
                                                {{ $aid->incident->title }}
                                            </a>
                                        @endif
                                        <span class="text-slate-600 block mt-0.5">Lokasi: {{ $aid->target_location }}</span>
                                        @if($aid->report)
                                            <span class="font-telemetry text-[11px] text-slate-500 block mt-0.5">
                                                Rujukan Tiket: <strong class="text-slate-700">{{ $aid->report->ticket_number }}</strong>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200 font-telemetry text-[10px] font-bold uppercase inline-block mb-1">
                                            {{ $needCategories[$aid->need_category] ?? $aid->need_category }}
                                        </span>
                                        <strong class="text-[#0F172A] block">{{ $aid->aid_item_title }}</strong>
                                        <span class="font-telemetry text-[11px] text-slate-600">Satuan/Kuantitas: {{ $aid->quantity_label }}</span>
                                    </td>
                                    <td class="py-3 px-3 space-y-1">
                                        <strong class="text-[#0F172A] block">{{ $aid->publicResponderName() }}</strong>
                                        <span class="font-telemetry text-[11px] text-slate-500 block">
                                            {{ $responderTypes[$aid->responder_type] ?? $aid->responder_type }}
                                        </span>
                                        <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 font-telemetry text-[10px] font-bold uppercase inline-block">
                                            Respons Bantuan Masyarakat
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 space-y-1.5">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-1">
                                                <span class="font-telemetry text-[9px] uppercase font-bold text-slate-500">Pemeriksaan:</span>
                                                <x-verification-badge :status="$aid->verification_status" />
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <span class="font-telemetry text-[9px] uppercase font-bold text-slate-500">Operasional:</span>
                                                <x-aid-status-badge :status="$aid->status" />
                                            </div>
                                        </div>
                                        @if($isDeliveredSubmission && $aidStatusVal !== 'terkonfirmasi')
                                            <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-900 border border-amber-300 font-telemetry text-[10px] font-bold uppercase block">
                                                Dilaporkan Sudah Diserahkan • Menunggu Pemeriksaan Petugas
                                            </span>
                                        @endif
                                        @if($aid->confirmation_notes)
                                            <p class="text-[11px] text-slate-600 max-w-xs leading-snug">
                                                {{ $aid->confirmation_notes }}
                                            </p>
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
                                        <div class="pt-0.5">
                                            <a href="{{ route('public.track', ['ticket' => $aid->reference_code]) }}"
                                               class="inline-flex items-center gap-1 font-telemetry text-[11px] font-bold text-emerald-700 hover:text-emerald-900 hover:underline">
                                                <span>Lacak Tiket Respons</span>
                                                <span aria-hidden="true">&rarr;</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="bg-[#FFFBEB] border border-[#FCD34D] rounded-lg p-3 text-xs text-[#92400E] space-y-1">
                <div>
                    <strong class="font-telemetry uppercase">Catatan Transparansi &amp; Cakupan Data:</strong>
                    Data di atas hanya menampilkan kebutuhan dan respons bantuan yang telah dilaporkan/dicatat di dalam TanggapTerang, dan tidak merepresentasikan keseluruhan bantuan yang berada di lapangan.
                </div>
                <div>
                    <strong class="font-telemetry uppercase">Keterangan Bukti Dokumentasi:</strong>
                    Tautan dokumentasi yang diinput tidak otomatis membuktikan bantuan telah diterima secara menyeluruh di lapangan; status yang tampil merupakan catatan konfirmasi administratif oleh petugas TanggapTerang.
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const mapContainer = document.getElementById('monitor-map');
        if (!mapContainer || typeof window.L === 'undefined') return;

        const markersData = @json($mapIncidents);
        const needsData = @json($mapReportNeeds ?? []);
        const aidData = @json($mapAidResponses ?? []);

        const defaultCenter = [-6.9175, 107.6191];
        const map = window.L.map(mapContainer, {
            scrollWheelZoom: false,
        }).setView(defaultCenter, 9);

        window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const severityColorMap = {
            kritis: '#DC2626',
            tinggi: '#D97706',
            sedang: '#2563EB',
            rendah: '#059669',
        };

        const incidentsLayer = window.L.layerGroup().addTo(map);
        const needsLayer = window.L.layerGroup().addTo(map);
        const aidLayer = window.L.layerGroup().addTo(map);

        const bounds = [];

        markersData.forEach((item) => {
            if (typeof item.latitude !== 'number' || typeof item.longitude !== 'number') {
                return;
            }

            const color = severityColorMap[item.severity] || '#0F172A';
            const marker = window.L.circleMarker([item.latitude, item.longitude], {
                radius: 9,
                fillColor: color,
                color: '#FFFFFF',
                weight: 2.5,
                opacity: 1,
                fillOpacity: 0.92,
            }).addTo(incidentsLayer);

            const popupHtml = `
                <div style="min-width: 220px; font-family: 'Public Sans', sans-serif; color: #0F172A;">
                    <div style="font-family: 'Space Grotesk', monospace; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #475569; margin-bottom: 4px;">
                        [KEJADIAN] • ${item.incident_type}
                    </div>
                    <div style="font-size: 14px; font-weight: 700; line-height: 1.3; margin-bottom: 4px;">
                        ${item.title}
                    </div>
                    <div style="font-size: 12px; color: #475569; margin-bottom: 8px;">
                        ${item.location_name}${item.wilayah_nama ? ' • ' + item.wilayah_nama : ''}
                    </div>
                    <div style="font-family: 'Space Grotesk', monospace; font-size: 11px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; padding: 6px; margin-bottom: 8px;">
                        <div><strong>Verifikasi:</strong> ${item.verification_label}</div>
                        <div><strong>Tindak Lanjut Tim:</strong> ${item.follow_up_label}</div>
                        <div><strong>Laporan:</strong> ${item.reports_count} | <strong>Respons Bantuan:</strong> ${item.aid_responses_count || 0}</div>
                    </div>
                    <a href="${item.url}" style="display: inline-block; width: 100%; text-align: center; background: #0F172A; color: #FFFFFF; text-decoration: none; padding: 6px 10px; border-radius: 6px; font-family: 'Space Grotesk', monospace; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                        Lihat Detail Kejadian &rarr;
                    </a>
                </div>
            `;

            marker.bindPopup(popupHtml);
            bounds.push([item.latitude, item.longitude]);
        });

        needsData.forEach((need) => {
            if (typeof need.latitude !== 'number' || typeof need.longitude !== 'number') {
                return;
            }

            const marker = window.L.circleMarker([need.latitude, need.longitude], {
                radius: 6,
                fillColor: '#1E40AF',
                color: '#DBEAFE',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.9,
            }).addTo(needsLayer);

            const popupHtml = `
                <div style="min-width: 200px; font-family: 'Public Sans', sans-serif; color: #0F172A;">
                    <div style="font-family: 'Space Grotesk', monospace; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #1E40AF; margin-bottom: 4px;">
                        [KEBUTUHAN DILAPORKAN] • ${need.ticket_number}
                    </div>
                    <div style="font-size: 13px; font-weight: 700; line-height: 1.3; margin-bottom: 4px;">
                        ${need.title}
                    </div>
                    <div style="font-size: 11px; color: #475569; margin-bottom: 6px;">
                        Kategori: <strong>${need.need_category_label}</strong><br>
                        Lokasi: ${need.location_name}<br>
                        Status: ${need.verification_label}
                    </div>
                    <a href="${need.track_url}" style="display: inline-block; width: 100%; text-align: center; background: #2563EB; color: #FFFFFF; text-decoration: none; padding: 5px 8px; border-radius: 6px; font-family: 'Space Grotesk', monospace; font-size: 10px; font-weight: 700; text-transform: uppercase;">
                        Lacak Tiket Laporan &rarr;
                    </a>
                </div>
            `;

            marker.bindPopup(popupHtml);
            bounds.push([need.latitude, need.longitude]);
        });

        aidData.forEach((aid) => {
            if (typeof aid.latitude !== 'number' || typeof aid.longitude !== 'number') {
                return;
            }

            const marker = window.L.circleMarker([aid.latitude, aid.longitude], {
                radius: 7,
                fillColor: '#059669',
                color: '#FFFFFF',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.95,
            }).addTo(aidLayer);

            const popupHtml = `
                <div style="min-width: 210px; font-family: 'Public Sans', sans-serif; color: #0F172A;">
                    <div style="font-family: 'Space Grotesk', monospace; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #059669; margin-bottom: 4px;">
                        [RESPONS BANTUAN] • ${aid.reference_code}
                    </div>
                    <div style="font-size: 13px; font-weight: 700; line-height: 1.3; margin-bottom: 4px;">
                        ${aid.aid_item_title}
                    </div>
                    <div style="font-size: 11px; color: #475569; margin-bottom: 6px;">
                        Kapasitas: <strong>${aid.quantity_label}</strong><br>
                        Pemberi Respons: <strong>${aid.responder_name}</strong><br>
                        Tujuan: ${aid.target_location}<br>
                        Status: <strong>${aid.status_label}</strong>
                    </div>
                    <a href="${aid.incident_url}" style="display: inline-block; width: 100%; text-align: center; background: #059669; color: #FFFFFF; text-decoration: none; padding: 5px 8px; border-radius: 6px; font-family: 'Space Grotesk', monospace; font-size: 10px; font-weight: 700; text-transform: uppercase;">
                        Buka Kejadian Terkait &rarr;
                    </a>
                </div>
            `;

            marker.bindPopup(popupHtml);
            bounds.push([aid.latitude, aid.longitude]);
        });

        if (bounds.length === 1) {
            map.setView(bounds[0], 13);
        } else if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [36, 36], maxZoom: 13 });
        }

        // Layer Filter Buttons
        const layerButtons = document.querySelectorAll('.map-layer-btn');
        layerButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                const mode = btn.getAttribute('data-map-layer');
                layerButtons.forEach((b) => {
                    b.className = 'map-layer-btn px-2 py-1 rounded bg-white border border-slate-300 text-slate-700 font-telemetry text-[10px] font-bold uppercase cursor-pointer';
                });
                btn.className = 'map-layer-btn px-2 py-1 rounded bg-[#0F172A] text-white font-telemetry text-[10px] font-bold uppercase cursor-pointer';

                map.removeLayer(incidentsLayer);
                map.removeLayer(needsLayer);
                map.removeLayer(aidLayer);

                if (mode === 'all' || mode === 'incidents') map.addLayer(incidentsLayer);
                if (mode === 'all' || mode === 'needs') map.addLayer(needsLayer);
                if (mode === 'all' || mode === 'aid') map.addLayer(aidLayer);
            });
        });
    });
</script>
@endpush
