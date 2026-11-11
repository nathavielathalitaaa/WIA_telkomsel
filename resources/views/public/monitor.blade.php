@extends('layouts.app', [
    'title' => 'Pantau Sebaran Kejadian — TanggapTerang',
    'activeTab' => 'pantau',
])

@section('content')
<div class="space-y-6">
    <!-- Top Editorial Header (Clean, Light, Aligned with Master Design) -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-200/80 pb-4">
        <div class="space-y-1 max-w-2xl">
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#202938]">
                Pantau Sebaran Kejadian Bencana
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                Pantau sebaran spasial kejadian bencana, kebutuhan mendesak warga, dan penyaluran bantuan di lapangan.
            </p>
        </div>

        <!-- Quick Action Buttons -->
        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <a href="{{ route('public.track') }}"
               class="px-3.5 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-telemetry text-xs font-bold uppercase tracking-wider transition inline-flex items-center gap-1.5 shadow-2xs min-h-[38px]">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span>Lacak Tiket</span>
            </a>
            <a href="{{ route('public.aid-responses.create') }}"
               class="px-3.5 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-telemetry text-xs font-bold uppercase tracking-wider transition inline-flex items-center gap-1 shadow-2xs min-h-[38px]">
                <span class="font-extrabold text-sm leading-none text-[#173B70]">+</span>
                <span>Catat Bantuan</span>
            </a>
            <a href="{{ route('public.reports.create') }}"
               class="px-4 py-2 rounded-lg btn-brand-gradient text-white font-telemetry text-xs font-bold uppercase tracking-wider inline-flex items-center gap-1.5 shadow-xs transition min-h-[38px]">
                <span class="font-extrabold text-sm leading-none">+</span>
                <span>Kirim Laporan Warga</span>
            </a>
        </div>
    </div>

    <!-- Ringkasan Jumlah Kejadian — Indikator Sederhana (Minimal Telemetry Strip) -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none text-xs font-telemetry" role="navigation" aria-label="Ringkasan dan Filter Cepat">
        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] shrink-0 mr-1">Status:</span>
        <a href="{{ route('public.monitor') }}"
           class="px-3 py-1.5 rounded-lg border transition shrink-0 inline-flex items-center gap-1.5 {{ empty(request()->except('page')) ? 'bg-[#173B70] text-white border-[#173B70] font-bold shadow-2xs' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200 font-medium' }}"
           title="Tampilkan semua kejadian">
            <span>Semua</span>
            <span class="px-1.5 py-0.5 rounded text-[10px] {{ empty(request()->except('page')) ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700 font-bold' }}">{{ $summary['total_incidents'] }}</span>
        </a>

        <a href="{{ route('public.monitor', array_merge(request()->except('page'), ['source_mode' => 'historical'])) }}"
           class="px-3 py-1.5 rounded-lg border transition shrink-0 inline-flex items-center gap-1.5 {{ request('source_mode') === 'historical' ? 'bg-[#173B70] text-white border-[#173B70] font-bold shadow-2xs' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200 font-medium' }}"
           title="Saring arsip historis resmi BNPB (DIBI)">
            <span>Historis BNPB</span>
            <span class="px-1.5 py-0.5 rounded text-[10px] {{ request('source_mode') === 'historical' ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-800 font-bold' }}">{{ $summary['historical_incidents'] ?? 0 }}</span>
        </a>

        <a href="{{ route('public.monitor', array_merge(request()->except('page'), ['source_mode' => 'simulation'])) }}"
           class="px-3 py-1.5 rounded-lg border transition shrink-0 inline-flex items-center gap-1.5 {{ request('source_mode') === 'simulation' ? 'bg-[#173B70] text-white border-[#173B70] font-bold shadow-2xs' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200 font-medium' }}"
           title="Saring skenario simulasi">
            <span>Simulasi</span>
            <span class="px-1.5 py-0.5 rounded text-[10px] {{ request('source_mode') === 'simulation' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700 font-bold' }}">{{ $summary['simulation_incidents'] ?? 0 }}</span>
        </a>

        <a href="{{ route('public.monitor', array_merge(request()->except('page'), ['verification_status' => 'terverifikasi'])) }}"
           class="px-3 py-1.5 rounded-lg border transition shrink-0 inline-flex items-center gap-1.5 {{ request('verification_status') === 'terverifikasi' ? 'bg-blue-700 text-white border-blue-700 font-bold shadow-2xs' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200 font-medium' }}"
           title="Saring kejadian terverifikasi">
            <span>Terverifikasi</span>
            <span class="px-1.5 py-0.5 rounded text-[10px] {{ request('verification_status') === 'terverifikasi' ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-700 font-bold' }}">{{ $summary['terverifikasi'] }}</span>
        </a>

        <a href="{{ route('public.monitor', array_merge(request()->except('page'), ['follow_up_status' => 'sedang_ditindaklanjuti'])) }}"
           class="px-3 py-1.5 rounded-lg border transition shrink-0 inline-flex items-center gap-1.5 {{ request('follow_up_status') === 'sedang_ditindaklanjuti' ? 'bg-emerald-700 text-white border-emerald-700 font-bold shadow-2xs' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200 font-medium' }}"
           title="Saring kejadian sedang ditindaklanjuti">
            <span>Ditindaklanjuti</span>
            <span class="px-1.5 py-0.5 rounded text-[10px] {{ request('follow_up_status') === 'sedang_ditindaklanjuti' ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-700 font-bold' }}">{{ $summary['sedang_ditindaklanjuti'] }}</span>
        </a>

        <a href="{{ route('public.monitor', array_merge(request()->except('page'), ['aid_status' => 'terkonfirmasi'])) }}"
           class="px-3 py-1.5 rounded-lg border transition shrink-0 inline-flex items-center gap-1.5 {{ request('aid_status') === 'terkonfirmasi' ? 'bg-emerald-700 text-white border-emerald-700 font-bold shadow-2xs' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200 font-medium' }}"
           title="Saring respons bantuan masyarakat">
            <span>Respons Bantuan</span>
            <span class="px-1.5 py-0.5 rounded text-[10px] {{ request('aid_status') === 'terkonfirmasi' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700 font-bold' }}">{{ $summary['total_aid_responses'] ?? 0 }}</span>
        </a>
    </div>

    <!-- Filter Bar Ringkas (Satu Baris Rapi & Efisien) -->
    <section aria-label="Filter Kejadian" class="bg-white rounded-xl shadow-xs p-3 sm:p-4 border border-slate-200/80">
        <form method="GET" action="{{ route('public.monitor') }}" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2.5 items-center">
                <!-- Search Input -->
                <div class="lg:col-span-4">
                    <label for="filter-q" class="sr-only">Cari Kejadian</label>
                    <div class="relative">
                        <input type="text"
                               id="filter-q"
                               name="q"
                               value="{{ $filters['q'] }}"
                               placeholder="Cari kejadian, sungai, jalan, wilayah..."
                               class="w-full min-h-[38px] pl-9 pr-3 py-1.5 bg-slate-50/60 border border-slate-200 rounded-lg text-xs text-[#202938] placeholder:text-slate-400 focus:outline-none focus:bg-white focus:border-[#173B70]">
                        <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Wilayah Filter -->
                <div class="lg:col-span-3">
                    <label for="filter-wilayah" class="sr-only">Wilayah</label>
                    <select id="filter-wilayah"
                            name="wilayah"
                            class="w-full min-h-[38px] px-3 py-1.5 bg-slate-50/60 border border-slate-200 rounded-lg text-xs text-[#202938] focus:outline-none focus:bg-white focus:border-[#173B70]">
                        <option value="">Semua Wilayah</option>
                        @foreach($availableLocations as $loc)
                            <option value="{{ $loc }}" @selected($filters['wilayah'] === $loc)>{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Jenis Kejadian Filter -->
                <div class="lg:col-span-3">
                    <label for="filter-type" class="sr-only">Jenis Kejadian</label>
                    <select id="filter-type"
                            name="incident_type"
                            class="w-full min-h-[38px] px-3 py-1.5 bg-slate-50/60 border border-slate-200 rounded-lg text-xs text-[#202938] focus:outline-none focus:bg-white focus:border-[#173B70]">
                        <option value="">Semua Jenis Kejadian</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" @selected($filters['incident_type'] === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="lg:col-span-2 flex items-center gap-1.5 justify-end">
                    @if(array_filter($filters))
                        <a href="{{ route('public.monitor') }}"
                           class="px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[38px] inline-flex items-center">
                            Reset
                        </a>
                    @endif
                    <button type="submit"
                            class="flex-1 lg:flex-none px-4 py-1.5 rounded-lg bg-[#173B70] hover:bg-[#0F2A52] text-white font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[38px] inline-flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span>Filter</span>
                    </button>
                </div>
            </div>

            <!-- Filter Tambahan Ringkas (Tersusun Sejajar & Tidak Menumpuk) -->
            <div class="pt-2 border-t border-slate-100 flex flex-wrap items-center gap-3 text-xs">
                <div class="flex items-center gap-1.5">
                    <span class="text-slate-400 font-telemetry text-[10px] uppercase font-bold">Verifikasi:</span>
                    <select name="verification_status" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded px-2 py-1 text-xs text-slate-700">
                        <option value="">Semua Status</option>
                        @foreach(\App\Enums\VerificationStatus::cases() as $vCase)
                            @if($vCase !== \App\Enums\VerificationStatus::Ditolak)
                                <option value="{{ $vCase->value }}" @selected($filters['verification_status'] === $vCase->value)>{{ $vCase->label() }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-1.5">
                    <span class="text-slate-400 font-telemetry text-[10px] uppercase font-bold">Tindak Lanjut:</span>
                    <select name="follow_up_status" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded px-2 py-1 text-xs text-slate-700">
                        <option value="">Semua Tindak Lanjut</option>
                        @foreach(\App\Enums\FollowUpStatus::cases() as $fCase)
                            <option value="{{ $fCase->value }}" @selected($filters['follow_up_status'] === $fCase->value)>{{ $fCase->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-1.5">
                    <span class="text-slate-400 font-telemetry text-[10px] uppercase font-bold">Sumber:</span>
                    <select name="source_mode" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded px-2 py-1 text-xs text-slate-700">
                        <option value="">Semua Sumber</option>
                        <option value="historical" @selected(($filters['source_mode'] ?? '') === 'historical')>Historis Resmi BNPB</option>
                        <option value="simulation" @selected(($filters['source_mode'] ?? '') === 'simulation')>Simulasi Pengujian</option>
                    </select>
                </div>
            </div>
        </form>
    </section>

    <!-- Komponen Kejadian Berdasarkan Wilayah (BMKG Nowcasting Alert Carousel) -->
    <x-regional-incidents-carousel :incidents="$regionalIncidents" />

    <!-- Main View: PETA SEBAGAI AREA TERBESAR + Panel Pendamping -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        <!-- Area Utama: Peta Interaktif Spasial (lg:col-span-8) -->
        <section class="lg:col-span-8 bg-white rounded-2xl shadow-xs overflow-hidden border border-slate-200/80">
            <!-- Header Toolbar Peta -->
            <div class="p-3 sm:p-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2 bg-[#F8FAFC]">
                <div class="flex items-center gap-2">
                    <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                        Peta Sebaran Bencana &amp; Respons
                    </h2>
                    <span class="px-2 py-0.5 rounded bg-blue-100 text-[#173B70] font-telemetry text-[10px] font-bold">
                        {{ $mapIncidents->count() }} Kejadian
                    </span>
                </div>

                <!-- Layer Filter Pills -->
                <div class="flex flex-wrap items-center gap-1" role="group" aria-label="Filter Layer Peta">
                    <button type="button" data-map-layer="all" class="map-layer-btn px-2.5 py-1 rounded bg-[#173B70] text-white font-telemetry text-[10px] font-bold uppercase cursor-pointer transition">
                        Semua Layer
                    </button>
                    <button type="button" data-map-layer="incidents" class="map-layer-btn px-2.5 py-1 rounded bg-white border border-slate-200 text-slate-700 font-telemetry text-[10px] font-bold uppercase cursor-pointer transition">
                        Kejadian ({{ $mapIncidents->count() }})
                    </button>
                    <button type="button" data-map-layer="needs" class="map-layer-btn px-2.5 py-1 rounded bg-white border border-slate-200 text-slate-700 font-telemetry text-[10px] font-bold uppercase cursor-pointer transition">
                        Kebutuhan ({{ ($mapReportNeeds ?? collect())->count() }})
                    </button>
                    <button type="button" data-map-layer="aid" class="map-layer-btn px-2.5 py-1 rounded bg-white border border-slate-200 text-slate-700 font-telemetry text-[10px] font-bold uppercase cursor-pointer transition">
                        Respons ({{ ($mapAidResponses ?? collect())->count() }})
                    </button>
                </div>
            </div>

            <!-- Leaflet Map Container: Prominent Height for Above-the-Fold Visibility -->
            <div id="monitor-map" class="w-full h-[420px] sm:h-[500px] lg:h-[560px] bg-slate-100 z-10"></div>

            <!-- Minimalist Map Legend -->
            <div class="px-4 py-2.5 border-t border-slate-100 bg-[#F8FAFC] flex flex-wrap items-center justify-between gap-x-4 gap-y-1.5 text-[11px] font-telemetry text-slate-600">
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#D94350] inline-block"></span>
                        <span>Kejadian Kritis</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#D97706] inline-block"></span>
                        <span>Kejadian Tinggi</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-[#173B70] ring-2 ring-blue-200 inline-block"></span>
                        <span>Laporan Kebutuhan</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-xs bg-[#059669] inline-block"></span>
                        <span>Respons Bantuan</span>
                    </span>
                </div>
                <span class="text-[10px] text-slate-400">Titik memiliki koordinat valid pada sistem.</span>
            </div>
        </section>

        <!-- Panel Pendamping: Daftar Kejadian Terbaru (lg:col-span-4) -->
        <section class="lg:col-span-4 flex flex-col space-y-3" aria-label="Daftar Kejadian">
            <div class="bg-white rounded-xl border border-slate-200/80 px-3.5 py-2.5 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                        Daftar Kejadian
                    </h2>
                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-telemetry text-[11px] font-bold">
                        {{ $incidents->total() }}
                    </span>
                </div>
                <span class="text-[10px] text-slate-400 font-telemetry">Waktu Terbaru</span>
            </div>

            @if($incidents->isEmpty())
                <div class="bg-white rounded-2xl shadow-xs p-6 text-center space-y-2.5 border border-slate-200/80">
                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-[#202938]">Tidak Ada Kejadian yang Sesuai Filter</h3>
                    <p class="text-xs text-slate-500">
                        Tidak ada data kejadian yang cocok dengan kriteria filter saat ini.
                    </p>
                    <div class="pt-1">
                        <a href="{{ route('public.monitor') }}"
                           class="px-3 py-1.5 rounded-lg bg-[#173B70] hover:bg-[#0F2A52] text-white font-telemetry text-xs font-bold uppercase tracking-wider inline-flex items-center transition shadow-2xs">
                            Reset Filter
                        </a>
                    </div>
                </div>
            @else
                <!-- Scrollable Container on Desktop aligned with Map -->
                <div class="flex overflow-x-auto snap-x snap-mandatory gap-3 pb-2 -mx-4 px-4 sm:mx-0 sm:px-0 sm:flex-col sm:space-y-3 sm:gap-0 sm:overflow-visible lg:max-h-[580px] lg:overflow-y-auto pr-0.5 scrollbar-thin"
                     role="region" aria-label="Daftar Kartu Kejadian">
                    @foreach($incidents as $incident)
                        <div class="w-[84vw] max-w-[340px] shrink-0 snap-start sm:w-full sm:max-w-none flex flex-col">
                            <x-incident-card :incident="$incident" />
                        </div>
                    @endforeach
                </div>

                @if($incidents->hasPages())
                    <div class="bg-white rounded-xl shadow-xs p-3 border border-slate-200/80">
                        {{ $incidents->links() }}
                    </div>
                @endif
            @endif
        </section>
    </div>

    <!-- Transparansi Kebutuhan & Respons Bantuan (Clean Editorial Presentation) -->
    <section aria-label="Transparansi Kebutuhan dan Respons Bantuan" class="bg-white rounded-2xl shadow-xs p-5 sm:p-6 space-y-5 border border-slate-200/80">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-telemetry text-[10px] font-bold uppercase">
                        4 Kategori Utama
                    </span>
                    <span class="text-xs text-slate-400">•</span>
                    <span class="font-telemetry text-xs text-slate-500">
                        Pencatatan Berbasis Laporan Warga
                    </span>
                </div>
                <h2 class="text-lg sm:text-xl font-bold text-[#202938] tracking-tight">
                    Kebutuhan Mendesak &amp; Penyaluran Bantuan
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 max-w-2xl leading-relaxed">
                    Pemantauan kebutuhan mendesak dan respons bantuan terkonfirmasi pada kategori: Kesehatan, Makan &amp; Minum, Bansos, dan Evakuasi.
                </p>
            </div>

            <div class="shrink-0 flex flex-wrap items-center gap-2">
                <a href="{{ route('public.aid-responses.create', ['submission_type' => 'tawaran_rencana']) }}"
                   class="px-3 py-1.5 rounded-lg btn-brand-gradient text-white font-telemetry text-xs font-semibold tracking-wide min-h-[36px] inline-flex items-center gap-1 shadow-xs transition">
                    <span class="font-bold text-sm leading-none">+</span>
                    <span>Ajukan Bantuan</span>
                </a>
                <a href="{{ route('public.aid-responses.create', ['submission_type' => 'laporan_penyerahan']) }}"
                   class="px-3 py-1.5 rounded-lg bg-[#173B70] hover:bg-[#0F2A52] text-white font-telemetry text-xs font-semibold tracking-wide min-h-[36px] inline-flex items-center gap-1 shadow-xs transition">
                    <span class="font-bold text-sm leading-none">+</span>
                    <span>Konfirmasi Penyerahan</span>
                </a>
            </div>
        </div>

        @php
            $statsCollection = collect($categoryStats ?? []);
            $maxNeedsCount = max((int) $statsCollection->max('needs_count'), 1);
            $maxResponsesCount = max((int) $statsCollection->max('responses_total'), 1);
        @endphp

        <!-- 3-Column Summary Strip -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- 1. Kebutuhan Warga -->
            <div class="bg-slate-50/70 rounded-xl p-4 space-y-3 border border-slate-100">
                <div class="border-b border-slate-200/80 pb-2">
                    <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-900 font-telemetry text-[10px] font-bold uppercase">
                        1. Kebutuhan Warga
                    </span>
                    <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mt-1">
                        Kebutuhan Tercatat per Kategori
                    </h3>
                </div>

                <div class="space-y-2.5">
                    @foreach($statsCollection as $catStat)
                        @php
                            $needBarWidth = $catStat['needs_count'] > 0
                                ? max(10, (int) round(($catStat['needs_count'] / $maxNeedsCount) * 100))
                                : 0;
                        @endphp
                        <div class="space-y-1 text-[11px] font-telemetry">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-[#202938]">{{ $catStat['label'] }}</span>
                                <span class="font-semibold text-slate-700">{{ $catStat['needs_count'] }} laporan</span>
                            </div>
                            <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                                <div class="h-full bg-[#173B70] rounded-full" style="width: {{ $needBarWidth }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 2. Respons Bantuan Tercatat -->
            <div class="bg-slate-50/70 rounded-xl p-4 space-y-3 border border-slate-100">
                <div class="border-b border-slate-200/80 pb-2">
                    <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-900 font-telemetry text-[10px] font-bold uppercase">
                        2. Respons Bantuan
                    </span>
                    <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mt-1">
                        Respons Bantuan Tercatat per Kategori
                    </h3>
                </div>

                <div class="space-y-2.5">
                    @foreach($statsCollection as $catStat)
                        @php
                            $respBarWidth = $catStat['responses_total'] > 0
                                ? max(10, (int) round(($catStat['responses_total'] / $maxResponsesCount) * 100))
                                : 0;
                        @endphp
                        <div class="space-y-1 text-[11px] font-telemetry">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-[#202938]">{{ $catStat['label'] }}</span>
                                <span class="font-semibold text-slate-700">{{ $catStat['responses_total'] }} entri</span>
                            </div>
                            <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-600 rounded-full" style="width: {{ $respBarWidth }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 3. Status Distribusi Respons -->
            <div class="bg-slate-50/70 rounded-xl p-4 space-y-3 border border-slate-100">
                <div class="border-b border-slate-200/80 pb-2">
                    <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-900 font-telemetry text-[10px] font-bold uppercase">
                        3. Proporsi Status
                    </span>
                    <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mt-1">
                        Distribusi Status Respons Bantuan
                    </h3>
                </div>

                <div class="space-y-2.5">
                    @foreach($statsCollection as $catStat)
                        <div class="space-y-1 text-[11px] font-telemetry">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-[#202938]">{{ $catStat['label'] }}</span>
                                <span class="text-[10px] text-slate-500">{{ $catStat['factual_status'] }}</span>
                            </div>
                            @if($catStat['responses_total'] > 0)
                                <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden flex">
                                    @if($catStat['direncanakan_count'] > 0)
                                        <div class="h-full bg-amber-500" style="width: {{ $catStat['pct_direncanakan'] }}%" title="Direncanakan: {{ $catStat['direncanakan_count'] }}"></div>
                                    @endif
                                    @if($catStat['dalam_penyaluran_count'] > 0)
                                        <div class="h-full bg-[#173B70]" style="width: {{ $catStat['pct_dalam_penyaluran'] }}%" title="Penyaluran: {{ $catStat['dalam_penyaluran_count'] }}"></div>
                                    @endif
                                    @if($catStat['terkonfirmasi_count'] > 0)
                                        <div class="h-full bg-[#059669]" style="width: {{ $catStat['pct_terkonfirmasi'] }}%" title="Dikonfirmasi: {{ $catStat['terkonfirmasi_count'] }}"></div>
                                    @endif
                                </div>
                            @else
                                <div class="w-full h-2 bg-slate-200 rounded-full"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Tabel Respons Bantuan Terkini -->
        <div class="space-y-3 pt-2">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                    Daftar Respons Bantuan Tercatat ({{ ($publicAidResponses ?? collect())->count() }})
                </h3>
                <span class="font-telemetry text-[11px] text-slate-400">
                    Kontak pelapor dilindungi privasi
                </span>
            </div>

            @if(($publicAidResponses ?? collect())->isEmpty())
                <div class="bg-[#F8F9FC] border border-slate-200/80 rounded-xl p-6 text-center text-xs text-slate-500">
                    Belum ada respons bantuan yang tercatat pada sistem.
                </div>
            @else
                <div class="overflow-x-auto border border-slate-200/80 rounded-xl">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#F8F9FC] border-b border-slate-200/80 font-telemetry text-[11px] font-bold uppercase text-slate-500">
                                <th class="py-2.5 px-3">Kode &amp; Waktu</th>
                                <th class="py-2.5 px-3">Tujuan &amp; Kejadian</th>
                                <th class="py-2.5 px-3">Bantuan &amp; Kategori</th>
                                <th class="py-2.5 px-3">Pemberi Respons</th>
                                <th class="py-2.5 px-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @foreach($publicAidResponses as $aid)
                                @php
                                    $aidStatusVal = $aid->status instanceof \BackedEnum ? $aid->status->value : (string) $aid->status;
                                    $isDeliveredSubmission = $aid->isReportedDelivered();
                                @endphp
                                <tr class="hover:bg-slate-50/80 align-top">
                                    <td class="py-2.5 px-3 whitespace-nowrap space-y-1">
                                        <span class="font-telemetry font-bold text-[#202938] block">{{ $aid->reference_code }}</span>
                                        <span class="px-1.5 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase inline-block {{ $isDeliveredSubmission ? 'bg-indigo-50 text-indigo-900 border border-indigo-200' : 'bg-blue-50 text-[#173B70] border border-blue-200' }}">
                                            {{ $aid->submissionTypeLabel() }}
                                        </span>
                                        <div class="font-telemetry text-[10px] text-slate-500">
                                            {{ $aid->recorded_at?->format('d M Y, H:i') }} WIB
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3">
                                        @if($aid->incident)
                                            <a href="{{ route('public.incidents.show', $aid->incident->slug) }}" class="font-bold text-[#173B70] hover:underline block">
                                                {{ $aid->incident->title }}
                                            </a>
                                        @endif
                                        <span class="text-slate-600 block mt-0.5 text-[11px]">{{ $aid->target_location }}</span>
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <strong class="text-[#202938] block">{{ $aid->aid_item_title }}</strong>
                                        <span class="font-telemetry text-[11px] text-slate-500">{{ $aid->quantity_label }}</span>
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <strong class="text-[#202938] block">{{ $aid->publicResponderName() }}</strong>
                                        <span class="font-telemetry text-[10px] text-slate-500 block">
                                            {{ $responderTypes[$aid->responder_type] ?? $aid->responder_type }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 space-y-1">
                                        <div class="flex items-center gap-1">
                                            <x-verification-badge :status="$aid->verification_status" />
                                            <x-aid-status-badge :status="$aid->status" />
                                        </div>
                                        <div>
                                            <a href="{{ route('public.track', ['ticket' => $aid->reference_code]) }}"
                                               class="inline-flex items-center gap-1 font-telemetry text-[11px] font-bold text-[#173B70] hover:underline">
                                                <span>Lacak Tiket</span>
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
        </div>
    </section>

    <!-- Diagram Alur Transparansi Informasi & Klasifikasi Sumber Data -->
    <x-transparency-flow-diagram compact="true" />
</div>
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
        const incidentMarkersMap = new Map();

        markersData.forEach((item) => {
            if (typeof item.latitude !== 'number' || typeof item.longitude !== 'number') {
                return;
            }

            const isHistorical = !item.is_simulation;
            const sourceBadge = isHistorical
                ? '<span style="background: #173B70; color: #FFFFFF; padding: 2px 6px; border-radius: 4px; font-family: \'Poppins\', sans-serif; font-size: 9px; font-weight: 700; text-transform: uppercase;">HISTORIS BNPB</span>'
                : '<span style="background: #315D91; color: #FFFFFF; padding: 2px 6px; border-radius: 4px; font-family: \'Poppins\', sans-serif; font-size: 9px; font-weight: 700; text-transform: uppercase;">SIMULASI</span>';

            const sev = (item.severity || '').toLowerCase();
            const color = severityColorMap[sev] || '#173B70';

            const marker = window.L.circleMarker([item.latitude, item.longitude], {
                radius: isHistorical ? 10 : 9,
                fillColor: color,
                color: isHistorical ? '#173B70' : '#FFFFFF',
                weight: isHistorical ? 3 : 2.5,
                opacity: 1,
                fillOpacity: 0.92,
            }).addTo(incidentsLayer);

            incidentMarkersMap.set(String(item.id), marker);

            const reportMetaHtml = isHistorical
                ? '<div><strong>Sumber Resmi:</strong> Arsip BNPB (DIBI)</div><div><strong>Keterangan:</strong> Riwayat Bencana Nyata</div>'
                : `<div><strong>Verifikasi:</strong> ${item.verification_label}</div><div><strong>Tindak Lanjut:</strong> ${item.follow_up_label}</div><div><strong>Laporan:</strong> ${item.reports_count} | <strong>Respons:</strong> ${item.aid_responses_count || 0}</div>`;

            const popupHtml = `
                <div style="min-width: 230px; font-family: 'Poppins', sans-serif; color: #202938;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; margin-bottom: 4px;">
                        <span style="font-family: 'Poppins', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #788397;">
                            ${item.incident_type}
                        </span>
                        ${sourceBadge}
                    </div>
                    <div style="font-size: 14px; font-weight: 700; line-height: 1.3; margin-bottom: 4px; color: #202938;">
                        ${item.title}
                    </div>
                    <div style="font-size: 12px; color: #788397; margin-bottom: 8px;">
                        ${item.location_name}${item.wilayah_nama ? ' • ' + item.wilayah_nama : ''}
                    </div>
                    <div style="font-family: 'Poppins', sans-serif; font-size: 11px; background: #F8F9FC; border: 1px solid #E2E8F0; border-radius: 6px; padding: 6px; margin-bottom: 8px;">
                        ${reportMetaHtml}
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 4px;">
                        <button type="button" class="btn-popup-sidepanel" data-incident-id="${item.id}" style="display: block; width: 100%; text-align: center; background: #F8F9FC; color: #202938; border: 1px solid #CBD5E1; padding: 6px 10px; border-radius: 6px; font-family: 'Poppins', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; cursor: pointer;">
                            Buka Panel Cepat &rarr;
                        </button>
                        <a href="${item.url}" style="display: inline-block; width: 100%; text-align: center; background: #173B70; color: #FFFFFF; text-decoration: none; padding: 6px 10px; border-radius: 6px; font-family: 'Poppins', sans-serif; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                            Lihat Detail Kejadian &rarr;
                        </a>
                    </div>
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
                fillColor: '#173B70',
                color: '#DBEAFE',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.9,
            }).addTo(needsLayer);

            const popupHtml = `
                <div style="min-width: 200px; font-family: 'Poppins', sans-serif; color: #202938;">
                    <div style="font-family: 'Poppins', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #173B70; margin-bottom: 4px;">
                        [KEBUTUHAN DILAPORKAN] • ${need.ticket_number}
                    </div>
                    <div style="font-size: 13px; font-weight: 700; line-height: 1.3; margin-bottom: 4px; color: #202938;">
                        ${need.title}
                    </div>
                    <div style="font-size: 11px; color: #788397; margin-bottom: 6px;">
                        Kategori: <strong>${need.need_category_label}</strong><br>
                        Lokasi: ${need.location_name}<br>
                        Status: ${need.verification_label}
                    </div>
                    <a href="${need.track_url}" style="display: inline-block; width: 100%; text-align: center; background: #173B70; color: #FFFFFF; text-decoration: none; padding: 5px 8px; border-radius: 6px; font-family: 'Poppins', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase;">
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
                <div style="min-width: 210px; font-family: 'Poppins', sans-serif; color: #202938;">
                    <div style="font-family: 'Poppins', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #059669; margin-bottom: 4px;">
                        [RESPONS BANTUAN] • ${aid.reference_code}
                    </div>
                    <div style="font-size: 13px; font-weight: 700; line-height: 1.3; margin-bottom: 4px; color: #202938;">
                        ${aid.aid_item_title}
                    </div>
                    <div style="font-size: 11px; color: #788397; margin-bottom: 6px;">
                        Kapasitas: <strong>${aid.quantity_label}</strong><br>
                        Pemberi Respons: <strong>${aid.responder_name}</strong><br>
                        Tujuan: ${aid.target_location}<br>
                        Status: <strong>${aid.status_label}</strong>
                    </div>
                    <a href="${aid.incident_url}" style="display: inline-block; width: 100%; text-align: center; background: #059669; color: #FFFFFF; text-decoration: none; padding: 5px 8px; border-radius: 6px; font-family: 'Poppins', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase;">
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

        // Inisialisasi Cross-Highlighting antara Card & Marker Peta
        const incidentCards = Array.from(document.querySelectorAll('.incident-card[data-incident-id]'));
        if (window.MapCrossHighlight) {
            window.MapCrossHighlight.init({
                map,
                incidentMarkers: incidentMarkersMap,
                incidentCards,
            });
        }

        map.on('popupopen', (e) => {
            const btn = e.popup.getElement()?.querySelector('.btn-popup-sidepanel');
            if (btn) {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-incident-id');
                    const card = incidentCards.find(c => c.getAttribute('data-incident-id') === id);
                    const marker = incidentMarkersMap.get(id);
                    if (card && window.MapCrossHighlight) {
                        window.MapCrossHighlight.openIncidentSidepanel(card, marker);
                    }
                });
            }
        });

        // Layer Filter Buttons
        const layerButtons = document.querySelectorAll('.map-layer-btn');
        layerButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                const mode = btn.getAttribute('data-map-layer');
                layerButtons.forEach((b) => {
                    b.className = 'map-layer-btn px-2.5 py-1 rounded bg-white border border-slate-200 text-slate-700 font-telemetry text-[10px] font-bold uppercase cursor-pointer transition';
                });
                btn.className = 'map-layer-btn px-2.5 py-1 rounded bg-[#173B70] text-white font-telemetry text-[10px] font-bold uppercase cursor-pointer transition';

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
