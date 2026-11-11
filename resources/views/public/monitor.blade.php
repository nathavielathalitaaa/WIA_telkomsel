@extends('layouts.app', [
    'title' => 'Pantau Sebaran Kejadian — TanggapTerang',
    'activeTab' => 'pantau',
])

@section('content')
    <!-- Master Institutional Hero Header Card (Aligned with /welcome) -->
    <section class="bg-[#173B70] text-white border border-[#315D91] rounded-2xl p-4 sm:p-6 space-y-4 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div class="space-y-2 max-w-3xl">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-1 rounded bg-[#315D91] text-white border border-blue-300/30 font-telemetry text-xs font-bold uppercase tracking-wider">
                        Papan Pemantauan Bencana
                    </span>
                    <span class="px-2.5 py-1 rounded bg-amber-400 text-[#173B70] font-telemetry text-xs font-bold uppercase tracking-wider">
                        Data Terbuka &amp; Terverifikasi
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold tracking-tight text-white leading-tight">
                    Peta Sebaran &amp; Perkembangan Respons
                </h1>
                <p class="text-xs sm:text-sm text-blue-100 leading-relaxed">
                    Pantau sebaran kejadian bencana, laporan kebutuhan warga, dan dokumentasi tindak lanjut di lapangan secara terpadu dan terlacak.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0 pt-1 lg:pt-0">
                <a href="{{ route('public.track') }}"
                   class="px-4 py-2 rounded-lg border border-blue-300/40 hover:bg-[#315D91] text-white font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[40px] inline-flex items-center gap-2 shadow-xs">
                    <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Lacak Tiket</span>
                </a>
                <a href="{{ route('public.aid-responses.create') }}"
                   class="px-4 py-2 rounded-lg border border-blue-300/40 hover:bg-[#315D91] text-white font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[40px] inline-flex items-center gap-1.5 shadow-xs">
                    <span class="font-extrabold text-sm leading-none">+</span>
                    <span>Catat Bantuan</span>
                </a>
                <a href="{{ route('public.reports.create') }}"
                   class="px-4 py-2 rounded-lg btn-brand-gradient text-white font-telemetry text-xs font-bold uppercase tracking-wider min-h-[40px] inline-flex items-center gap-1.5 shadow-xs">
                    <span class="font-extrabold text-sm leading-none">+</span>
                    <span>Kirim Laporan Warga</span>
                </a>
            </div>
        </div>

        <!-- Integrated Data Source Transparency Bar -->
        <div class="pt-3 border-t border-blue-400/20 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
            <div class="flex items-center gap-2 text-blue-100">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                <span><strong>Sumber Data:</strong> Arsip resmi <strong>Pusdatin BNPB (DIBI 2022–2024)</strong> &amp; skenario simulasi terpisah.</span>
            </div>
            <div class="flex items-center gap-2 font-telemetry text-xs shrink-0">
                <a href="{{ route('public.monitor', array_merge(request()->except('page'), ['source_mode' => 'historical'])) }}"
                   class="px-3 py-1.5 rounded-lg border {{ request('source_mode') === 'historical' ? 'bg-white text-[#173B70] border-white font-extrabold shadow-xs' : 'bg-[#0F2A52] hover:bg-[#315D91] text-blue-100 border-blue-400/30 font-bold' }} transition">
                    {{ $summary['historical_incidents'] ?? 0 }} Data Historis
                </a>
                <a href="{{ route('public.monitor', array_merge(request()->except('page'), ['source_mode' => 'simulation'])) }}"
                   class="px-3 py-1.5 rounded-lg border {{ request('source_mode') === 'simulation' ? 'bg-white text-[#173B70] border-white font-extrabold shadow-xs' : 'bg-[#0F2A52] hover:bg-[#315D91] text-blue-100 border-blue-400/30 font-bold' }} transition">
                    {{ $summary['simulation_incidents'] ?? 0 }} Simulasi
                </a>
            </div>
        </div>
    </section>

    <!-- Summary Metrics Bar (Mobile Horizontal Snap Carousel, Desktop/Tablet Grid) -->
    <section aria-label="Ringkasan Data Kejadian" class="flex overflow-x-auto snap-x snap-mandatory gap-3 pb-2 -mx-4 px-4 sm:mx-0 sm:px-0 scrollbar-none sm:grid sm:grid-cols-3 lg:grid-cols-6">
        <a href="{{ route('public.monitor') }}"
           class="w-[155px] sm:w-auto shrink-0 snap-start bg-white border border-slate-100 hover:border-slate-300 hover:shadow-xs rounded-xl p-3.5 flex flex-col justify-between transition shadow-xs {{ empty(request()->except('page')) ? 'ring-2 ring-slate-900 bg-slate-50' : '' }}"
           title="Tampilkan seluruh kejadian tanpa filter">
            <span class="font-telemetry text-[11px] font-semibold text-slate-500">Total Kejadian</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="font-telemetry text-2xl font-bold text-[#202938]">{{ $summary['total_incidents'] }}</span>
                <span class="font-telemetry text-[10px] text-slate-400 font-medium">{{ $summary['historical_incidents'] ?? 0 }} Riil • {{ $summary['simulation_incidents'] ?? 0 }} Sim</span>
            </div>
        </a>

        <a href="{{ route('public.monitor', array_merge(request()->except('page'), ['source_mode' => 'historical'])) }}"
           class="w-[155px] sm:w-auto shrink-0 snap-start bg-white border border-slate-100 hover:border-slate-300 hover:shadow-xs rounded-xl p-3.5 flex flex-col justify-between transition shadow-xs {{ request('source_mode') === 'historical' ? 'ring-2 ring-blue-700 bg-blue-50/40 border-blue-300' : '' }}"
           title="Saring kejadian riil dari Arsip BNPB (DIBI)">
            <span class="font-telemetry text-[11px] font-semibold text-slate-600">Historis BNPB</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="font-telemetry text-2xl font-bold text-blue-900">{{ $summary['historical_incidents'] ?? 0 }}</span>
                <span class="font-telemetry text-[10px] text-blue-700 font-medium">Resmi DIBI</span>
            </div>
        </a>

        <a href="{{ route('public.monitor', array_merge(request()->except('page'), ['source_mode' => 'simulation'])) }}"
           class="w-[155px] sm:w-auto shrink-0 snap-start bg-white border border-slate-100 hover:border-slate-300 hover:shadow-xs rounded-xl p-3.5 flex flex-col justify-between transition shadow-xs {{ request('source_mode') === 'simulation' ? 'ring-2 ring-slate-800 bg-slate-50 border-slate-400' : '' }}"
           title="Saring skenario simulasi alur">
            <span class="font-telemetry text-[11px] font-semibold text-slate-600">Simulasi Alur</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="font-telemetry text-2xl font-bold text-slate-800">{{ $summary['simulation_incidents'] ?? 0 }}</span>
                <span class="font-telemetry text-[10px] text-slate-500 font-medium">Pengujian</span>
            </div>
        </a>

        <a href="{{ route('public.monitor', array_merge(request()->except('page'), ['verification_status' => 'terverifikasi'])) }}"
           class="w-[155px] sm:w-auto shrink-0 snap-start bg-white border border-slate-100 hover:border-slate-300 hover:shadow-xs rounded-xl p-3.5 flex flex-col justify-between transition shadow-xs {{ request('verification_status') === 'terverifikasi' ? 'ring-2 ring-blue-600 bg-blue-50/30 border-blue-300' : '' }}"
           title="Saring kejadian terverifikasi">
            <span class="font-telemetry text-[11px] font-semibold text-slate-600">Terverifikasi</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="font-telemetry text-2xl font-bold text-blue-700">{{ $summary['terverifikasi'] }}</span>
                <span class="font-telemetry text-[10px] text-blue-600 font-medium">Valid</span>
            </div>
        </a>

        <a href="{{ route('public.monitor', array_merge(request()->except('page'), ['follow_up_status' => 'sedang_ditindaklanjuti'])) }}"
           class="w-[155px] sm:w-auto shrink-0 snap-start bg-white border border-slate-100 hover:border-slate-300 hover:shadow-xs rounded-xl p-3.5 flex flex-col justify-between transition shadow-xs {{ request('follow_up_status') === 'sedang_ditindaklanjuti' ? 'ring-2 ring-emerald-600 bg-emerald-50/30 border-emerald-300' : '' }}"
           title="Saring kejadian sedang ditindaklanjuti">
            <span class="font-telemetry text-[11px] font-semibold text-slate-600">Ditindaklanjuti</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="font-telemetry text-2xl font-bold text-emerald-700">{{ $summary['sedang_ditindaklanjuti'] }}</span>
                <span class="font-telemetry text-[10px] text-emerald-600 font-medium">Aktif</span>
            </div>
        </a>

        <a href="{{ route('public.monitor', array_merge(request()->except('page'), ['aid_status' => 'terkonfirmasi'])) }}"
           class="w-[155px] sm:w-auto shrink-0 snap-start bg-white border border-slate-100 hover:border-slate-300 hover:shadow-xs rounded-xl p-3.5 flex flex-col justify-between transition shadow-xs {{ request('aid_status') === 'terkonfirmasi' ? 'ring-2 ring-emerald-600 bg-emerald-50/30 border-emerald-300' : '' }}"
           title="Saring kejadian dengan respons bantuan terkonfirmasi">
            <span class="font-telemetry text-[11px] font-semibold text-slate-600">Respons Bantuan</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="font-telemetry text-2xl font-bold text-[#202938]">{{ $summary['total_aid_responses'] ?? 0 }}</span>
                <span class="font-telemetry text-[10px] text-emerald-700 font-medium">{{ $summary['confirmed_aid_responses'] ?? 0 }} Sah</span>
            </div>
        </a>
    </section>

    <!-- Search & Filter Bar (Borderless Treatment with Subtle Shadow) -->
    <section aria-label="Filter dan Pencarian Kejadian" class="bg-white rounded-2xl shadow-xs p-4 sm:p-5 border border-slate-100/90">
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
                           class="w-full min-h-[42px] px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-sm text-[#202938] placeholder:text-slate-400 focus:outline-none focus:border-[#173B70]">
                </div>

                <!-- Wilayah / Lokasi Filter -->
                <div class="md:col-span-3">
                    <label for="filter-wilayah" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Wilayah / Kawasan
                    </label>
                    <select id="filter-wilayah"
                            name="wilayah"
                            class="w-full min-h-[42px] px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-sm text-[#202938] focus:outline-none focus:border-[#173B70]">
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
                            class="w-full min-h-[42px] px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-sm text-[#202938] focus:outline-none focus:border-[#173B70]">
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
                            class="w-full min-h-[42px] px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-sm text-[#202938] focus:outline-none focus:border-[#173B70]">
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
                            class="w-full min-h-[42px] px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-sm text-[#202938] focus:outline-none focus:border-[#173B70]">
                        <option value="">Semua Jenis Kejadian</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" @selected($filters['incident_type'] === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Severity Filter -->
                <div class="sm:col-span-2">
                    <label for="filter-severity" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Dampak
                    </label>
                    <select id="filter-severity"
                            name="severity"
                            class="w-full min-h-[42px] px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-sm text-[#202938] focus:outline-none focus:border-[#173B70]">
                        <option value="">Semua Dampak</option>
                        @foreach(\App\Enums\IncidentSeverity::cases() as $sCase)
                            <option value="{{ $sCase->value }}" @selected($filters['severity'] === $sCase->value)>
                                {{ $sCase->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Aid Response Status Filter -->
                <div class="sm:col-span-2">
                    <label for="filter-aid-status" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Respons Bantuan
                    </label>
                    <select id="filter-aid-status"
                            name="aid_status"
                            class="w-full min-h-[42px] px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-sm text-[#202938] focus:outline-none focus:border-[#173B70]">
                        <option value="">Semua Respons</option>
                        @foreach(\App\Enums\AidResponseStatus::cases() as $aCase)
                            <option value="{{ $aCase->value }}" @selected(($filters['aid_status'] ?? '') === $aCase->value)>
                                {{ $aCase->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Data Source / Simulation Filter -->
                <div class="sm:col-span-3">
                    <label for="filter-source-mode" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Cakupan Data
                    </label>
                    <select id="filter-source-mode"
                            name="source_mode"
                            class="w-full min-h-[42px] px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-sm text-[#202938] focus:outline-none focus:border-[#173B70]">
                        <option value="">Semua Data (Historis &amp; Simulasi)</option>
                        <option value="historical" @selected(($filters['source_mode'] ?? '') === 'historical')>Data Historis Resmi (BNPB / DIBI)</option>
                        <option value="simulation" @selected(($filters['source_mode'] ?? '') === 'simulation')>Skenario Simulasi Prototipe</option>
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="sm:col-span-2 flex items-center justify-end gap-2">
                    @if(array_filter($filters))
                        <a href="{{ route('public.monitor') }}"
                           class="px-2.5 py-2.5 rounded-lg border border-slate-200/90 bg-white hover:bg-slate-50 text-slate-700 font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[42px] inline-flex items-center">
                            Reset
                        </a>
                    @endif
                    <button type="submit"
                            class="w-full px-3 py-2.5 rounded-lg bg-[#173B70] hover:bg-[#0F2A52] text-white font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[42px] inline-flex items-center justify-center gap-1.5 cursor-pointer shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span>Filter</span>
                    </button>
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
        <section class="lg:col-span-5 lg:sticky lg:top-20 bg-white rounded-2xl shadow-xs overflow-hidden border border-slate-100/90">
            <div class="p-3.5 border-b border-slate-100 space-y-2 bg-[#F8FAFC]">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                            Peta Kejadian, Kebutuhan, &amp; Respons Bantuan
                        </h2>
                        <p class="text-[11px] text-[#788397] mt-0.5">
                            {{ $mapIncidents->count() }} kejadian • {{ ($mapReportNeeds ?? collect())->count() }} laporan kebutuhan • {{ ($mapAidResponses ?? collect())->count() }} respons bantuan
                        </p>
                    </div>
                    <span class="px-2 py-0.5 rounded bg-slate-200 text-[#202938] font-telemetry text-[10px] font-bold uppercase shrink-0">
                        Simulasi GIS
                    </span>
                </div>

                <!-- Map Layer Filter Pills -->
                <div class="flex flex-wrap items-center gap-1.5 pt-1" role="group" aria-label="Filter Layer Peta">
                    <button type="button" data-map-layer="all" class="map-layer-btn px-2 py-1 rounded bg-[#173B70] text-white font-telemetry text-[10px] font-bold uppercase cursor-pointer">
                        Semua Layer
                    </button>
                    <button type="button" data-map-layer="incidents" class="map-layer-btn px-2 py-1 rounded bg-white border border-slate-200 text-slate-700 font-telemetry text-[10px] font-bold uppercase cursor-pointer">
                        Kejadian ({{ $mapIncidents->count() }})
                    </button>
                    <button type="button" data-map-layer="needs" class="map-layer-btn px-2 py-1 rounded bg-white border border-slate-200 text-slate-700 font-telemetry text-[10px] font-bold uppercase cursor-pointer">
                        Kebutuhan ({{ ($mapReportNeeds ?? collect())->count() }})
                    </button>
                    <button type="button" data-map-layer="aid" class="map-layer-btn px-2 py-1 rounded bg-white border border-slate-200 text-slate-700 font-telemetry text-[10px] font-bold uppercase cursor-pointer">
                        Respons Bantuan ({{ ($mapAidResponses ?? collect())->count() }})
                    </button>
                </div>
            </div>

            <div id="monitor-map" class="w-full h-[360px] sm:h-[440px] bg-slate-100 z-10"></div>

            <!-- Map Legend -->
            <div class="p-3 border-t border-slate-100 bg-white space-y-2">
                <div class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-[#788397]">
                    Legenda Peta Kejadian &amp; Respons Bantuan
                </div>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5 text-xs font-telemetry">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-[#D94350] inline-block"></span>
                        <span>Kejadian Kritis</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-[#D97706] inline-block"></span>
                        <span>Kejadian Tinggi</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#173B70] ring-2 ring-blue-200 inline-block"></span>
                        <span>Laporan Kebutuhan</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-xs bg-[#059669] border border-white inline-block"></span>
                        <span>Respons Bantuan Tercatat</span>
                    </span>
                </div>
                <p class="text-[11px] text-[#788397] leading-snug">
                    Catatan: Data hanya menampilkan titik yang memiliki koordinat valid pada sistem dan tidak merepresentasikan seluruh kondisi di lapangan.
                </p>
            </div>
        </section>

        <!-- Right/Bottom Column: Latest Incidents Feed -->
        <section class="lg:col-span-7 space-y-4" aria-label="Daftar Kejadian Terbaru">
            <div class="bg-white rounded-xl border border-slate-100/90 shadow-2xs px-4 py-3 flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                        Daftar Kejadian Terbaru
                    </h2>
                    <span class="px-2 py-0.5 rounded bg-slate-100 text-[#202938] font-telemetry text-xs font-bold">
                        {{ $incidents->total() }} Kejadian
                    </span>
                </div>
                <span class="font-telemetry text-[11px] text-[#788397]">
                    Diurutkan dari waktu kejadian terbaru
                </span>
            </div>

            @if($incidents->isEmpty())
                <div class="bg-white rounded-2xl shadow-xs p-8 text-center space-y-3 border border-slate-100/90">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-[#202938]">Tidak Ada Kejadian yang Sesuai Filter</h3>
                    <p class="text-sm text-[#788397] max-w-md mx-auto">
                        Belum ada data kejadian yang cocok dengan kata kunci atau kriteria filter yang Anda pilih.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('public.monitor') }}"
                           class="px-4 py-2 rounded-lg bg-[#173B70] hover:bg-[#0F2A52] text-white font-telemetry text-xs font-bold uppercase tracking-wider inline-flex items-center transition shadow-xs">
                            Tampilkan Semua Kejadian
                        </a>
                    </div>
                </div>
            @else
                <!-- Responsive Card Layout: Mobile Horizontal Carousel with Edge Peek, Desktop Stack/List -->
                <div class="flex overflow-x-auto snap-x snap-mandatory gap-4 pb-3 -mx-4 px-4 sm:mx-0 sm:px-0 sm:flex-col sm:space-y-4 sm:gap-0 sm:overflow-visible scrollbar-none"
                     role="region" aria-label="Daftar Kartu Kejadian">
                    @foreach($incidents as $incident)
                        <div class="w-[84vw] max-w-[340px] shrink-0 snap-start sm:w-full sm:max-w-none flex flex-col">
                            <x-incident-card :incident="$incident" />
                        </div>
                    @endforeach
                </div>

                @if($incidents->hasPages())
                    <div class="bg-white rounded-xl shadow-xs p-4 border border-slate-100/90">
                        {{ $incidents->links() }}
                    </div>
                @endif
            @endif
        </section>
    </div>

    <!-- Diagram Alur Transparansi Informasi & Klasifikasi Sumber Data -->
    <x-transparency-flow-diagram />

    <!-- Transparansi Kebutuhan dan Respons Bantuan (4 Kategori) -->
    <section aria-label="Transparansi Kebutuhan dan Respons Bantuan Masyarakat" class="bg-white rounded-2xl shadow-xs p-5 sm:p-6 space-y-6 border border-slate-100/90">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-100 pb-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-telemetry text-[11px] font-semibold">
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
                    Ringkasan kebutuhan dari laporan warga dan pemantauan respons bantuan pada kategori: <strong>Kesehatan</strong>, <strong>Makan dan Minum</strong>, <strong>Bansos</strong>, dan <strong>Evakuasi</strong>.
                </p>
            </div>

            <div class="shrink-0 flex flex-wrap items-center gap-2">
                <a href="{{ route('public.aid-responses.create', ['submission_type' => 'tawaran_rencana']) }}"
                   class="px-3.5 py-2 rounded-lg btn-brand-gradient font-telemetry text-xs font-semibold tracking-wide min-h-[40px] inline-flex items-center gap-1.5 shadow-xs transition">
                    <span class="font-bold text-sm leading-none">+</span>
                    <span>Ajukan Bantuan</span>
                </a>
                <a href="{{ route('public.aid-responses.create', ['submission_type' => 'laporan_penyerahan']) }}"
                   class="px-3.5 py-2 rounded-lg bg-[#173B70] hover:bg-[#0F2A52] text-white font-telemetry text-xs font-semibold tracking-wide min-h-[40px] inline-flex items-center gap-1.5 shadow-xs transition">
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

        <!-- 3 Bagian Visualisasi (Mobile Horizontal Carousel with Edge Peek, Desktop 3-Column Grid) -->
        <div class="flex overflow-x-auto snap-x snap-mandatory gap-4 pb-3 -mx-4 px-4 sm:mx-0 sm:px-0 lg:grid lg:grid-cols-3 scrollbar-none items-stretch"
             role="region" aria-label="Visualisasi Kebutuhan dan Respons Bantuan">
            <!-- BAGIAN 1: KEBUTUHAN TERCATAT -->
            <div class="w-[86vw] max-w-[360px] shrink-0 snap-start lg:w-auto lg:max-w-none bg-slate-50/70 rounded-xl p-4 space-y-3 border border-slate-100 flex flex-col justify-between shadow-2xs">
                <div>
                    <div class="border-b border-slate-200/80 pb-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-900 font-telemetry text-[10px] font-bold uppercase">
                                1. Laporan Warga
                            </span>
                            <span class="font-telemetry text-[11px] text-slate-500">Satuan: Laporan</span>
                        </div>
                        <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mt-1.5">
                            Kebutuhan Tercatat per Kategori
                        </h3>
                        <p class="text-[11px] text-[#788397] mt-0.5">
                            Jumlah laporan warga yang mencantumkan kebutuhan mendesak pada setiap kategori.
                        </p>
                    </div>

                    <div class="space-y-3 pt-3">
                        @foreach($statsCollection as $catStat)
                            @php
                                $needBarWidth = $catStat['needs_count'] > 0
                                    ? max(10, (int) round(($catStat['needs_count'] / $maxNeedsCount) * 100))
                                    : 0;
                            @endphp
                            <div class="space-y-1 text-[11px] font-telemetry">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-bold text-[#202938]">{{ $catStat['label'] }}</span>
                                    <span class="font-bold text-[#173B70]">{{ $catStat['needs_count'] }} laporan</span>
                                </div>
                                <div class="w-full h-2.5 bg-slate-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-[#173B70] rounded-full" style="width: {{ $needBarWidth }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- BAGIAN 2: RESPONS BANTUAN TERCATAT & RINCIAN SATUAN TERPISAH -->
            <div class="w-[86vw] max-w-[360px] shrink-0 snap-start lg:w-auto lg:max-w-none bg-slate-50/70 rounded-xl p-4 space-y-3 border border-slate-100 flex flex-col justify-between shadow-2xs">
                <div>
                    <div class="border-b border-slate-200/80 pb-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-900 font-telemetry text-[10px] font-bold uppercase">
                                2. Respons Bantuan Masyarakat
                            </span>
                            <span class="font-telemetry text-[11px] text-[#788397]">Satuan: Entri &amp; Unit Asli</span>
                        </div>
                        <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mt-1.5">
                            Respons Bantuan Tercatat per Kategori
                        </h3>
                        <p class="text-[11px] text-[#788397] mt-0.5">
                            Rincian bantuan dicatat sesuai satuan aslinya tanpa digabung lintas satuan.
                        </p>
                    </div>

                    <div class="space-y-3 pt-3">
                        @foreach($statsCollection as $catStat)
                            @php
                                $respBarWidth = $catStat['responses_total'] > 0
                                    ? max(10, (int) round(($catStat['responses_total'] / $maxResponsesCount) * 100))
                                    : 0;
                            @endphp
                            <div class="space-y-1.5 text-[11px] font-telemetry border-b border-slate-200/70 pb-2.5 last:border-b-0 last:pb-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-bold text-[#202938]">{{ $catStat['label'] }}</span>
                                    <span class="font-bold text-[#202938]">{{ $catStat['responses_total'] }} entri respons</span>
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
            </div>

            <!-- BAGIAN 3: STATUS RESPONS BANTUAN (STACKED BAR 3 WARNA) -->
            <div class="w-[86vw] max-w-[360px] shrink-0 snap-start lg:w-auto lg:max-w-none bg-slate-50/70 rounded-xl p-4 space-y-3 border border-slate-100 flex flex-col justify-between shadow-2xs">
                <div>
                    <div class="border-b border-slate-200/80 pb-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-900 font-telemetry text-[10px] font-bold uppercase">
                                3. Proporsi Status Respons
                            </span>
                            <span class="font-telemetry text-[11px] text-slate-500">Stacked Bar 3 Warna</span>
                        </div>
                        <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mt-1.5">
                            Distribusi Status Respons Bantuan
                        </h3>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[10px] font-telemetry mt-1.5">
                            <span class="inline-flex items-center gap-1">
                                <span class="w-2.5 h-2.5 rounded-xs bg-amber-500 inline-block"></span>
                                <span>Direncanakan</span>
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <span class="w-2.5 h-2.5 rounded-xs bg-[#173B70] inline-block"></span>
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
                                <span class="font-bold text-[#202938]">{{ $catStat['label'] }}</span>
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
                                        <div class="h-full bg-[#173B70]"
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
    </div>

        <!-- Tabel Kebutuhan & Respons Bantuan Tercatat -->
        <div class="space-y-3 pt-2">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                    Daftar Respons Bantuan Masyarakat / Organisasi yang Tercatat ({{ ($publicAidResponses ?? collect())->count() }})
                </h3>
                <span class="font-telemetry text-[11px] text-[#788397]">
                    Kontak pribadi pemberi respons disembunyikan demi privasi
                </span>
            </div>

            @if(($publicAidResponses ?? collect())->isEmpty())
                <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-6 text-center text-sm text-[#788397]">
                    Belum ada respons bantuan yang tercatat pada sistem.
                </div>
            @else
                <div class="overflow-x-auto border border-slate-100 rounded-xl">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#F8F9FC] border-b border-slate-100 font-telemetry text-[11px] font-bold uppercase text-[#788397]">
                                <th class="py-2.5 px-3">Kode &amp; Waktu Pencatatan / Pembaruan</th>
                                <th class="py-2.5 px-3">Kejadian &amp; Lokasi Tujuan</th>
                                <th class="py-2.5 px-3">Kategori &amp; Jenis Bantuan</th>
                                <th class="py-2.5 px-3">Pemberi Respons &amp; Klasifikasi</th>
                                <th class="py-2.5 px-3">Status &amp; Tautan Dokumentasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @foreach($publicAidResponses as $aid)
                                @php
                                    $aidStatusVal = $aid->status instanceof \BackedEnum ? $aid->status->value : (string) $aid->status;
                                    $isDeliveredSubmission = $aid->isReportedDelivered();
                                @endphp
                                <tr class="hover:bg-slate-50/80 align-top">
                                    <td class="py-3 px-3 whitespace-nowrap space-y-1">
                                        <span class="font-telemetry font-bold text-[#202938] block">{{ $aid->reference_code }}</span>
                                        <span class="px-1.5 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase inline-block {{ $isDeliveredSubmission ? 'bg-indigo-50 text-indigo-900 border border-indigo-200' : 'bg-blue-50 text-[#173B70] border border-blue-200' }}">
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
                                                <span class="text-[#173B70] font-semibold">Diperbarui:</span> {{ $aid->updated_at->format('d M Y, H:i') }} WIB
                                            @else
                                                <span class="text-slate-400">Pembaruan: Belum ada perubahan status</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 px-3">
                                        @if($aid->incident)
                                            <a href="{{ route('public.incidents.show', $aid->incident->slug) }}" class="font-bold text-[#173B70] hover:underline block">
                                                {{ $aid->incident->title }}
                                            </a>
                                        @endif
                                        <span class="text-[#202938]/80 block mt-0.5">Lokasi: {{ $aid->target_location }}</span>
                                        @if($aid->report)
                                            <span class="font-telemetry text-[11px] text-[#788397] block mt-0.5">
                                                Rujukan Tiket: <strong class="text-[#202938]">{{ $aid->report->ticket_number }}</strong>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase inline-block mb-1 border {{ \App\Models\AidResponse::categoryBadgeClass($aid->need_category) }}">
                                            {{ \App\Models\AidResponse::categoryLabel($aid->need_category) }}
                                        </span>
                                        <strong class="text-[#202938] block">{{ $aid->aid_item_title }}</strong>
                                        <span class="font-telemetry text-[11px] text-[#788397]">Satuan/Kuantitas: {{ $aid->quantity_label }}</span>
                                    </td>
                                    <td class="py-3 px-3 space-y-1">
                                        <strong class="text-[#202938] block">{{ $aid->publicResponderName() }}</strong>
                                        <span class="font-telemetry text-[11px] text-[#788397] block">
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
                    <strong class="font-telemetry uppercase">Cakupan Data:</strong>
                    Tabel ini hanya menampilkan kebutuhan dan respons yang tercatat di TanggapTerang, bukan total seluruh bantuan yang ada di lapangan.
                </div>
                <div>
                    <strong class="font-telemetry uppercase">Dokumentasi Lapangan:</strong>
                    Tautan bukti memperlihatkan dokumentasi serah terima yang telah diperiksa petugas, bukan jaminan bahwa seluruh bantuan sudah tersalurkan ke semua warga.
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
        const incidentMarkersMap = new Map();

        markersData.forEach((item) => {
            if (typeof item.latitude !== 'number' || typeof item.longitude !== 'number') {
                return;
            }

            const isHistorical = !item.is_simulation;
            const sourceBadge = isHistorical
                ? '<span style="background: #173B70; color: #FFFFFF; padding: 2px 6px; border-radius: 4px; font-family: \'Poppins\', sans-serif; font-size: 9px; font-weight: 700; text-transform: uppercase;">HISTORIS BNPB</span>'
                : '<span style="background: #315D91; color: #FFFFFF; padding: 2px 6px; border-radius: 4px; font-family: \'Poppins\', sans-serif; font-size: 9px; font-weight: 700; text-transform: uppercase;">SIMULASI</span>';

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
                ? '<div><strong>Sumber Resmi:</strong> Arsip BNPB (DIBI)</div><div><strong>Keterangan:</strong> Riwayat Bencana Nyata (Tanpa Laporan Fiktif)</div>'
                : `<div><strong>Verifikasi:</strong> ${item.verification_label}</div><div><strong>Tindak Lanjut Tim:</strong> ${item.follow_up_label}</div><div><strong>Laporan:</strong> ${item.reports_count} | <strong>Respons Bantuan:</strong> ${item.aid_responses_count || 0}</div>`;

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
                    b.className = 'map-layer-btn px-2 py-1 rounded bg-white border border-slate-300 text-slate-700 font-telemetry text-[10px] font-bold uppercase cursor-pointer';
                });
                btn.className = 'map-layer-btn px-2 py-1 rounded bg-[#173B70] text-white font-telemetry text-[10px] font-bold uppercase cursor-pointer';

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
