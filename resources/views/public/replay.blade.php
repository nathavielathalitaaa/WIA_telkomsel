@extends('layouts.app', [
    'title' => 'Mode Replay Simulasi Garis Waktu — TanggapTerang',
    'activeTab' => 'replay',
])

@php
    $hasCoordinates = $selectedIncident && $selectedIncident->latitude !== null && $selectedIncident->longitude !== null;
    $eventsArray = $timelineEvents->values()->all();
    $firstEventMs = !empty($eventsArray) ? $eventsArray[0]['timestamp_ms'] : 0;
    $lastEventMs = !empty($eventsArray) ? $eventsArray[count($eventsArray) - 1]['timestamp_ms'] : 0;
@endphp

@section('content')
<div class="space-y-8 sm:space-y-10 lg:space-y-12">
    <!-- Master Institutional Hero Header (Aligned with /welcome) -->
    <section class="bg-[#173B70] text-white rounded-2xl shadow-xs border border-[#315D91] p-5 sm:p-7 space-y-4">
        <div class="flex flex-wrap items-center gap-2">
            <span class="px-2.5 py-1 rounded bg-[#315D91] text-white border border-blue-300/30 font-telemetry text-xs font-bold uppercase tracking-wider">
                Linimasa Garis Waktu Interaktif
            </span>
            <span class="px-2.5 py-1 rounded bg-amber-400 text-[#173B70] font-telemetry text-xs font-bold uppercase tracking-wider">
                Data Historis BNPB &amp; Simulasi
            </span>
        </div>

        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 min-w-0 w-full">
            <div class="space-y-2 max-w-3xl min-w-0">
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold tracking-tight text-white leading-tight">
                    Linimasa Perkembangan Kejadian
                </h1>
                <p class="text-xs sm:text-sm text-blue-100 leading-relaxed">
                    Putar kembali kronologi kejadian dari waktu ke waktu: mulai dari <strong class="text-white">waktu kejadian riil</strong>, konfirmasi <strong class="text-white">sumber resmi BNPB</strong>, hasil <strong class="text-white">verifikasi data</strong>, hingga <strong class="text-white">catatan tindak lanjut</strong> di lapangan.
                </p>
            </div>

            <!-- Incident Selector Form -->
            @if($incidents->isNotEmpty())
                <form method="GET" action="{{ route('public.replay') }}" class="w-full max-w-full lg:w-96 min-w-0 shrink-0 bg-[#0F2A52] border border-[#315D91] rounded-xl p-3 space-y-2 overflow-hidden">
                    <label for="incident-selector" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-blue-100">
                        Pilih Kejadian untuk Mode Replay
                    </label>
                    <div class="flex items-center gap-2 min-w-0 w-full max-w-full">
                        <select id="incident-selector"
                                name="incident"
                                onchange="this.form.submit()"
                                class="flex-1 min-w-0 w-full max-w-full min-h-[40px] px-3 py-1.5 bg-white text-[#202938] rounded font-medium text-xs truncate focus:outline-none">
                            <optgroup label="Data Historis Resmi BNPB (DIBI)">
                                @foreach($incidents->where('is_simulation', false) as $inc)
                                    <option value="{{ $inc->slug }}" @selected($selectedIncident && $selectedIncident->id === $inc->id)>
                                        [HISTORIS] {{ $inc->title }}
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Skenario Mode Simulasi Prototype">
                                @foreach($incidents->where('is_simulation', true) as $inc)
                                    <option value="{{ $inc->slug }}" @selected($selectedIncident && $selectedIncident->id === $inc->id)>
                                        [SIMULASI] {{ $inc->title }} ({{ $inc->reports_count }} lap, {{ $inc->follow_up_records_count }} tindak)
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                        <button type="submit"
                                class="px-3.5 py-1.5 rounded bg-[#D94350] hover:bg-[#C23541] text-white font-telemetry text-xs font-bold uppercase cursor-pointer shrink-0 transition shadow-xs">
                            Muat
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </section>

    @if($selectedIncident && !$selectedIncident->is_simulation)
        <section class="bg-blue-50/80 border border-blue-200 rounded-xl p-3.5 flex items-start gap-2.5 text-xs text-blue-950">
            <svg class="w-4 h-4 text-blue-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <strong class="font-bold text-blue-900">Mode Replay Data Kejadian Riil (Pusdatin BNPB):</strong>
                Garis waktu di bawah ini merekonstruksi catatan riil dari arsip <strong>Data Informasi Bencana Indonesia (DIBI)</strong> Badan Nasional Penanggulangan Bencana. Kejadian historis ini tidak dihubungkan dengan laporan warga crowdsourcing untuk menjaga integritas data riil.
            </div>
        </section>
    @endif

    @if(!$selectedIncident)
        <!-- Empty State when no incidents exist in DB -->
        <section class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-8 text-center space-y-3">
            <h2 class="text-lg font-bold text-[#202938]">Belum Ada Kejadian Simulasi untuk Mode Replay</h2>
            <p class="text-sm text-[#788397] max-w-md mx-auto">
                Saat ini belum terdapat data kejadian di dalam database yang dapat ditampilkan pada garis waktu replay.
            </p>
            <a href="{{ route('public.monitor') }}"
               class="px-4 py-2 rounded-lg bg-[#173B70] hover:bg-[#0F2A52] text-white font-telemetry text-xs font-bold uppercase inline-flex items-center shadow-xs transition">
                Kembali ke Halaman Pantau
            </a>
        </section>
    @else
        <!-- Insufficient History Notice (Requirement A.7) -->
        @if(!$hasSufficientHistory)
            <section class="bg-[#FFFBEB] border-l-4 border-[#D97706] border-y border-r border-[#FCD34D] rounded-xl p-4 text-xs text-[#92400E] space-y-1" role="status">
                <div class="font-telemetry font-bold uppercase tracking-wider">
                    Pemberitahuan Riwayat Terbatas
                </div>
                <p class="leading-relaxed">
                    Kejadian <strong>{{ $selectedIncident->title }}</strong> saat ini baru memiliki {{ $timelineEvents->count() }} catatan peristiwa awal dan belum memiliki rangkaian histori laporan, verifikasi, maupun tindak lanjut tambahan yang panjang untuk diputar secara bertahap.
                </p>
            </section>
        @endif

        <!-- Interactive Playback Console & Time Scrubber -->
        <section class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 space-y-5">
            <!-- Active Replay Time & Current Statuses Banner -->
            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 min-w-0">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-lg bg-[#173B70] text-white flex items-center justify-center font-telemetry font-bold text-sm shrink-0">
                        <span id="replay-step-badge">1/{{ max(1, count($eventsArray)) }}</span>
                    </div>
                    <div class="min-w-0">
                        <div class="font-telemetry text-[11px] font-bold uppercase tracking-wider text-[#788397]">
                            Posisi Waktu Replay Saat Ini
                        </div>
                        <div id="replay-active-timestamp" class="font-telemetry text-lg sm:text-xl font-bold text-[#202938]">
                            {{ $eventsArray[0]['timestamp_formatted'] ?? '-' }}
                        </div>
                        <div id="replay-active-event-title" class="text-xs font-semibold text-[#173B70] mt-0.5">
                            {{ $eventsArray[0]['title'] ?? '-' }}
                        </div>
                    </div>
                </div>

                <!-- Dynamic Dual Status at Active Replay Time -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 shrink-0">
                    <div class="bg-white border border-slate-200 rounded-lg px-3.5 py-2">
                        <span class="font-telemetry text-[10px] font-bold uppercase text-[#788397] block">
                            1. Status Verifikasi (Saat Waktu Replay)
                        </span>
                        <span id="replay-verification-status" class="font-telemetry text-xs font-bold uppercase text-[#173B70]">
                            {{ $eventsArray[0]['verification_label'] ?? 'Menunggu Verifikasi' }}
                        </span>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-lg px-3.5 py-2">
                        <span class="font-telemetry text-[10px] font-bold uppercase text-[#788397] block">
                            2. Status Tindak Lanjut (Saat Waktu Replay)
                        </span>
                        <span id="replay-followup-status" class="font-telemetry text-xs font-bold uppercase text-[#059669]">
                            {{ $eventsArray[0]['follow_up_label'] ?? 'Belum Ditindaklanjuti' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Time Range Scrubber Slider -->
            <div class="space-y-2">
                <div class="flex flex-wrap items-center justify-between gap-2 font-telemetry text-xs text-[#788397]">
                    <span>Awal: <strong>{{ $eventsArray[0]['timestamp_formatted'] ?? '-' }}</strong></span>
                    <span class="text-[#202938] font-bold uppercase">Geser Slider Waktu untuk Melihat Informasi yang Tercatat Sampai Titik Waktu Tersebut</span>
                    <span>Akhir: <strong>{{ $eventsArray[count($eventsArray) - 1]['timestamp_formatted'] ?? '-' }}</strong></span>
                </div>

                <input type="range"
                       id="replay-time-slider"
                       min="{{ $firstEventMs }}"
                       max="{{ max($firstEventMs, $lastEventMs) }}"
                       value="{{ max($firstEventMs, $lastEventMs) }}"
                       step="1000"
                       aria-label="Penggeser Waktu Replay Simulasi"
                       class="w-full h-3 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-[#D94350]">

                <!-- Clickable Step Markers on Timeline -->
                <div class="flex flex-wrap items-center justify-between gap-1.5 pt-1">
                    @foreach($eventsArray as $idx => $ev)
                        <button type="button"
                                data-jump-ms="{{ $ev['timestamp_ms'] }}"
                                data-jump-index="{{ $idx }}"
                                data-tooltip="Lompat ke tahap #{{ $ev['step_number'] }}: {{ $ev['title'] }}"
                                class="replay-milestone-btn px-2.5 py-1 rounded border border-slate-200/90 bg-white hover:bg-slate-100 font-telemetry text-[11px] font-bold text-[#202938] transition cursor-pointer hover:shadow-xs active:scale-95">
                            #{{ $ev['step_number'] }} • {{ $ev['time_short'] }} ({{ match($ev['type']) {
                                'kejadian_awal' => 'Awal',
                                'laporan_masyarakat' => 'Laporan',
                                'sumber_terbuka' => 'Sumber',
                                'verifikasi' => 'Verifikasi',
                                'tindak_lanjut' => 'Tindak Lanjut',
                                default => 'Log'
                            } }})
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Play / Pause / Step / Reset Controls -->
            <div class="pt-2 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button"
                            id="btn-replay-reset"
                            data-tooltip="Kembalikan replay ke awal timeline"
                            class="px-3 py-2 rounded-lg border border-slate-200/90 bg-white hover:bg-slate-50 text-[#202938] font-telemetry text-xs font-bold uppercase tracking-wider cursor-pointer min-h-[42px] transition active:scale-95">
                        Reset ke Awal
                    </button>

                    <button type="button"
                            id="btn-replay-prev"
                            data-tooltip="Mundur satu peristiwa ke belakang"
                            class="px-3 py-2 rounded-lg border border-slate-200/90 bg-white hover:bg-slate-50 text-[#202938] font-telemetry text-xs font-bold uppercase tracking-wider cursor-pointer min-h-[42px] transition active:scale-95">
                        Tahap Sebelumnya
                    </button>

                    <button type="button"
                            id="btn-replay-play"
                            data-tooltip="Mulai / jeda simulasi otomatis"
                            class="px-5 py-2 rounded-lg bg-[#D94350] hover:bg-[#C23541] text-white font-telemetry text-xs font-bold uppercase tracking-wider cursor-pointer min-h-[42px] inline-flex items-center gap-2 transition active:scale-95 shadow-xs">
                        <span id="btn-replay-play-label">Putar Replay</span>
                    </button>

                    <button type="button"
                            id="btn-replay-next"
                            data-tooltip="Maju satu peristiwa ke depan"
                            class="px-3 py-2 rounded-lg border border-slate-200/90 bg-white hover:bg-slate-50 text-[#202938] font-telemetry text-xs font-bold uppercase tracking-wider cursor-pointer min-h-[42px] transition active:scale-95">
                        Tahap Berikutnya
                    </button>
                </div>

                <!-- Playback Speed Controls -->
                <div class="flex items-center gap-1.5 font-telemetry text-xs">
                    <span class="text-[#788397] uppercase font-bold mr-1">Kecepatan:</span>
                    <button type="button" data-speed="1" data-tooltip="Kecepatan normal (1x)" class="replay-speed-btn px-2.5 py-1 rounded bg-[#173B70] text-white font-bold cursor-pointer transition">1x</button>
                    <button type="button" data-speed="2" data-tooltip="Kecepatan 2x" class="replay-speed-btn px-2.5 py-1 rounded bg-slate-100 text-[#202938] font-bold cursor-pointer transition hover:bg-slate-200">2x</button>
                    <button type="button" data-speed="4" data-tooltip="Kecepatan 4x" class="replay-speed-btn px-2.5 py-1 rounded bg-slate-100 text-[#202938] font-bold cursor-pointer transition hover:bg-slate-200">4x</button>
                </div>
            </div>
        </section>

        <!-- Dynamic Summary Panel (Real DB Counts Accumulated Up to Active Replay Time — No Invented Metrics) -->
        <section aria-label="Ringkasan Akumulasi Informasi pada Waktu Replay" class="flex overflow-x-auto snap-x snap-mandatory gap-3.5 pb-2 -mx-4 px-4 sm:mx-0 sm:px-0 scrollbar-none sm:grid sm:grid-cols-2 lg:grid-cols-4">
            <div class="bg-white rounded-xl shadow-xs border border-slate-100/90 p-4 w-[74vw] max-w-[260px] shrink-0 snap-start sm:w-auto card-interactive"
                 data-tooltip="Total log peristiwa yang telah diputar">
                <span class="font-telemetry text-[11px] font-bold uppercase tracking-wider text-[#788397] block">
                    Peristiwa Tercatat s/d Waktu Ini
                </span>
                <div class="mt-2 flex items-baseline justify-between">
                    <span id="metric-visible-events" class="font-telemetry text-2xl sm:text-3xl font-bold text-[#202938]">
                        {{ count($eventsArray) }}
                    </span>
                    <span class="font-telemetry text-xs text-[#788397]">dari {{ count($eventsArray) }} total log</span>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-xs border border-slate-100/90 p-4 w-[74vw] max-w-[260px] shrink-0 snap-start sm:w-auto card-interactive"
                 data-tooltip="Laporan masuk sampai waktu ini">
                <span class="font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-500 block">
                    Laporan Warga Masuk s/d Waktu Ini
                </span>
                <div class="mt-2 flex items-baseline justify-between">
                    <span id="metric-visible-reports" class="font-telemetry text-2xl sm:text-3xl font-bold text-[#2563EB]">
                        {{ collect($eventsArray)->where('type', 'laporan_masyarakat')->count() }}
                    </span>
                    <span class="font-telemetry text-xs text-slate-500">Laporan Masyarakat</span>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-xs border border-slate-100/90 p-4 w-[74vw] max-w-[260px] shrink-0 snap-start sm:w-auto card-interactive"
                 data-tooltip="Rujukan sumber terbuka yang dicatat">
                <span class="font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-500 block">
                    Sumber Terbuka Dirujuk s/d Waktu Ini
                </span>
                <div class="mt-2 flex items-baseline justify-between">
                    <span id="metric-visible-sources" class="font-telemetry text-2xl sm:text-3xl font-bold text-[#D97706]">
                        {{ collect($eventsArray)->where('type', 'sumber_terbuka')->count() }}
                    </span>
                    <span class="font-telemetry text-xs text-slate-500">Rujukan Terbuka</span>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-xs border border-slate-100/90 p-4 w-[74vw] max-w-[260px] shrink-0 snap-start sm:w-auto card-interactive"
                 data-tooltip="Aksi dan tindak lanjut tim lapangan">
                <span class="font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-500 block">
                    Catatan Tindak Lanjut s/d Waktu Ini
                </span>
                <div class="mt-2 flex items-baseline justify-between">
                    <span id="metric-visible-followups" class="font-telemetry text-2xl sm:text-3xl font-bold text-[#059669]">
                        {{ collect($eventsArray)->where('type', 'tindak_lanjut')->count() }}
                    </span>
                    <span class="font-telemetry text-xs text-slate-500">Tindak Lanjut Tim</span>
                </div>
            </div>
        </section>

        <!-- Main Grid: Map (Left) & Chronological Event Stream Filtered by Replay Time (Right) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
            <!-- Left Column (5 cols): Incident Info & Map -->
            <div class="lg:col-span-5 space-y-5">
                <section class="bg-white rounded-2xl shadow-xs border border-slate-100/90 overflow-hidden">
                    <div class="p-4 border-b border-slate-100 bg-[#F8F9FC] flex items-center justify-between">
                        <div>
                            <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                                Peta Lokasi Kejadian &amp; Titik Laporan
                            </h2>
                            <p class="text-xs text-[#788397]">{{ $selectedIncident->location_name }}</p>
                        </div>
                        <x-severity-badge :severity="$selectedIncident->severity" />
                    </div>

                    @if($hasCoordinates)
                        <div id="replay-map" class="w-full h-[300px] bg-slate-100 z-10"></div>
                    @else
                        <div class="p-6 text-center space-y-2 bg-slate-50">
                            <p class="text-sm font-bold text-[#202938]">Koordinat Peta Tidak Tersedia</p>
                            <p class="text-xs text-[#788397]">
                                Kejadian ini tidak memiliki koordinat lintang/bujur di database. Sistem tidak menampilkan koordinat palsu.
                            </p>
                        </div>
                    @endif

                    <div class="p-4 border-t border-slate-100 space-y-2 text-xs text-slate-600">
                        <div class="flex items-center justify-between">
                            <span>Kejadian Terpilih:</span>
                            <a href="{{ route('public.incidents.show', $selectedIncident->slug) }}" class="font-telemetry font-bold link-text">
                                Buka halaman detail
                            </a>
                        </div>
                        <p class="text-[11px] text-[#788397]">
                            Catatan: Mode replay ini hanya memvisualisasikan catatan laporan, sumber terbuka, verifikasi, dan tindak lanjut yang benar-benar ada pada database.
                        </p>
                    </div>
                </section>
            </div>

            <!-- Right Column (7 cols): Chronological Timeline Events (Filtered by Active Slider Timestamp) -->
            <div class="lg:col-span-7 space-y-4">
                <section class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
                        <div>
                            <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                                Garis Waktu Peristiwa Informasi ({{ $selectedIncident->title }})
                            </h2>
                            <p class="text-xs text-[#788397]">
                                Hanya menampilkan peristiwa yang sudah tercatat hingga posisi waktu slider saat ini.
                            </p>
                        </div>
                        <span id="future-hidden-indicator" class="hidden px-2.5 py-1 rounded bg-amber-100 text-amber-800 border border-amber-300 font-telemetry text-[11px] font-bold">
                            0 peristiwa berikutnya belum tercapai
                        </span>
                    </div>

                    <div id="replay-events-container" class="space-y-3">
                        @foreach($eventsArray as $ev)
                            @php
                                $badgeClasses = match($ev['type']) {
                                    'kejadian_awal' => 'bg-[#173B70] text-white',
                                    'laporan_masyarakat' => 'bg-[#EFF6FF] text-[#173B70] border border-[#93C5FD]',
                                    'sumber_terbuka' => 'bg-[#FFFBEB] text-[#D97706] border border-[#FCD34D]',
                                    'verifikasi' => 'bg-blue-600 text-white',
                                    'tindak_lanjut' => 'bg-[#ECFDF5] text-[#059669] border border-[#6EE7B7]',
                                    default => 'bg-slate-100 text-[#202938]',
                                };
                            @endphp
                            <article class="replay-event-card border border-slate-100/90 rounded-xl p-4 bg-white transition"
                                     data-event-uid="{{ $ev['uid'] }}"
                                     data-event-type="{{ $ev['type'] }}"
                                     data-timestamp-ms="{{ $ev['timestamp_ms'] }}"
                                     data-step-number="{{ $ev['step_number'] }}">
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-1.5">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="w-6 h-6 rounded-full bg-[#173B70] text-white font-telemetry text-xs font-bold inline-flex items-center justify-center">
                                            {{ $ev['step_number'] }}
                                        </span>
                                        <span class="font-telemetry text-xs font-bold text-[#202938]">
                                            {{ $ev['timestamp_formatted'] }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase {{ $badgeClasses }}">
                                            {{ $ev['type_label'] }}
                                        </span>
                                    </div>
                                    <span class="font-telemetry text-[10px] text-[#788397] uppercase">
                                        Simulasi
                                    </span>
                                </div>

                                <h3 class="text-sm sm:text-base font-bold text-[#202938]">
                                    {{ $ev['title'] }}
                                </h3>

                                <p class="text-xs sm:text-sm text-slate-700 mt-1 leading-relaxed">
                                    {{ $ev['description'] }}
                                </p>

                                <div class="mt-2.5 pt-2 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 font-telemetry text-xs text-slate-500">
                                    <span>Sumber / Pencatat: <strong class="text-slate-700">{{ $ev['source_label'] }}</strong></span>
                                    @if($ev['verification_label'])
                                        <span class="text-[#2563EB] font-bold">{{ $ev['verification_label'] }}</span>
                                    @elseif($ev['follow_up_label'])
                                        <span class="text-[#059669] font-bold">Status Tindak Lanjut: {{ $ev['follow_up_label'] }}</span>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            </div>
        </div>
    @endif
</div>
@endsection

@if($selectedIncident && !empty($eventsArray))
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const events = @json($eventsArray);
            const slider = document.getElementById('replay-time-slider');
            const stepBadge = document.getElementById('replay-step-badge');
            const activeTimeEl = document.getElementById('replay-active-timestamp');
            const activeTitleEl = document.getElementById('replay-active-event-title');
            const verifStatusEl = document.getElementById('replay-verification-status');
            const followUpStatusEl = document.getElementById('replay-followup-status');

            const metricEventsEl = document.getElementById('metric-visible-events');
            const metricReportsEl = document.getElementById('metric-visible-reports');
            const metricSourcesEl = document.getElementById('metric-visible-sources');
            const metricFollowUpsEl = document.getElementById('metric-visible-followups');
            const futureIndicatorEl = document.getElementById('future-hidden-indicator');

            const btnReset = document.getElementById('btn-replay-reset');
            const btnPrev = document.getElementById('btn-replay-prev');
            const btnPlay = document.getElementById('btn-replay-play');
            const btnPlayLabel = document.getElementById('btn-replay-play-label');
            const btnNext = document.getElementById('btn-replay-next');
            const milestoneBtns = document.querySelectorAll('.replay-milestone-btn');
            const speedBtns = document.querySelectorAll('.replay-speed-btn');
            const eventCards = document.querySelectorAll('.replay-event-card');

            if (!slider || events.length === 0) return;

            // Optional Leaflet map integration
            let map = null;
            const reportMarkers = [];
            @if($hasCoordinates)
                const mapEl = document.getElementById('replay-map');
                if (mapEl && typeof window.L !== 'undefined') {
                    const incLat = {{ (float) $selectedIncident->latitude }};
                    const incLng = {{ (float) $selectedIncident->longitude }};
                    map = window.L.map(mapEl, { scrollWheelZoom: false }).setView([incLat, incLng], 14);
                    window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 18,
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    window.L.circleMarker([incLat, incLng], {
                        radius: 10,
                        fillColor: '#DC2626',
                        color: '#FFFFFF',
                        weight: 3,
                        opacity: 1,
                        fillOpacity: 0.95,
                    }).addTo(map).bindPopup(@json('Titik Kejadian: ' . $selectedIncident->title));

                    events.forEach(ev => {
                        if (ev.type === 'laporan_masyarakat' && typeof ev.latitude === 'number' && typeof ev.longitude === 'number') {
                            const m = window.L.circleMarker([ev.latitude, ev.longitude], {
                                radius: 7,
                                fillColor: '#2563EB',
                                color: '#FFFFFF',
                                weight: 2,
                                opacity: 1,
                                fillOpacity: 0.9,
                            }).bindPopup(`${ev.type_label}: ${ev.title}`);
                            reportMarkers.push({ timestamp_ms: ev.timestamp_ms, marker: m });
                        }
                    });
                }
            @endif

            let currentIndex = events.length - 1;
            let isPlaying = false;
            let playTimer = null;
            let speedMultiplier = 1;

            const renderUpToTimestamp = (cutoffMs) => {
                let latestReachedIndex = 0;
                let currentVerifLabel = 'Menunggu Verifikasi';
                let currentFollowUpLabel = 'Belum Ditindaklanjuti';
                let visibleCount = 0;
                let reportsCount = 0;
                let sourcesCount = 0;
                let followUpsCount = 0;

                events.forEach((ev, idx) => {
                    if (ev.timestamp_ms <= cutoffMs) {
                        latestReachedIndex = idx;
                        visibleCount++;
                        if (ev.type === 'laporan_masyarakat') reportsCount++;
                        if (ev.type === 'sumber_terbuka') sourcesCount++;
                        if (ev.type === 'tindak_lanjut') followUpsCount++;

                        if ((ev.type === 'kejadian_awal' || ev.type === 'verifikasi') && ev.verification_label) {
                            currentVerifLabel = ev.verification_label;
                        }
                        if ((ev.type === 'kejadian_awal' || ev.type === 'tindak_lanjut') && ev.follow_up_label) {
                            currentFollowUpLabel = ev.follow_up_label;
                        }
                    }
                });

                currentIndex = latestReachedIndex;
                const activeEv = events[latestReachedIndex];

                if (stepBadge) stepBadge.textContent = `${latestReachedIndex + 1}/${events.length}`;
                if (activeTimeEl && activeEv) activeTimeEl.textContent = activeEv.timestamp_formatted;
                if (activeTitleEl && activeEv) activeTitleEl.textContent = `Tahap #${activeEv.step_number}: ${activeEv.title}`;
                if (verifStatusEl) verifStatusEl.textContent = currentVerifLabel;
                if (followUpStatusEl) followUpStatusEl.textContent = currentFollowUpLabel;

                if (metricEventsEl) metricEventsEl.textContent = String(visibleCount);
                if (metricReportsEl) metricReportsEl.textContent = String(reportsCount);
                if (metricSourcesEl) metricSourcesEl.textContent = String(sourcesCount);
                if (metricFollowUpsEl) metricFollowUpsEl.textContent = String(followUpsCount);

                const hiddenFutureCount = events.length - visibleCount;
                if (futureIndicatorEl) {
                    if (hiddenFutureCount > 0) {
                        futureIndicatorEl.textContent = `${hiddenFutureCount} peristiwa berikutnya belum terjadi pada waktu ini`;
                        futureIndicatorEl.classList.remove('hidden');
                    } else {
                        futureIndicatorEl.classList.add('hidden');
                    }
                }

                // Tampilkan HANYA kartu peristiwa yang sudah tercatat sampai cutoffMs
                eventCards.forEach(card => {
                    const cardMs = Number(card.getAttribute('data-timestamp-ms'));
                    const cardStep = Number(card.getAttribute('data-step-number'));
                    if (cardMs <= cutoffMs) {
                        card.classList.remove('hidden');
                        if (cardStep === latestReachedIndex + 1) {
                            card.classList.add('ring-2', 'ring-[#173B70]', 'bg-[#F8F9FC]');
                            if (window.innerWidth >= 1024 && !isPlaying) {
                                card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                            }
                        } else {
                            card.classList.remove('ring-2', 'ring-[#173B70]', 'bg-[#F8F9FC]');
                        }
                    } else {
                        card.classList.add('hidden');
                        card.classList.remove('ring-2', 'ring-[#173B70]', 'bg-[#F8F9FC]');
                    }
                });

                // Highlight tombol milestone
                milestoneBtns.forEach((btn, idx) => {
                    if (idx === latestReachedIndex) {
                        btn.classList.add('bg-[#173B70]', 'text-white');
                        btn.classList.remove('bg-white', 'text-[#202938]');
                    } else {
                        btn.classList.remove('bg-[#173B70]', 'text-white');
                        btn.classList.add('bg-white', 'text-[#202938]');
                    }
                });

                // Sinkronkan marker laporan warga pada peta sesuai cutoffMs
                if (map) {
                    reportMarkers.forEach(item => {
                        if (item.timestamp_ms <= cutoffMs) {
                            if (!map.hasLayer(item.marker)) {
                                item.marker.addTo(map);
                            }
                        } else {
                            if (map.hasLayer(item.marker)) {
                                map.removeLayer(item.marker);
                            }
                        }
                    });
                }
            };

            const jumpToStepIndex = (idx) => {
                const clamped = Math.max(0, Math.min(events.length - 1, idx));
                const targetMs = events[clamped].timestamp_ms;
                slider.value = String(targetMs);
                renderUpToTimestamp(targetMs);
            };

            const stopPlayback = () => {
                isPlaying = false;
                if (btnPlayLabel) btnPlayLabel.innerHTML = '&#9654; Putar Replay';
                if (playTimer) {
                    clearInterval(playTimer);
                    playTimer = null;
                }
            };

            const startPlayback = () => {
                if (currentIndex >= events.length - 1) {
                    jumpToStepIndex(0);
                }
                isPlaying = true;
                if (btnPlayLabel) btnPlayLabel.innerHTML = '&#10074;&#10074; Jeda Replay';

                const delayMs = Math.max(350, Math.round(1400 / speedMultiplier));
                playTimer = setInterval(() => {
                    if (currentIndex >= events.length - 1) {
                        stopPlayback();
                        return;
                    }
                    jumpToStepIndex(currentIndex + 1);
                }, delayMs);
            };

            slider.addEventListener('input', (e) => {
                stopPlayback();
                renderUpToTimestamp(Number(e.target.value));
            });

            btnReset?.addEventListener('click', () => {
                stopPlayback();
                jumpToStepIndex(0);
            });

            btnPrev?.addEventListener('click', () => {
                stopPlayback();
                jumpToStepIndex(currentIndex - 1);
            });

            btnNext?.addEventListener('click', () => {
                stopPlayback();
                jumpToStepIndex(currentIndex + 1);
            });

            btnPlay?.addEventListener('click', () => {
                if (isPlaying) {
                    stopPlayback();
                } else {
                    startPlayback();
                }
            });

            milestoneBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    stopPlayback();
                    const idx = Number(btn.getAttribute('data-jump-index'));
                    jumpToStepIndex(idx);
                });
            });

            speedBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    speedMultiplier = Number(btn.getAttribute('data-speed')) || 1;
                    speedBtns.forEach(b => {
                        b.classList.remove('bg-[#173B70]', 'text-white');
                        b.classList.add('bg-slate-100', 'text-slate-700');
                    });
                    btn.classList.add('bg-[#173B70]', 'text-white');
                    btn.classList.remove('bg-slate-100', 'text-slate-700');

                    if (isPlaying) {
                        stopPlayback();
                        startPlayback();
                    }
                });
            });

            // Render initial state
            renderUpToTimestamp(Number(slider.value));
        });
    </script>
    @endpush
@endif
