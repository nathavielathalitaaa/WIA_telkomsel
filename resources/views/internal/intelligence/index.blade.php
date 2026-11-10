@extends('layouts.internal', [
    'title' => 'Pusat Intelijen Sumber Data — TanggapTerang',
    'internalMenu' => 'intelligence',
])

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 sm:p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <span class="px-2.5 py-0.5 rounded font-telemetry text-xs font-bold uppercase tracking-wider bg-amber-500 text-slate-950 border border-amber-400">
                        Khusus Administrator
                    </span>
                    <span class="px-2.5 py-0.5 rounded font-telemetry text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                        Multi-Source Intelligence
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold font-telemetry text-slate-900 tracking-tight">
                    Pusat Intelijen Sumber Data Kebencanaan
                </h1>
                <p class="text-sm text-slate-600 mt-1 max-w-3xl">
                    Monitoring agregat sumber informasi kebencanaan dan hidrometeorologi resmi domestik dan global. Data eksternal terisolasi dari basis data kejadian utama hingga dikurasi secara manual.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('internal.intelligence.comparisons') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-telemetry text-xs font-bold uppercase tracking-wider shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Perbandingan Sumber Data
                </a>
                <a href="{{ route('internal.intelligence.logs') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-700 font-telemetry text-xs font-bold uppercase tracking-wider transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Log Audit API
                </a>
            </div>
        </div>

        <!-- Telemetry Metrics Bar -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mt-6 pt-5 border-t border-slate-100">
            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                <span class="text-xs font-bold uppercase font-telemetry text-slate-500">Total Rekaman Tersimpan</span>
                <div class="text-2xl font-bold font-telemetry text-slate-900 mt-0.5">{{ number_format($totalRecords) }}</div>
                <span class="text-[11px] text-slate-500">Hasil normalisasi deduplikasi</span>
            </div>
            <div class="bg-blue-50/60 rounded-xl p-3.5 border border-blue-100">
                <span class="text-xs font-bold uppercase font-telemetry text-blue-700">Sumber Dalam Negeri</span>
                <div class="text-2xl font-bold font-telemetry text-blue-900 mt-0.5">{{ number_format($domesticRecordsCount) }}</div>
                <span class="text-[11px] text-blue-700/80">BMKG, PetaBencana, BNPB</span>
            </div>
            <div class="bg-purple-50/60 rounded-xl p-3.5 border border-purple-100">
                <span class="text-xs font-bold uppercase font-telemetry text-purple-700">Sumber Internasional</span>
                <div class="text-2xl font-bold font-telemetry text-purple-900 mt-0.5">{{ number_format($intlRecordsCount) }}</div>
                <span class="text-[11px] text-purple-700/80">Open-Meteo, GDACS, GDELT</span>
            </div>
            <div class="bg-emerald-50/60 rounded-xl p-3.5 border border-emerald-100">
                <span class="text-xs font-bold uppercase font-telemetry text-emerald-700">Integritas Arsitektur</span>
                <div class="text-sm font-bold font-telemetry text-emerald-900 mt-1 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Terisolasi dari Core Incident
                </div>
                <span class="text-[11px] text-emerald-700/80">Memerlukan kurasi Admin</span>
            </div>
        </div>
    </div>

    <!-- Section A: Sumber Data Dalam Negeri -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                <h2 class="text-base sm:text-lg font-bold font-telemetry text-slate-900">
                    A. Sumber Data Dalam Negeri (Domestik)
                </h2>
            </div>
            <span class="text-xs font-telemetry text-slate-500">4 Integrasi Terdaftar</span>
        </div>

        <div class="flex overflow-x-auto pb-4 gap-4 sm:grid sm:grid-cols-2 lg:grid-cols-4 sm:overflow-x-visible sm:pb-0 scrollbar-none snap-x snap-mandatory">
            @foreach($domesticProviders as $provider)
                @php
                    $statusBadge = \App\Models\ExternalProvider::statusBadgeClass($provider->last_status, $provider->is_active);
                    $statusText = \App\Models\ExternalProvider::statusLabel($provider->last_status, $provider->is_active);
                @endphp
                <div class="w-[85vw] sm:w-auto shrink-0 snap-start bg-white rounded-2xl border border-slate-100/90 shadow-xs p-4 flex flex-col justify-between hover:border-blue-400 transition">
                    <div>
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase tracking-wider {{ $statusBadge }} border">
                                {{ $statusText }}
                            </span>
                            <span class="font-telemetry text-[10px] font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200 uppercase">
                                {{ str_replace('_', ' ', $provider->info_type) }}
                            </span>
                        </div>
                        <h3 class="font-bold text-sm text-slate-900 leading-snug">{{ $provider->name }}</h3>
                        <p class="text-xs text-slate-500 font-mono break-all mt-1 line-clamp-1" title="{{ $provider->default_endpoint }}">
                            {{ $provider->default_endpoint }}
                        </p>

                        <div class="mt-3 pt-3 border-t border-slate-100 space-y-1 text-xs">
                            <div class="flex justify-between text-slate-600">
                                <span class="text-slate-400">Terakhir:</span>
                                <span class="font-medium font-telemetry">{{ $provider->last_fetched_at ? $provider->last_fetched_at->diffForHumans() : 'Belum pernah' }}</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span class="text-slate-400">Durasi Respons:</span>
                                <span class="font-medium font-telemetry">{{ $provider->last_duration_ms ? $provider->last_duration_ms.' ms' : '-' }}</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span class="text-slate-400">Record Tersimpan:</span>
                                <span class="font-medium font-telemetry font-bold text-slate-800">{{ $provider->last_record_count }} record</span>
                            </div>
                        </div>

                        @if($provider->last_error_message)
                            <div class="mt-2.5 p-2 rounded bg-amber-50 border border-amber-200 text-[11px] text-amber-900 line-clamp-2" title="{{ $provider->last_error_message }}">
                                {{ $provider->last_error_message }}
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('internal.intelligence.fetch', $provider->code) }}" method="POST" class="mt-4 pt-2">
                        @csrf
                        @if(! $provider->is_active)
                            <button type="button" disabled
                                    class="w-full py-1.5 px-3 rounded-lg bg-slate-100 text-slate-400 border border-slate-200 font-telemetry text-xs font-bold uppercase tracking-wider cursor-not-allowed flex items-center justify-center gap-1.5"
                                    title="Provider belum terintegrasi — endpoint dataset kejadian yang valid belum terbukti">
                                Belum Terintegrasi (Stub)
                            </button>
                        @else
                            <button type="submit"
                                    class="w-full py-1.5 px-3 rounded-lg bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white border border-blue-200 hover:border-transparent font-telemetry text-xs font-bold uppercase tracking-wider transition cursor-pointer flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Tarik Data Sekarang
                            </button>
                        @endif
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Section B: Sumber Data Internasional -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-purple-600"></span>
                <h2 class="text-base sm:text-lg font-bold font-telemetry text-slate-900">
                    B. Sumber Data Internasional (Global)
                </h2>
            </div>
            <span class="text-xs font-telemetry text-slate-500">4 Integrasi Terdaftar</span>
        </div>

        <div class="flex overflow-x-auto pb-4 gap-4 sm:grid sm:grid-cols-2 lg:grid-cols-4 sm:overflow-x-visible sm:pb-0 scrollbar-none snap-x snap-mandatory">
            @foreach($internationalProviders as $provider)
                @php
                    $statusBadge = \App\Models\ExternalProvider::statusBadgeClass($provider->last_status, $provider->is_active);
                    $statusText = \App\Models\ExternalProvider::statusLabel($provider->last_status, $provider->is_active);
                @endphp
                <div class="w-[85vw] sm:w-auto shrink-0 snap-start bg-white rounded-2xl border border-slate-100/90 shadow-xs p-4 flex flex-col justify-between hover:border-purple-400 transition">
                    <div>
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase tracking-wider {{ $statusBadge }} border">
                                {{ $statusText }}
                            </span>
                            <span class="font-telemetry text-[10px] font-bold text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded border border-purple-200 uppercase">
                                {{ str_replace('_', ' ', $provider->info_type) }}
                            </span>
                        </div>
                        <h3 class="font-bold text-sm text-slate-900 leading-snug">{{ $provider->name }}</h3>
                        <p class="text-xs text-slate-500 font-mono break-all mt-1 line-clamp-1" title="{{ $provider->default_endpoint }}">
                            {{ $provider->default_endpoint }}
                        </p>

                        <div class="mt-3 pt-3 border-t border-slate-100 space-y-1 text-xs">
                            <div class="flex justify-between text-slate-600">
                                <span class="text-slate-400">Terakhir:</span>
                                <span class="font-medium font-telemetry">{{ $provider->last_fetched_at ? $provider->last_fetched_at->diffForHumans() : 'Belum pernah' }}</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span class="text-slate-400">Durasi Respons:</span>
                                <span class="font-medium font-telemetry">{{ $provider->last_duration_ms ? $provider->last_duration_ms.' ms' : '-' }}</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span class="text-slate-400">Record Tersimpan:</span>
                                <span class="font-medium font-telemetry font-bold text-slate-800">{{ $provider->last_record_count }} record</span>
                            </div>
                        </div>

                        @if($provider->last_error_message)
                            <div class="mt-2.5 p-2 rounded bg-amber-50 border border-amber-200 text-[11px] text-amber-800 line-clamp-2" title="{{ $provider->last_error_message }}">
                                {{ $provider->last_error_message }}
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('internal.intelligence.fetch', $provider->code) }}" method="POST" class="mt-4 pt-2">
                        @csrf
                        <button type="submit"
                                class="w-full py-1.5 px-3 rounded-lg bg-purple-50 hover:bg-purple-700 text-purple-700 hover:text-white border border-purple-200 hover:border-transparent font-telemetry text-xs font-bold uppercase tracking-wider transition cursor-pointer flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Tarik Data Sekarang
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Weather Comparison Callout / Preview Card -->
    @if(!empty($weatherComparison) && ($weatherComparison['has_data'] ?? false))
        <div class="bg-[#0F172A] text-white rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-800">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="px-2.5 py-0.5 rounded font-telemetry text-[11px] font-bold uppercase tracking-wider bg-blue-500/30 text-blue-200 border border-blue-400/40">
                        Prakiraan Terkini: Domestik vs Internasional
                    </span>
                    <h3 class="text-lg font-bold font-telemetry mt-1">
                        {{ $weatherComparison['title'] ?? 'Prakiraan Cuaca: BMKG (Nasional) vs Open-Meteo (Global)' }}
                    </h3>
                    <p class="text-xs text-blue-200 mt-0.5">
                        Wilayah: {{ $weatherComparison['location'] ?? 'Wilayah Acuan Komparasi' }}
                    </p>
                </div>
                <a href="{{ route('internal.intelligence.comparisons') }}"
                   class="px-4 py-2 rounded-lg bg-white text-blue-950 hover:bg-blue-50 font-telemetry text-xs font-bold uppercase tracking-wider transition shrink-0 inline-flex items-center gap-1.5">
                    Buka Matriks Perbandingan Lengkap →
                </a>
            </div>

            @if($weatherComparison['has_match'] ?? false)
                @php
                    $bmkgData = is_array($weatherComparison['bmkg'] ?? null) ? $weatherComparison['bmkg'] : [];
                    $omData = is_array($weatherComparison['open_meteo'] ?? null) ? $weatherComparison['open_meteo'] : [];

                    $bmkgTime = (isset($bmkgData['time']) && $bmkgData['time'] !== '' && $bmkgData['time'] !== null)
                        ? $bmkgData['time']
                        : ($weatherComparison['valid_time_wib'] ?? 'Waktu tidak tersedia');

                    $omTime = (isset($omData['time']) && $omData['time'] !== '' && $omData['time'] !== null)
                        ? $omData['time']
                        : ($weatherComparison['valid_time_wib'] ?? 'Waktu tidak tersedia');

                    $bmkgTemp = (isset($bmkgData['temp']) && $bmkgData['temp'] !== '' && $bmkgData['temp'] !== null)
                        ? $bmkgData['temp']
                        : '-';
                    $bmkgCond = (isset($bmkgData['condition']) && $bmkgData['condition'] !== '' && $bmkgData['condition'] !== null)
                        ? $bmkgData['condition']
                        : 'Tidak tersedia';
                    $bmkgPrecip = (isset($bmkgData['precipitation']) && $bmkgData['precipitation'] !== '' && $bmkgData['precipitation'] !== null)
                        ? $bmkgData['precipitation']
                        : '-';
                    $bmkgHumidity = (isset($bmkgData['humidity']) && $bmkgData['humidity'] !== '' && $bmkgData['humidity'] !== null)
                        ? $bmkgData['humidity']
                        : '-';
                    $bmkgWind = (isset($bmkgData['wind']) && $bmkgData['wind'] !== '' && $bmkgData['wind'] !== null)
                        ? $bmkgData['wind']
                        : '-';

                    $omTemp = (isset($omData['temp']) && $omData['temp'] !== '' && $omData['temp'] !== null)
                        ? $omData['temp']
                        : '-';
                    $omCond = (isset($omData['condition']) && $omData['condition'] !== '' && $omData['condition'] !== null)
                        ? $omData['condition']
                        : 'Tidak tersedia';
                    $omPrecip = (isset($omData['precipitation']) && $omData['precipitation'] !== '' && $omData['precipitation'] !== null)
                        ? $omData['precipitation']
                        : '-';
                    $omHumidity = (isset($omData['humidity']) && $omData['humidity'] !== '' && $omData['humidity'] !== null)
                        ? $omData['humidity']
                        : '-';
                    $omWind = (isset($omData['wind']) && $omData['wind'] !== '' && $omData['wind'] !== null)
                        ? $omData['wind']
                        : '-';
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 pt-4 border-t border-blue-800/60">
                    <div class="bg-white/10 rounded-lg p-3.5 border border-white/10">
                        <div class="flex justify-between items-center text-xs font-telemetry text-blue-300 mb-1">
                            <span class="font-bold">BMKG (Lokal)</span>
                            <span>{{ $bmkgTime }}</span>
                        </div>
                        <div class="text-xl font-bold font-telemetry">{{ $bmkgTemp }} — {{ $bmkgCond }}</div>
                        <div class="text-xs text-blue-200 mt-1">Presipitasi: {{ $bmkgPrecip }} • Kelembapan: {{ $bmkgHumidity }} • Angin: {{ $bmkgWind }}</div>
                    </div>
                    <div class="bg-white/10 rounded-lg p-3.5 border border-white/10">
                        <div class="flex justify-between items-center text-xs font-telemetry text-purple-300 mb-1">
                            <span class="font-bold">Open-Meteo (Global ECMWF/GFS)</span>
                            <span>{{ $omTime }}</span>
                        </div>
                        <div class="text-xl font-bold font-telemetry">{{ $omTemp }} — {{ $omCond }}</div>
                        <div class="text-xs text-purple-200 mt-1">Presipitasi: {{ $omPrecip }} • Kelembapan: {{ $omHumidity }} • Angin: {{ $omWind }}</div>
                    </div>
                </div>
            @else
                <div class="mt-4 pt-4 border-t border-blue-800/60 text-xs text-blue-200 leading-relaxed flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        <span>{{ $weatherComparison['status_message'] ?? 'Tidak ditemukan slot waktu valid yang identik antara BMKG dan Open-Meteo saat ini.' }}</span>
                    </div>
                    <a href="{{ route('internal.intelligence.comparisons') }}" class="underline hover:text-white font-bold font-telemetry shrink-0">
                        Buka Matriks Lengkap &rarr;
                    </a>
                </div>
            @endif
        </div>
    @endif

    <!-- Section C: Master Data & Filter Rekaman Eksternal -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-100/90 overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                <div>
                    <h3 class="text-base font-bold font-telemetry text-slate-900">
                        Rekaman Data Eksternal Tersimpan
                    </h3>
                    <p class="text-xs text-slate-500">
                        Seluruh entri telah divalidasi, dinormalisasi, dan disimpan di tabel eksternal khusus.
                    </p>
                </div>

                <!-- Filter Controls -->
                <form method="GET" action="{{ route('internal.intelligence.index') }}" class="flex flex-wrap items-center gap-2">
                    <select name="provider" class="text-xs rounded-lg border border-slate-300 py-1.5 px-2.5 bg-white text-slate-700">
                        <option value="">Semua Provider</option>
                        @foreach($domesticProviders as $p)
                            <option value="{{ $p->code }}" {{ $selectedProvider === $p->code ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                        @foreach($internationalProviders as $p)
                            <option value="{{ $p->code }}" {{ $selectedProvider === $p->code ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>

                    <select name="category" class="text-xs rounded-lg border border-slate-300 py-1.5 px-2.5 bg-white text-slate-700">
                        <option value="">Semua Kategori</option>
                        <option value="dalam_negeri" {{ $selectedCategory === 'dalam_negeri' ? 'selected' : '' }}>Dalam Negeri</option>
                        <option value="internasional" {{ $selectedCategory === 'internasional' ? 'selected' : '' }}>Internasional</option>
                    </select>

                    <select name="info_type" class="text-xs rounded-lg border border-slate-300 py-1.5 px-2.5 bg-white text-slate-700">
                        <option value="">Semua Tipe</option>
                        <option value="gempa" {{ $selectedInfoType === 'gempa' ? 'selected' : '' }}>Gempa</option>
                        <option value="prakiraan_cuaca" {{ $selectedInfoType === 'prakiraan_cuaca' ? 'selected' : '' }}>Prakiraan Cuaca</option>
                        <option value="laporan_masyarakat" {{ $selectedInfoType === 'laporan_masyarakat' ? 'selected' : '' }}>Laporan Warga</option>
                        <option value="hidrologi" {{ $selectedInfoType === 'hidrologi' ? 'selected' : '' }}>Hidrologi</option>
                        <option value="peringatan_dini" {{ $selectedInfoType === 'peringatan_dini' ? 'selected' : '' }}>Peringatan Dini</option>
                        <option value="berita" {{ $selectedInfoType === 'berita' ? 'selected' : '' }}>Berita</option>
                    </select>

                    <input type="text" name="q" value="{{ $searchKeyword }}" placeholder="Cari judul/lokasi..."
                           class="text-xs rounded-lg border border-slate-300 py-1.5 px-3 bg-white text-slate-700 placeholder-slate-400 w-36 sm:w-44">

                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-telemetry text-xs font-bold uppercase transition">
                        Filter
                    </button>
                    @if($selectedProvider || $selectedCategory || $selectedInfoType || $searchKeyword)
                        <a href="{{ route('internal.intelligence.index') }}" class="px-2.5 py-1.5 rounded-lg border border-slate-300 hover:bg-slate-50 text-slate-600 font-telemetry text-xs transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 font-telemetry uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Sumber & Kategori</th>
                        <th class="px-4 py-3">Jenis Informasi</th>
                        <th class="px-4 py-3">Judul & Ringkasan</th>
                        <th class="px-4 py-3">Lokasi / Koordinat</th>
                        <th class="px-4 py-3">Waktu Kejadian/Prakiraan</th>
                        <th class="px-4 py-3">Diambil</th>
                        <th class="px-4 py-3">Validasi</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($records as $rec)
                        @php
                            $catBadge = $rec->category === 'dalam_negeri'
                                ? 'bg-blue-100 text-blue-800 border-blue-200'
                                : 'bg-purple-100 text-purple-800 border-purple-200';
                            $typeBadge = match ($rec->info_type) {
                                'gempa' => 'bg-rose-100 text-rose-800 border-rose-200',
                                'prakiraan_cuaca' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                                'laporan_masyarakat' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'hidrologi' => 'bg-teal-100 text-teal-800 border-teal-200',
                                'peringatan_dini' => 'bg-orange-100 text-orange-800 border-orange-200',
                                'berita' => 'bg-slate-100 text-slate-800 border-slate-200',
                                default => 'bg-slate-100 text-slate-800',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $rec->provider?->name ?? $rec->provider_code }}</div>
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase tracking-wider {{ $catBadge }} border">
                                    {{ $rec->category === 'dalam_negeri' ? 'Dalam Negeri' : 'Internasional' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase tracking-wider {{ $typeBadge }} border">
                                    {{ str_replace('_', ' ', $rec->info_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 min-w-[280px]">
                                <div class="font-bold text-slate-900 line-clamp-1">{{ $rec->title }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5 line-clamp-2">{{ $rec->summary }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-medium text-slate-800">{{ $rec->location_name ?? '-' }}</div>
                                @if($rec->latitude && $rec->longitude)
                                    <div class="text-[11px] font-mono text-slate-400">
                                        {{ round($rec->latitude, 3) }}, {{ round($rec->longitude, 3) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap font-telemetry text-slate-600">
                                {{ $rec->occurred_at ? $rec->occurred_at->format('d/m/Y H:i') : ($rec->published_at ? $rec->published_at->format('d/m/Y H:i') : '-') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap font-telemetry text-slate-500 text-[11px]">
                                {{ $rec->fetched_at ? $rec->fetched_at->diffForHumans() : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-1.5 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase {{ $rec->validation_status === 'valid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    {{ $rec->validation_status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <a href="{{ route('internal.intelligence.records.show', $rec->id) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-800 font-telemetry text-[11px] font-bold uppercase transition">
                                    Detail / JSON
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-slate-500">
                                <div class="max-w-md mx-auto space-y-2">
                                    <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    <p class="font-bold text-slate-700">Belum ada rekaman data eksternal</p>
                                    <p class="text-xs text-slate-500">Klik tombol "Tarik Data Sekarang" pada salah satu provider di atas untuk mengambil data secara live.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $records->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

