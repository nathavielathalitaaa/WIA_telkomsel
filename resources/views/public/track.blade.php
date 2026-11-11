@extends('layouts.app', [
    'title' => 'Lacak Status Tiket Laporan — TanggapTerang',
    'activeTab' => 'lacak',
])

@section('content')
<div class="space-y-8 sm:space-y-10 lg:space-y-12">
    <!-- Ticket Created Success Banner (Shown right after citizen submits a report) -->
    @if(session('created_ticket'))
        <section class="bg-[#ECFDF5] border border-emerald-300 rounded-2xl shadow-xs p-5 space-y-3" role="status">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <span class="px-2.5 py-1 rounded bg-[#059669] text-white font-telemetry text-xs font-bold uppercase tracking-wider">
                    Laporan Berhasil Diterima
                </span>
                <span class="font-telemetry text-xs text-[#065F46] font-semibold">
                    Status: MENUNGGU VERIFIKASI
                </span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-[#6EE7B7] rounded-xl p-4">
                <div>
                    <div class="font-telemetry text-[11px] font-bold uppercase tracking-wider text-[#788397]">
                        Nomor Tiket Anda
                    </div>
                    <div class="font-telemetry text-2xl sm:text-3xl font-bold text-[#202938] tracking-wider mt-0.5 select-all">
                        {{ session('created_ticket') }}
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Simpan nomor tiket ini untuk melacak status verifikasi dan tindak lanjut kapan saja tanpa perlu login.
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button"
                            data-copy-text="{{ session('created_ticket') }}"
                            data-tooltip="Salin nomor tiket ke clipboard"
                            class="px-4 py-2.5 rounded-lg bg-[#059669] hover:bg-emerald-700 text-white font-telemetry text-xs font-bold uppercase tracking-wider transition inline-flex items-center gap-2 cursor-pointer shadow-xs active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span>Salin Nomor Tiket</span>
                    </button>
                </div>
            </div>
        </section>
    @endif

    <!-- 1. Header & 2. Deskripsi & 3. Search Form (Clean Editorial Composition) -->
    <section class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 sm:p-7 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#173B70]/10 text-[#173B70] font-telemetry">
                        Pelacakan Mandiri
                    </span>
                    <span class="text-xs text-slate-400">•</span>
                    <span class="text-xs text-slate-500 font-telemetry">Tanpa Perlu Login</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#202938]">
                    Lacak Tiket
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 max-w-2xl leading-relaxed">
                    Lacak verifikasi, keterkaitan kejadian, dan perkembangan tindak lanjut melalui nomor tiket unik.
                </p>
            </div>

            <div class="shrink-0">
                <a href="{{ route('public.reports.create') }}"
                   class="px-4 py-2 rounded-lg btn-brand-gradient text-white font-telemetry text-xs font-bold uppercase tracking-wider inline-flex items-center gap-1.5 shadow-xs transition min-h-[38px]">
                    <span class="font-extrabold text-sm leading-none">+</span>
                    <span>Buat Laporan Baru</span>
                </a>
            </div>
        </div>

        <!-- Input Nomor Tiket dan Tombol Pencarian -->
        <form method="GET" action="{{ route('public.track') }}" class="space-y-3">
            <div class="flex flex-col sm:flex-row gap-2.5">
                <div class="flex-1 relative">
                    <label for="ticket-input" class="sr-only">Nomor Tiket Laporan</label>
                    <input type="text"
                           id="ticket-input"
                           name="ticket"
                           value="{{ $ticketQuery }}"
                           required
                           maxlength="32"
                           placeholder="Masukkan nomor tiket (contoh: RPT-SIM-8K4M9P2X atau RSP-SIM-7M2K8N4Q)"
                           class="w-full min-h-[44px] px-4 py-2 bg-slate-50/60 border border-slate-200 rounded-xl font-telemetry text-sm font-bold uppercase tracking-wider placeholder:font-normal placeholder:normal-case placeholder:text-slate-400 focus:outline-none focus:bg-white focus:border-[#173B70]">
                </div>
                <button type="submit"
                        class="px-6 py-2 rounded-xl bg-[#173B70] hover:bg-[#0F2A52] text-white font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[44px] inline-flex items-center justify-center gap-2 shadow-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Cari Tiket</span>
                </button>
            </div>

            <!-- Monochrome Sample Ticket Chips -->
            <div class="flex flex-wrap items-center gap-1.5 pt-1 text-xs font-telemetry text-slate-500">
                <span class="font-semibold text-slate-400 text-[11px] uppercase mr-1">Contoh Tiket:</span>
                <a href="{{ route('public.track', ['ticket' => 'RPT-SIM-8K4M9P2X']) }}"
                   class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 transition text-[11px]">
                    RPT-SIM-8K4M9P2X (Terverifikasi)
                </a>
                <a href="{{ route('public.track', ['ticket' => 'RPT-SIM-6T9W4L8R']) }}"
                   class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 transition text-[11px]">
                    RPT-SIM-6T9W4L8R (Menunggu)
                </a>
                <a href="{{ route('public.track', ['ticket' => 'RPT-SIM-9P3F6Y2M']) }}"
                   class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 transition text-[11px]">
                    RPT-SIM-9P3F6Y2M (Ditolak)
                </a>
                <span class="text-slate-300">|</span>
                <a href="{{ route('public.track', ['ticket' => 'RSP-SIM-7M2K8N4Q']) }}"
                   class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 transition text-[11px]">
                    RSP-SIM-7M2K8N4Q (Respons)
                </a>
                <a href="{{ route('public.track', ['ticket' => 'RSP-SIM-9V5P3W6X']) }}"
                   class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 transition text-[11px]">
                    RSP-SIM-9V5P3W6X (Penyaluran)
                </a>
            </div>
        </form>
    </section>

    <!-- State Sebelum Pencarian (Bersih & Minimalis tanpa tumpukan kotak) -->
    @if(!$searched)
        <div class="bg-slate-50/60 rounded-2xl border border-slate-200/60 p-8 text-center space-y-2">
            <div class="w-10 h-10 rounded-full bg-slate-200/80 text-slate-400 mx-auto flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-600">
                Masukkan nomor tiket pada kolom di atas untuk melihat status verifikasi dan perkembangan laporan Anda.
            </p>
            <p class="text-xs text-slate-400">
                Format nomor tiket: <code class="font-mono bg-white px-1.5 py-0.5 rounded border border-slate-200 text-slate-600">RPT-SIM-XXXXXX</code> atau <code class="font-mono bg-white px-1.5 py-0.5 rounded border border-slate-200 text-slate-600">RSP-SIM-XXXXXX</code>
            </p>
        </div>
    @else
        <!-- Hasil Pencarian -->
        @if(!$report && !$aidResponse)
            <!-- Safe Not-Found Message -->
            <section class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-8 text-center space-y-3" role="alert">
                <div class="w-12 h-12 rounded-full bg-amber-50 text-[#D97706] mx-auto flex items-center justify-center border border-amber-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/>
                    </svg>
                </div>
                <h2 class="text-base sm:text-lg font-bold text-[#202938]">
                    Nomor Tiket / Kode Referensi Tidak Ditemukan
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto leading-relaxed">
                    Kode tiket <code class="font-telemetry font-bold text-[#202938] bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">{{ $ticketQuery }}</code> tidak terdaftar pada sistem. Periksa kembali huruf dan angka kode tiket Anda.
                </p>
                <div class="pt-2">
                    <a href="{{ route('public.track') }}"
                       class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-telemetry text-xs font-bold uppercase tracking-wider inline-flex items-center transition">
                        Cari Nomor Lain
                    </a>
                </div>
            </section>
        @elseif($aidResponse)
            @php
                $needLabel = \App\Models\AidResponse::categoryLabel($aidResponse->need_category);
                $isDeliveredSubmission = $aidResponse->isReportedDelivered();

                $aStatus = $aidResponse->status instanceof \BackedEnum ? $aidResponse->status->value : (string) $aidResponse->status;
                $aVerif = $aidResponse->verification_status instanceof \BackedEnum ? $aidResponse->verification_status->value : (string) $aidResponse->verification_status;

                if ($aStatus === 'terkonfirmasi') {
                    $aidStep = 5;
                } elseif ($aStatus === 'dalam_penyaluran') {
                    $aidStep = 4;
                } elseif ($aVerif === 'terverifikasi') {
                    $aidStep = 3;
                } elseif ($aVerif === 'menunggu_verifikasi') {
                    $aidStep = 2;
                } else {
                    $aidStep = 1;
                }
            @endphp

            <!-- Progress Alur Respons Bantuan -->
            <x-response-pipeline
                :activeStep="$aidStep"
                title="Progres Penanganan Respons Bantuan Ini"
                subtitle="Tahapan dari pencatatan respons bantuan, pemeriksaan petugas, verifikasi bukti, hingga konfirmasi penyerahan" />

            <!-- Informasi Hasil Pencarian: Sesuai Urutan Instruksi -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                <!-- Left Column (7 cols): Identitas, Status Verifikasi, Keterkaitan, Status Tindak Lanjut -->
                <div class="lg:col-span-7 space-y-4">
                    <!-- 1. Identitas Tiket Respons -->
                    <section class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-1 rounded bg-[#173B70] text-white font-telemetry text-xs font-bold tracking-wider inline-flex items-center gap-1.5">
                                    <span>{{ $aidResponse->reference_code }}</span>
                                    <button type="button"
                                            data-copy-text="{{ $aidResponse->reference_code }}"
                                            data-tooltip="Salin kode respons"
                                            class="text-blue-200 hover:text-white transition p-0.5 cursor-pointer"
                                            aria-label="Salin kode respons">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </span>
                                @if($aidResponse->is_simulation)
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-300 font-telemetry text-[10px] font-bold uppercase">
                                        Data Simulasi
                                    </span>
                                @endif
                                <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase {{ $isDeliveredSubmission ? 'bg-indigo-50 text-indigo-900 border border-indigo-200' : 'bg-blue-50 text-[#173B70] border border-blue-200' }}">
                                    {{ $aidResponse->submissionTypeLabel() }}
                                </span>
                            </div>

                            <span class="font-telemetry text-xs text-slate-400">
                                {{ $aidResponse->recorded_at?->format('d M Y, H:i') }} WIB
                            </span>
                        </div>

                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-[#202938]">
                                {{ $aidResponse->aid_item_title }}
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Kategori: <strong class="text-slate-700">{{ $needLabel }}</strong> • Kuantitas: <strong class="text-slate-700">{{ $aidResponse->quantity_label }}</strong>
                            </p>
                            @if($aidResponse->aid_description)
                                <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed whitespace-pre-line">{{ $aidResponse->aid_description }}</p>
                            @endif
                        </div>

                        <!-- 2. Status Verifikasi & 4. Status Tindak Lanjut (Dua Status Dibedakan Secara Visual) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            <!-- Status Verifikasi (Pemeriksaan Informasi) -->
                            <div class="bg-blue-50/40 border border-blue-200/70 rounded-xl p-3.5 space-y-1.5">
                                <span class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-[#173B70] block">
                                    Status Verifikasi Informasi
                                </span>
                                <div>
                                    <x-verification-badge :status="$aidResponse->verification_status" />
                                </div>
                                <p class="text-xs text-slate-600 leading-snug">
                                    @if($aidResponse->isVerified())
                                        Informasi telah diperiksa dan dinyatakan <strong>Terverifikasi</strong> oleh Tim Verifikator.
                                    @elseif($aidResponse->isRejected())
                                        Pemeriksaan respons dinyatakan <strong>Ditolak</strong> oleh Tim Verifikator.
                                    @else
                                        Menunggu pemeriksaan dan verifikasi kelayakan oleh Tim Verifikator.
                                    @endif
                                </p>
                                @if($aidResponse->verification_notes)
                                    <div class="text-[11px] bg-white border border-blue-200 rounded p-2 text-slate-700 mt-1">
                                        <span class="font-telemetry font-bold uppercase text-[9px] text-[#173B70] block">Catatan Verifikator:</span>
                                        {{ $aidResponse->verification_notes }}
                                    </div>
                                @endif
                            </div>

                            <!-- Status Tindak Lanjut (Operasional Bantuan) -->
                            <div class="bg-emerald-50/40 border border-emerald-200/70 rounded-xl p-3.5 space-y-1.5">
                                <span class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-emerald-800 block">
                                    Status Tindak Lanjut Bantuan
                                </span>
                                <div>
                                    <x-aid-status-badge :status="$aidResponse->status" />
                                </div>
                                <p class="text-xs text-slate-600 leading-snug">
                                    @if($aidResponse->isConfirmed())
                                        Penyerahan bantuan telah <strong>Dikonfirmasi</strong> di lapangan berdasarkan verifikasi bukti serah terima.
                                    @elseif($aidResponse->isDelivering())
                                        Bantuan saat ini sedang dalam proses <strong>Penyaluran</strong> ke lokasi tujuan.
                                    @else
                                        Bantuan berstatus <strong>Direncanakan</strong> dalam sistem koordinasi.
                                    @endif
                                </p>
                                @if($aidResponse->confirmation_notes)
                                    <div class="text-[11px] bg-white border border-emerald-200 rounded p-2 text-slate-700 mt-1">
                                        <span class="font-telemetry font-bold uppercase text-[9px] text-emerald-800 block">Catatan Lapangan:</span>
                                        {{ $aidResponse->confirmation_notes }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Metadata Singkat -->
                        <div class="grid grid-cols-2 gap-2 text-xs text-slate-600 pt-1 border-t border-slate-100">
                            <div>
                                <span class="text-slate-400 font-telemetry text-[10px] uppercase block">Tujuan Penyaluran:</span>
                                <span class="font-semibold text-slate-700">{{ $aidResponse->target_location }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 font-telemetry text-[10px] uppercase block">Pemberi Respons:</span>
                                <span class="font-semibold text-slate-700">{{ $aidResponse->publicResponderName() }}</span>
                            </div>
                        </div>

                        <!-- 6. Informasi Sumber & Dokumentasi Pendukung -->
                        @if($aidResponse->evidence_url)
                            <div class="pt-2 border-t border-slate-100">
                                <a href="{{ $aidResponse->evidence_url }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="font-telemetry text-xs font-bold text-[#173B70] hover:underline inline-flex items-center gap-1">
                                    <span>Buka Tautan Bukti Dokumentasi Lapangan</span>
                                    <span>&nearr;</span>
                                </a>
                            </div>
                        @endif
                    </section>

                    <!-- 3. Keterkaitan Kejadian -->
                    @if($aidResponse->incident)
                        <section class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4 sm:p-5 space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                                <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                                    Kejadian Bencana Terkait
                                </h3>
                                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-telemetry text-[10px] font-bold uppercase">
                                    Terhubung
                                </span>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <x-severity-badge :severity="$aidResponse->incident->severity" />
                                    <span class="text-xs text-slate-400 font-telemetry">
                                        {{ $aidResponse->incident->occurred_at?->format('d M Y, H:i') }} WIB
                                    </span>
                                </div>
                                <h4 class="text-base font-bold text-[#202938]">
                                    <a href="{{ route('public.incidents.show', $aidResponse->incident->slug) }}" class="hover:text-[#173B70]">
                                        {{ $aidResponse->incident->title }}
                                    </a>
                                </h4>
                                <p class="text-xs text-slate-500">
                                    Lokasi: {{ $aidResponse->incident->location_name }}
                                </p>
                            </div>

                            <div class="pt-2">
                                <a href="{{ route('public.incidents.show', $aidResponse->incident->slug) }}"
                                   class="px-3.5 py-1.5 rounded-lg bg-[#173B70] hover:bg-[#0F2A52] text-white font-telemetry text-xs font-bold uppercase tracking-wider inline-flex items-center gap-1.5 transition">
                                    <span>Lihat Detail Kejadian</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </section>
                    @endif
                </div>

                <!-- Right Column (5 cols): 5. Timeline Perkembangan (Audit Trail) -->
                <div class="lg:col-span-5 space-y-4">
                    <section class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                                Riwayat Perkembangan Respons
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Jejak transparan verifikasi dan operasional bantuan.
                            </p>
                        </div>

                        <x-audit-timeline
                            :verificationHistories="collect()"
                            :followUpRecords="collect()"
                            :aidResponses="collect([$aidResponse])"
                            :isInternal="false"
                        />
                    </section>
                </div>
            </div>
        @else
            @php
                $categoryLabel = $categories[$report->category] ?? ucwords(str_replace('_', ' ', $report->category));
                $rVerif = $report->verification_status instanceof \BackedEnum ? $report->verification_status->value : (string) ($report->verification_status ?? '');
                $rFollowUp = $report->incident?->follow_up_status instanceof \BackedEnum ? $report->incident->follow_up_status->value : (string) ($report->incident?->follow_up_status ?? '');

                if ($report->incident && $rFollowUp === 'selesai') {
                    $reportStep = 5;
                } elseif ($report->incident && in_array($rFollowUp, ['sedang_ditindaklanjuti', 'dipantau'])) {
                    $reportStep = 4;
                } elseif ($rVerif === 'terverifikasi') {
                    $reportStep = 3;
                } elseif ($rVerif === 'menunggu_verifikasi') {
                    $reportStep = 2;
                } else {
                    $reportStep = 1;
                }
            @endphp

            <!-- Progress Alur Penanganan Laporan Warga -->
            <x-response-pipeline
                :activeStep="$reportStep"
                title="Progres Penanganan Laporan Warga Ini"
                subtitle="Tahapan dari penerimaan laporan, pemeriksaan awal, verifikasi silang, penautan kejadian, hingga konfirmasi penyelesaian" />

            <!-- Informasi Hasil Pencarian: Sesuai Urutan Instruksi -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                <!-- Left Column (7 cols): Identitas, Status Verifikasi, Keterkaitan, Status Tindak Lanjut -->
                <div class="lg:col-span-7 space-y-4">
                    <!-- 1. Identitas Tiket Laporan Warga -->
                    <section class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-1 rounded bg-[#173B70] text-white font-telemetry text-xs font-bold tracking-wider inline-flex items-center gap-1.5">
                                    <span>{{ $report->ticket_number }}</span>
                                    <button type="button"
                                            data-copy-text="{{ $report->ticket_number }}"
                                            data-tooltip="Salin nomor tiket"
                                            class="text-blue-200 hover:text-white transition p-0.5 cursor-pointer"
                                            aria-label="Salin nomor tiket">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </span>
                                @if($report->is_simulation)
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-300 font-telemetry text-[10px] font-bold uppercase">
                                        Data Simulasi
                                    </span>
                                @endif
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200 font-telemetry text-xs font-bold uppercase">
                                    {{ $categoryLabel }}
                                </span>
                            </div>

                            <span class="font-telemetry text-xs text-slate-400">
                                {{ $report->created_at?->format('d M Y, H:i') }} WIB
                            </span>
                        </div>

                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-[#202938]">
                                {{ $report->title }}
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed whitespace-pre-line">{{ $report->description }}</p>
                        </div>

                        <!-- 2. Status Verifikasi (Pemeriksaan Informasi) & 4. Status Tindak Lanjut -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            <!-- Status Verifikasi Laporan -->
                            <div class="bg-blue-50/40 border border-blue-200/70 rounded-xl p-3.5 space-y-1.5">
                                <span class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-[#173B70] block">
                                    Status Verifikasi Informasi
                                </span>
                                <div>
                                    <x-verification-badge :status="$report->verification_status" />
                                </div>
                                <p class="text-xs text-slate-600 leading-snug">
                                    @if($rVerif === 'terverifikasi')
                                        Laporan telah diperiksa dan dinyatakan <strong>Terverifikasi</strong> oleh Tim Verifikator.
                                    @elseif($rVerif === 'ditolak')
                                        Laporan dinyatakan <strong>Ditolak</strong> berdasarkan hasil verifikasi silang.
                                    @else
                                        Laporan sedang menunggu pemeriksaan keabsahan informasi oleh Tim Verifikator.
                                    @endif
                                </p>
                            </div>

                            <!-- Status Tindak Lanjut Operasional -->
                            <div class="bg-emerald-50/40 border border-emerald-200/70 rounded-xl p-3.5 space-y-1.5">
                                <span class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-emerald-800 block">
                                    Status Tindak Lanjut Kejadian
                                </span>
                                <div>
                                    @if($report->incident)
                                        <x-followup-badge :status="$report->incident->follow_up_status" />
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-telemetry text-[11px] font-bold">
                                            Menunggu Penautan
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-600 leading-snug">
                                    @if($report->incident)
                                        Kejadian terkait berstatus: <strong>{{ $report->incident->follow_up_status?->label() ?? $report->incident->follow_up_status }}</strong> di lapangan.
                                    @else
                                        Tindak lanjut operasional dimulai setelah laporan diverifikasi dan ditautkan ke kejadian aktif.
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Metadata Singkat -->
                        <div class="grid grid-cols-2 gap-2 text-xs text-slate-600 pt-1 border-t border-slate-100">
                            <div>
                                <span class="text-slate-400 font-telemetry text-[10px] uppercase block">Lokasi:</span>
                                <span class="font-semibold text-slate-700">{{ $report->location_name }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 font-telemetry text-[10px] uppercase block">Waktu Diamati:</span>
                                <span class="font-semibold text-slate-700">{{ ($report->occurred_at ?? $report->created_at)?->format('d M Y, H:i') }} WIB</span>
                            </div>
                        </div>

                        <!-- 6. Informasi Sumber & Dokumentasi Pendukung -->
                        @if($report->evidence_url)
                            <div class="pt-2 border-t border-slate-100">
                                <a href="{{ $report->evidence_url }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="font-telemetry text-xs font-bold text-[#173B70] hover:underline inline-flex items-center gap-1">
                                    <span>Buka Tautan Sumber / Bukti Pendukung</span>
                                    <span>&nearr;</span>
                                </a>
                            </div>
                        @endif

                        <div class="bg-slate-50 border border-slate-200/80 rounded-lg p-2.5 text-xs text-slate-500">
                            <strong class="font-telemetry uppercase text-slate-600">Perlindungan Privasi:</strong>
                            Identitas pribadi pelapor dirahasiakan dan tidak ditampilkan ke publik.
                        </div>
                    </section>

                    <!-- 3. Keterkaitan Kejadian -->
                    <section class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4 sm:p-5 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                            <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                                Keterkaitan dengan Kejadian Publik
                            </h3>
                            @if($report->incident)
                                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-telemetry text-[10px] font-bold uppercase">
                                    Terhubung ke Kejadian
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200 font-telemetry text-[10px] font-bold uppercase">
                                    Belum Terhubung ke Kejadian
                                </span>
                            @endif
                        </div>

                        @if($report->incident)
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <x-severity-badge :severity="$report->incident->severity" />
                                    <span class="text-xs text-slate-400 font-telemetry">
                                        {{ $report->incident->occurred_at?->format('d M Y, H:i') }} WIB
                                    </span>
                                </div>
                                <h4 class="text-base font-bold text-[#202938]">
                                    <a href="{{ route('public.incidents.show', $report->incident->slug) }}" class="hover:text-[#173B70]">
                                        {{ $report->incident->title }}
                                    </a>
                                </h4>
                                <p class="text-xs text-slate-500">
                                    Lokasi Kejadian: {{ $report->incident->location_name }}
                                </p>
                            </div>

                            <div class="pt-2">
                                <a href="{{ route('public.incidents.show', $report->incident->slug) }}"
                                   class="px-3.5 py-1.5 rounded-lg bg-[#173B70] hover:bg-[#0F2A52] text-white font-telemetry text-xs font-bold uppercase tracking-wider inline-flex items-center gap-1.5 transition">
                                    <span>Lihat Detail Kejadian</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        @else
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Laporan ini belum ditautkan ke kejadian tertentu. Tim verifikator sedang memeriksa laporan untuk penautan kejadian.
                            </p>
                        @endif
                    </section>
                </div>

                <!-- Right Column (5 cols): 5. Timeline Perkembangan (Audit Trail) -->
                <div class="lg:col-span-5 space-y-4">
                    <section class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                                Riwayat Status Laporan
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Jejak transparan verifikasi dan tindak lanjut laporan.
                            </p>
                        </div>

                        <x-audit-timeline
                            :verificationHistories="$report->verificationHistories"
                            :followUpRecords="$report->followUpRecords"
                            :aidResponses="$report->aidResponses"
                            :isInternal="false"
                        />
                    </section>
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
