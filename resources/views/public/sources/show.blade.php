@extends('layouts.app')

@section('title', 'Rincian Data: ' . $record->title . ' — TanggapTerang')
@section('activeTab', 'sumber_data')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-telemetry text-slate-500" aria-label="Breadcrumb">
        <a href="{{ route('public.monitor') }}" class="hover:text-slate-900 transition">Beranda</a>
        <span>&rsaquo;</span>
        <a href="{{ route('public.sources.index') }}" class="hover:text-slate-900 transition">Transparansi Sumber Data</a>
        <span>&rsaquo;</span>
        <span class="text-blue-600 font-bold uppercase truncate max-w-[200px]">Rekaman #{{ $record->id }}</span>
    </nav>

    <!-- Main Card -->
    <article class="bg-white border border-slate-100/90 rounded-2xl shadow-xs overflow-hidden">
        <!-- Header -->
        <header class="bg-[#173B70] text-white p-5 sm:p-6 border-b border-[#315D91] space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-1 rounded-lg bg-blue-900/80 text-blue-200 border border-blue-700 font-telemetry text-xs font-bold uppercase tracking-wider">
                        {{ \App\Models\ExternalProvider::infoTypeLabel($record->info_type) }}
                    </span>
                    <span class="px-2 py-0.5 rounded font-telemetry text-[11px] font-bold uppercase {{ $record->is_simulation ? 'bg-amber-900/70 text-amber-200 border border-amber-700' : 'bg-emerald-900/70 text-emerald-200 border border-emerald-700' }}">
                        {{ $record->is_simulation ? 'Simulasi Prototype' : 'Data API Nyata' }}
                    </span>
                </div>
                <span class="font-telemetry text-xs text-slate-400 font-mono">
                    ID Eksternal: {{ $record->external_id }}
                </span>
            </div>

            <h1 class="text-lg sm:text-2xl font-extrabold text-white leading-snug font-sans">
                {{ $record->title }}
            </h1>

            <div class="flex flex-wrap items-center gap-4 text-xs font-telemetry text-slate-300 pt-1">
                <span>Penyedia: <strong class="text-white">{{ $record->provider?->name ?? $record->provider_code }}</strong></span>
                <span>•</span>
                <span>Cakupan: <strong class="text-white">{{ $record->category === 'dalam_negeri' ? 'Dalam Negeri' : 'Internasional' }}</strong></span>
                @if($record->provider)
                    <span>•</span>
                    <span class="text-blue-300">{{ $record->provider->dataNatureLabel() }}</span>
                @endif
            </div>
        </header>

        <!-- Body Details -->
        <div class="p-5 sm:p-6 space-y-6">
            <!-- Ringkasan / Deskripsi -->
            <section class="space-y-2">
                <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-slate-500">
                    Ringkasan Informasi
                </h2>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-sm text-slate-700 leading-relaxed font-sans">
                    {{ $record->summary ?: 'Tidak ada deskripsi tambahan dari penyedia data.' }}
                </div>
            </section>

            <!-- Metadata Grid -->
            <section class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Waktu Kejadian & Publikasi -->
                <div class="p-4 rounded-xl border border-slate-100 bg-white shadow-2xs space-y-2">
                    <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-slate-500">
                        Waktu &amp; Tanggal
                    </h3>
                    <dl class="space-y-1.5 text-xs text-slate-700">
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Waktu Kejadian:</dt>
                            <dd class="font-mono font-medium text-slate-900">
                                {{ $record->occurred_at ? $record->occurred_at->format('d M Y, H:i') . ' WIB' : '-' }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Waktu Publikasi Sumber:</dt>
                            <dd class="font-mono font-medium text-slate-900">
                                {{ $record->published_at ? $record->published_at->format('d M Y, H:i') . ' WIB' : '-' }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Waktu Pengambilan Data:</dt>
                            <dd class="font-mono font-medium text-blue-900">
                                {{ $record->fetched_at ? $record->fetched_at->format('d M Y, H:i:s') . ' WIB' : '-' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Lokasi & Koordinat -->
                <div class="p-4 rounded-xl border border-slate-100 bg-white shadow-2xs space-y-2">
                    <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-slate-500">
                        Lokasi &amp; Georeferensi
                    </h3>
                    <dl class="space-y-1.5 text-xs text-slate-700">
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Nama Wilayah / Lokasi:</dt>
                            <dd class="font-medium text-slate-900 text-right">
                                {{ $record->location_name ?: 'Tidak spesifik / Tingkat Nasional' }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Latitude (Garis Lintang):</dt>
                            <dd class="font-mono text-slate-900">
                                {{ $record->latitude ? number_format($record->latitude, 5) : '-' }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Longitude (Garis Bujur):</dt>
                            <dd class="font-mono text-slate-900">
                                {{ $record->longitude ? number_format($record->longitude, 5) : '-' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </section>

            <!-- Tautan Sumber Asli & Endpoint -->
            <section class="p-4 rounded-xl bg-blue-50/50 border border-blue-100 space-y-3">
                <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-blue-900">
                    Tautan Sumber Asli &amp; Keabsahan Informasi
                </h3>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="space-y-1">
                        <span class="text-slate-600 block">Penyedia: <strong>{{ $record->provider?->name ?? $record->provider_code }}</strong></span>
                        <span class="text-slate-500 text-[11px] block font-mono break-all">
                            Endpoint: {{ $record->provider?->default_endpoint ?? '-' }}
                        </span>
                    </div>
                    @if($record->source_url)
                        <a href="{{ $record->source_url }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg btn-brand-gradient text-xs font-telemetry font-bold uppercase tracking-wider shrink-0">
                            <span>Buka Dokumen Asli</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    @elseif($record->provider?->officialPortalUrl())
                        <a href="{{ $record->provider->officialPortalUrl() }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-telemetry font-bold uppercase tracking-wider shrink-0 transition">
                            <span>Buka Portal Resmi</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    @endif
                </div>
            </section>

            <!-- Transparansi Verifikasi -->
            <section class="p-4 rounded-xl bg-slate-50 border border-slate-100 text-slate-600 text-xs leading-relaxed space-y-1">
                <span class="font-telemetry text-[11px] font-bold uppercase text-slate-700 block">Status Transparansi Sistem:</span>
                <p>
                    Rekaman ini diimpor secara otomatis melalui integrasi API eksternal dan dicatat ke dalam database telemetri. Data eksternal disajikan apa adanya (as-is) untuk transparansi publik dan <em>bukan</em> merupakan klaim kejadian terverifikasi independen oleh TanggapTerang sebelum melalui telaah tim kurasi.
                </p>
            </section>
        </div>

        <!-- Footer Actions -->
        <footer class="bg-slate-50 px-5 sm:px-6 py-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('public.sources.index') }}"
               class="px-4 py-2 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-telemetry text-xs font-bold uppercase tracking-wider transition">
                &larr; Kembali ke Transparansi Sumber Data
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('public.monitor') }}"
                   class="px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-telemetry text-xs font-bold uppercase tracking-wider transition">
                    Buka Peta Pantau
                </a>
            </div>
        </footer>
    </article>
</div>
@endsection

