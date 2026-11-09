@extends('layouts.app')

@section('title', 'Transparansi Sumber Data & Integrasi API — TanggapTerang')
@section('activeTab', 'sumber_data')

@section('content')
<div class="space-y-8">
    <!-- Breadcrumb & Header Hero -->
    <header class="bg-white border border-slate-300 rounded-xl shadow-2xs overflow-hidden">
        <div class="bg-[#0F172A] text-white px-4 sm:px-6 py-6 border-b border-slate-800">
            <div class="flex flex-wrap items-center gap-2 text-xs font-telemetry text-slate-400 mb-2">
                <a href="{{ route('public.monitor') }}" class="hover:text-white transition">Beranda</a>
                <span>&rsaquo;</span>
                <span class="text-blue-400 font-bold uppercase tracking-wider">Transparansi Sumber Data</span>
            </div>
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold tracking-tight text-white font-sans">
                        Transparansi Sumber Data &amp; Integrasi API
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1.5 max-w-3xl leading-relaxed">
                        Pemantauan publik status integrasi, waktu pembaruan, dan rekaman data dari layanan kebencanaan Dalam Negeri dan Internasional. Penilai dan masyarakat dapat menelusuri dasar informasi secara terbuka tanpa harus login.
                    </p>
                </div>
                <div class="shrink-0 flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1.5 rounded-lg bg-blue-950/80 text-blue-300 border border-blue-800 font-telemetry text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Akses Publik Terbuka
                    </span>
                    <a href="{{ route('public.monitor') }}"
                       class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-telemetry text-xs font-bold uppercase tracking-wider transition">
                        &larr; Kembali ke Pantau
                    </a>
                </div>
            </div>
        </div>

        <!-- Metric Telemetry Counter Bar -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 divide-x divide-y sm:divide-y-0 divide-slate-200 bg-slate-50 text-slate-700 text-xs">
            <div class="p-3.5 sm:p-4">
                <span class="font-telemetry text-[10px] font-bold uppercase text-slate-500 block">Total Provider</span>
                <span class="font-telemetry text-lg sm:text-xl font-extrabold text-slate-900 mt-0.5 block">{{ $totalProviders }} Layanan</span>
                <span class="text-[10px] text-slate-500 font-medium">4 DN • 4 Internasional</span>
            </div>
            <div class="p-3.5 sm:p-4">
                <span class="font-telemetry text-[10px] font-bold uppercase text-emerald-700 block">Data Berhasil</span>
                <span class="font-telemetry text-lg sm:text-xl font-extrabold text-emerald-800 mt-0.5 block">{{ $successfulProvidersCount }} Provider</span>
                <span class="text-[10px] text-emerald-700 font-medium">Tersimpan di MySQL</span>
            </div>
            <div class="p-3.5 sm:p-4">
                <span class="font-telemetry text-[10px] font-bold uppercase text-amber-700 block">Terkendala / Timeout</span>
                <span class="font-telemetry text-lg sm:text-xl font-extrabold text-amber-800 mt-0.5 block">{{ $issueCount }} Provider</span>
                <span class="text-[10px] text-amber-700 font-medium">GDELT (Akses Terbatas)</span>
            </div>
            <div class="p-3.5 sm:p-4">
                <span class="font-telemetry text-[10px] font-bold uppercase text-slate-600 block">Belum Terintegrasi</span>
                <span class="font-telemetry text-lg sm:text-xl font-extrabold text-slate-800 mt-0.5 block">{{ $notIntegratedCount }} Provider</span>
                <span class="text-[10px] text-slate-500 font-medium">BNPB (Dataset Belum Ada)</span>
            </div>
            <div class="p-3.5 sm:p-4">
                <span class="font-telemetry text-[10px] font-bold uppercase text-blue-700 block">Total Rekaman API</span>
                <span class="font-telemetry text-lg sm:text-xl font-extrabold text-blue-900 mt-0.5 block">{{ number_format($totalRecords) }} Data</span>
                <span class="text-[10px] text-blue-700 font-medium">{{ $domesticRecordsCount }} DN • {{ $intlRecordsCount }} LN</span>
            </div>
            <div class="p-3.5 sm:p-4">
                <span class="font-telemetry text-[10px] font-bold uppercase text-slate-500 block">Sinkronisasi Terakhir</span>
                <span class="font-telemetry text-xs font-bold text-slate-900 mt-1 block truncate">
                    {{ $lastSyncTime ? $lastSyncTime->diffForHumans() : 'Belum sinkron' }}
                </span>
                <span class="text-[10px] text-slate-500 font-mono">
                    {{ $lastSyncTime ? $lastSyncTime->format('d M, H:i') . ' WIB' : '-' }}
                </span>
            </div>
        </div>
    </header>

    <!-- Deklarasi Transparansi & Integritas Publik -->
    <section aria-label="Deklarasi Transparansi" class="p-4 sm:p-5 rounded-xl bg-blue-50/70 border border-blue-200 text-slate-700 space-y-2">
        <div class="flex items-start gap-2.5">
            <span class="p-1 rounded bg-blue-600 text-white shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <div class="space-y-1 text-xs leading-relaxed">
                <h2 class="font-bold font-telemetry uppercase text-blue-900 text-xs tracking-wider">
                    Prinsip Transparansi &amp; Integritas Data Eksternal
                </h2>
                <p>
                    1. <strong>Rujukan Situasi, Bukan Status Otomatis:</strong> Data dari API eksternal disajikan sebagai rujukan terbuka dan pembanding situasi. Data ini tidak otomatis ditetapkan sebagai kejadian resmi sebelum melalui verifikasi silang tim.
                </p>
                <p>
                    2. <strong>Kejujuran Status Integrasi:</strong> Layanan <strong>BNPB</strong> berstatus <strong>Belum Terintegrasi</strong> karena endpoint data terbuka belum tersedia secara publik. TanggapTerang tidak mengarang data atas nama BNPB. Status layanan <strong>GDELT</strong> mengikuti hasil penarikan aktual.
                </p>
                <p>
                    3. <strong>Pemisahan Data Teknis:</strong> Rincian teknis seperti muatan mentah (<em>raw payload</em>) dan parameter internal dilindungi untuk akses administrator dan tidak ditampilkan ke publik.
                </p>
            </div>
        </div>
    </section>

    <!-- SECTION 1: KARTU SETIAP PROVIDER (8 PROVIDER LENGKAP) -->
    <section aria-label="Kartu Provider Eksternal" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-3">
            <div>
                <h2 class="font-telemetry text-sm sm:text-base font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                    Status Integrasi 8 Provider Eksternal
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Rincian status fetch terkini, telemetri, dan 3 cuplikan data terbaru untuk setiap sumber.
                </p>
            </div>
            <div class="flex items-center gap-2 text-xs font-telemetry">
                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 border border-emerald-300 font-bold">Aktif &amp; Tersimpan: {{ $successfulProvidersCount }}</span>
                <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-700 border border-slate-300 font-bold">Stub: {{ $notIntegratedCount }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($providers as $provider)
                @php
                    $statusBadge = \App\Models\ExternalProvider::statusBadgeClass($provider->last_status, $provider->is_active);
                    $statusText = \App\Models\ExternalProvider::statusLabel($provider->last_status, $provider->is_active);
                    $providerRecordCount = \App\Models\ExternalSourceRecord::where('provider_code', $provider->code)->count();
                @endphp
                <div class="bg-white rounded-xl border border-slate-300 shadow-2xs hover:shadow-xs transition p-4 flex flex-col justify-between space-y-4">
                    <div>
                        <!-- Header Kartu -->
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase tracking-wider {{ $statusBadge }} border shrink-0">
                                {{ $statusText }}
                            </span>
                            <span class="font-telemetry text-[10px] font-bold text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200 uppercase shrink-0">
                                {{ $provider->category === 'dalam_negeri' ? 'Dalam Negeri' : 'Internasional' }}
                            </span>
                        </div>

                        <h3 class="font-bold text-sm text-slate-900 leading-snug font-sans">
                            {{ $provider->name }}
                        </h3>

                        <!-- Klasifikasi Sifat Data -->
                        <div class="mt-1.5">
                            <span class="inline-block px-1.5 py-0.5 rounded font-telemetry text-[10px] font-bold border {{ $provider->dataNatureBadgeClass() }}">
                                {{ $provider->dataNatureLabel() }}
                            </span>
                        </div>

                        <!-- Endpoint / Tautan Sumber Asli -->
                        <div class="mt-3 pt-2.5 border-t border-slate-100 space-y-1">
                            <div class="text-[10px] font-telemetry text-slate-500 uppercase font-bold">
                                Sumber Rujukan Resmi:
                            </div>
                            <div class="flex items-center justify-between gap-1 text-xs">
                                <a href="{{ $provider->officialPortalUrl() }}" target="_blank" rel="noopener noreferrer"
                                   class="text-blue-700 hover:text-blue-900 font-medium inline-flex items-center gap-1 truncate"
                                   title="Buka portal resmi / dokumentasi {{ $provider->name }}">
                                    <span class="truncate">{{ parse_url($provider->officialPortalUrl(), PHP_URL_HOST) ?? $provider->officialPortalUrl() }}</span>
                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>
                        </div>

                        <!-- Telemetri Aktual -->
                        <div class="mt-3 pt-2.5 border-t border-slate-100 space-y-1 text-xs">
                            <div class="flex justify-between text-slate-600">
                                <span class="text-slate-500 text-[11px]">Waktu Pembaruan:</span>
                                <span class="font-telemetry font-bold text-slate-800 text-[11px]">
                                    {{ $provider->last_fetched_at ? $provider->last_fetched_at->diffForHumans() : 'Belum pernah' }}
                                </span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span class="text-slate-500 text-[11px]">Data Diterima (Terakhir):</span>
                                <span class="font-telemetry font-medium text-slate-800 text-[11px]">
                                    {{ $provider->last_record_count }} record
                                </span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span class="text-slate-500 text-[11px]">Tersimpan di DB:</span>
                                <span class="font-telemetry font-extrabold text-blue-900 text-[11px]">
                                    {{ $providerRecordCount }} record
                                </span>
                            </div>
                            @if($provider->last_duration_ms)
                                <div class="flex justify-between text-slate-600">
                                    <span class="text-slate-500 text-[11px]">Latensi HTTP:</span>
                                    <span class="font-telemetry font-mono text-slate-700 text-[11px]">
                                        {{ $provider->last_duration_ms }} ms
                                    </span>
                                </div>
                            @endif
                        </div>

                        @if($provider->last_error_message && $provider->last_status !== 'success')
                            <div class="mt-2.5 p-2 rounded bg-amber-50 border border-amber-200 text-[11px] text-amber-900 leading-snug">
                                <span class="font-bold">Catatan:</span> {{ Str::limit($provider->last_error_message, 120) }}
                            </div>
                        @endif

                        <!-- Contoh 3 Rekaman Terbaru -->
                        <div class="mt-3.5 pt-2.5 border-t border-slate-100 space-y-1.5">
                            <div class="text-[10px] font-telemetry uppercase font-bold text-slate-500 flex items-center justify-between">
                                <span>3 Contoh Rekaman:</span>
                                <span class="text-slate-400 font-mono">{{ $provider->sourceRecords->count() }} tampil</span>
                            </div>

                            @if($provider->sourceRecords->isNotEmpty())
                                <ul class="space-y-1.5">
                                    @foreach($provider->sourceRecords as $sample)
                                        <li class="p-2 rounded bg-slate-50 border border-slate-200 text-[11px] text-slate-800 hover:bg-blue-50/50 hover:border-blue-200 transition">
                                            <div class="font-bold line-clamp-1 text-[#0F172A]" title="{{ $sample->title }}">
                                                {{ $sample->title }}
                                            </div>
                                            <div class="flex items-center justify-between text-[10px] text-slate-500 mt-1">
                                                <span class="truncate max-w-[130px]">{{ $sample->location_name ?: 'Indonesia' }}</span>
                                                <a href="{{ route('public.sources.show', $sample->id) }}"
                                                   class="text-blue-700 hover:text-blue-900 font-bold font-telemetry inline-flex items-center gap-0.5">
                                                    Lihat &rarr;
                                                </a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="p-2.5 rounded bg-slate-100 text-[11px] text-slate-600 leading-snug">
                                    @if(! $provider->is_active)
                                        <em>Belum ada rekaman tersimpan — endpoint dataset kejadian BNPB belum terintegrasi. Tidak ada data yang dikarang.</em>
                                    @elseif($provider->last_status === 'failed')
                                        <em>Belum ada rekaman tersimpan — fetch terakhir gagal/dibatasi oleh server penyedia.</em>
                                    @else
                                        <em>Belum ada rekaman tersimpan dari provider ini.</em>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Link Provider -->
                    <div class="pt-2 border-t border-slate-100">
                        <a href="{{ route('public.sources.index', ['provider' => $provider->code]) }}"
                           class="w-full py-1.5 px-3 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-800 border border-slate-200 font-telemetry text-xs font-bold uppercase tracking-wider text-center block transition">
                            Filter Data {{ $provider->code }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- SECTION 2: FILTER KATEGORI & PENCARIAN -->
    <section aria-label="Filter Rekaman Data" class="bg-white border border-slate-300 rounded-xl p-4 sm:p-5 shadow-2xs space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
                <h2 class="font-telemetry text-sm sm:text-base font-bold uppercase tracking-wider text-slate-900">
                    Eksplorasi Rekaman Data Eksternal
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Filter berdasarkan kategori jenis informasi, cakupan wilayah, atau kata kunci pencarian.
                </p>
            </div>
            @if($selectedInfoType || $selectedCategory || $selectedProvider || $searchKeyword)
                <a href="{{ route('public.sources.index') }}"
                   class="inline-flex items-center gap-1 text-xs font-telemetry font-bold text-red-600 hover:text-red-800">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Reset Semua Filter
                </a>
            @endif
        </div>

        <!-- Category Pill Buttons -->
        <div class="flex flex-wrap items-center gap-1.5">
            <span class="text-xs font-telemetry font-bold text-slate-500 uppercase mr-1">Kategori:</span>
            @foreach($availableCategories as $typeKey => $typeLabel)
                @php
                    $isActive = ($selectedInfoType === $typeKey);
                    $url = route('public.sources.index', array_merge(request()->query(), ['info_type' => $typeKey, 'page' => 1]));
                @endphp
                <a href="{{ $url }}"
                   class="px-2.5 py-1 rounded-lg font-telemetry text-xs font-bold transition {{ $isActive ? 'nav-active-gradient' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200' }}">
                    {{ $typeLabel }}
                </a>
            @endforeach
        </div>

        <!-- Scope & Search Form -->
        <form action="{{ route('public.sources.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-2">
            @if($selectedInfoType)
                <input type="hidden" name="info_type" value="{{ $selectedInfoType }}">
            @endif

            <div class="sm:col-span-3">
                <label for="filter-category" class="block font-telemetry text-[11px] font-bold uppercase text-slate-600 mb-1">Cakupan Wilayah</label>
                <select id="filter-category" name="category" onchange="this.form.submit()"
                        class="w-full text-xs rounded-lg border-slate-300 shadow-2xs focus:border-blue-500 focus:ring-blue-500 py-2">
                    <option value="">Semua Cakupan</option>
                    <option value="dalam_negeri" {{ $selectedCategory === 'dalam_negeri' ? 'selected' : '' }}>Dalam Negeri (Indonesia)</option>
                    <option value="internasional" {{ $selectedCategory === 'internasional' ? 'selected' : '' }}>Internasional / Global</option>
                </select>
            </div>

            <div class="sm:col-span-3">
                <label for="filter-provider" class="block font-telemetry text-[11px] font-bold uppercase text-slate-600 mb-1">Provider Spesifik</label>
                <select id="filter-provider" name="provider" onchange="this.form.submit()"
                        class="w-full text-xs rounded-lg border-slate-300 shadow-2xs focus:border-blue-500 focus:ring-blue-500 py-2">
                    <option value="">Semua Provider</option>
                    @foreach($allProviders as $p)
                        <option value="{{ $p->code }}" {{ $selectedProvider === $p->code ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-4">
                <label for="filter-q" class="block font-telemetry text-[11px] font-bold uppercase text-slate-600 mb-1">Cari Kata Kunci</label>
                <input type="text" id="filter-q" name="q" value="{{ $searchKeyword }}"
                       placeholder="Cari judul, lokasi, atau ringkasan..."
                       class="w-full text-xs rounded-lg border-slate-300 shadow-2xs focus:border-blue-500 focus:ring-blue-500 py-2 px-3">
            </div>

            <div class="sm:col-span-2 flex items-end">
                <button type="submit"
                        class="w-full py-2 px-3 rounded-lg btn-brand-gradient font-telemetry text-xs font-bold uppercase tracking-wider text-center cursor-pointer">
                    Terapkan
                </button>
            </div>
        </form>
    </section>

    <!-- SECTION 3: DAFTAR REKAMAN PUBLIK TERFILTER -->
    <section aria-label="Daftar Rekaman Data Publik" class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-telemetry text-sm sm:text-base font-bold uppercase tracking-wider text-slate-900">
                Daftar Rekaman Data Terkini ({{ $records->total() }} Data Ditemukan)
            </h2>
            <span class="text-xs text-slate-500 font-telemetry">
                Halaman {{ $records->currentPage() }} dari {{ $records->lastPage() ?: 1 }}
            </span>
        </div>

        @if($records->isEmpty())
            <div class="bg-white border border-slate-300 rounded-xl p-8 text-center space-y-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-bold text-slate-800 text-sm">Tidak ada rekaman data yang sesuai filter</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    Coba ubah pilihan kategori, cakupan wilayah, atau reset filter pencarian untuk melihat data lainnya.
                </p>
                <a href="{{ route('public.sources.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 font-telemetry text-xs font-bold uppercase">
                    Reset Filter
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($records as $rec)
                    <article class="bg-white rounded-xl border border-slate-300 shadow-2xs hover:border-blue-400 hover:shadow-xs transition p-4 flex flex-col justify-between space-y-3">
                        <div class="space-y-2">
                            <!-- Header Rekaman -->
                            <div class="flex flex-wrap items-center justify-between gap-1.5">
                                <span class="font-telemetry text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-blue-50 text-blue-800 border border-blue-200">
                                    {{ \App\Models\ExternalProvider::infoTypeLabel($rec->info_type) }}
                                </span>
                                <span class="font-telemetry text-[10px] text-slate-500">
                                    ID: {{ Str::limit($rec->external_id, 16) }}
                                </span>
                            </div>

                            <h3 class="font-bold text-sm text-slate-900 leading-snug font-sans">
                                <a href="{{ route('public.sources.show', $rec->id) }}" class="hover:text-blue-700 transition">
                                    {{ $rec->title }}
                                </a>
                            </h3>

                            @if($rec->summary)
                                <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                                    {{ $rec->summary }}
                                </p>
                            @endif

                            <!-- Meta Lokasi & Waktu -->
                            <div class="pt-2 border-t border-slate-100 space-y-1 text-[11px] text-slate-600">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="truncate font-medium">{{ $rec->location_name ?: 'Koordinat / Wilayah' }}</span>
                                    @if($rec->latitude && $rec->longitude)
                                        <span class="text-[10px] font-mono text-slate-400">({{ number_format($rec->latitude, 2) }}, {{ number_format($rec->longitude, 2) }})</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Waktu: {{ $rec->occurred_at ? $rec->occurred_at->format('d M Y, H:i') . ' WIB' : ($rec->published_at ? $rec->published_at->format('d M Y, H:i') . ' WIB' : '-') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Rekaman -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-1.5">
                                <span class="font-telemetry text-[10px] font-bold uppercase text-slate-500">
                                    {{ $rec->provider?->name ? Str::limit($rec->provider->name, 18) : $rec->provider_code }}
                                </span>
                                @if($rec->is_simulation)
                                    <span class="px-1.5 py-0.2 rounded font-telemetry text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-300">Simulasi</span>
                                @else
                                    <span class="px-1.5 py-0.2 rounded font-telemetry text-[9px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">Live API</span>
                                @endif
                            </div>
                            <a href="{{ route('public.sources.show', $rec->id) }}"
                               class="font-telemetry text-xs font-bold text-blue-700 hover:text-blue-900 inline-flex items-center gap-1">
                                <span>Detail</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="pt-4">
                {{ $records->links() }}
            </div>
        @endif
    </section>

    <!-- SECTION 4: TELEMETRI LOG PENGAMBILAN DATA PUBLIK (AUDIT TRAIL RINGKAS) -->
    <section aria-label="Log Sinkronisasi API" class="bg-white border border-slate-300 rounded-xl p-4 sm:p-5 shadow-2xs space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
            <div>
                <h2 class="font-telemetry text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-slate-900"></span>
                    Riwayat Sinkronisasi API Terkini
                </h2>
                <p class="text-[11px] text-slate-500 mt-0.5">
                    Bukti aktivitas penarikan data yang tercatat dalam audit log TanggapTerang.
                </p>
            </div>
            <span class="text-[10px] font-telemetry uppercase text-slate-400 font-bold">
                6 Aktivitas Terakhir
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 font-telemetry text-[10px] font-bold uppercase text-slate-600">
                        <th class="py-2 px-3">Waktu Eksekusi</th>
                        <th class="py-2 px-3">Layanan / Provider</th>
                        <th class="py-2 px-3">Status Sinkronisasi</th>
                        <th class="py-2 px-3">Respons HTTP</th>
                        <th class="py-2 px-3">Latensi</th>
                        <th class="py-2 px-3 text-right">Data Tersimpan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-telemetry text-[11px]">
                    @forelse($recentFetchLogs as $log)
                        @php
                            $logBadge = match($log->status) {
                                'success' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                'not_integrated' => 'bg-slate-100 text-slate-700 border-slate-300',
                                default => 'bg-amber-100 text-amber-800 border-amber-300',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-2 px-3 text-slate-500 font-mono">
                                {{ $log->created_at ? $log->created_at->format('d M Y, H:i:s') : '-' }} WIB
                            </td>
                            <td class="py-2 px-3 font-bold text-slate-900">
                                {{ $log->provider?->name ?? $log->provider_code }}
                            </td>
                            <td class="py-2 px-3">
                                <span class="px-2 py-0.5 rounded border text-[10px] font-bold uppercase {{ $logBadge }}">
                                    {{ \App\Models\ExternalProvider::statusLabel($log->status, $log->provider?->is_active ?? true) }}
                                </span>
                            </td>
                            <td class="py-2 px-3 font-mono">
                                {{ $log->http_status ? 'HTTP ' . $log->http_status : '-' }}
                            </td>
                            <td class="py-2 px-3 font-mono text-slate-600">
                                {{ $log->duration_ms ? $log->duration_ms . ' ms' : '-' }}
                            </td>
                            <td class="py-2 px-3 text-right font-bold text-slate-900">
                                {{ $log->record_count }} data
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-slate-500 text-xs">
                                Belum ada riwayat aktivitas sinkronisasi yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
