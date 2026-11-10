@extends('layouts.app', [
    'title' => 'Lacak Status Tiket Laporan — TanggapTerang',
    'activeTab' => 'lacak',
])

@section('content')
    <!-- Ticket Confirmation Banner (Shown right after submitting a new report) -->
    @if(session('created_ticket'))
        <section class="bg-[#ECFDF5] border border-emerald-300 rounded-2xl shadow-xs p-5 space-y-3" role="status">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <span class="px-2.5 py-1 rounded bg-[#059669] text-white font-telemetry text-xs font-bold uppercase tracking-wider">
                    Laporan Berhasil Tercatat
                </span>
                <span class="font-telemetry text-xs text-[#065F46] font-semibold">
                    Status Awal: MENUNGGU VERIFIKASI
                </span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-[#6EE7B7] rounded-xl p-4">
                <div>
                    <div class="font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        Nomor Tiket Pelaporan Anda
                    </div>
                    <div class="font-telemetry text-2xl sm:text-3xl font-bold text-[#0F172A] tracking-wider mt-0.5 select-all">
                        {{ session('created_ticket') }}
                    </div>
                    <p class="text-xs text-slate-600 mt-1">
                        Simpan kode tiket ini untuk memeriksa perkembangan verifikasi dan penautan kejadian kapan saja.
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

    <!-- Search Header & Ticket Lookup Form -->
    <section class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 sm:p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="space-y-1">
                <span class="px-2 py-0.5 rounded bg-[#0F172A] text-white font-telemetry text-[10px] font-bold uppercase tracking-wider">
                    Pelacakan Tiket Laporan Warga
                </span>
                <h1 class="text-xl sm:text-2xl font-extrabold text-[#0F172A]">
                    Lacak Status Laporan &amp; Respons Bantuan
                </h1>
                <p class="text-sm text-slate-600">
                    Masukkan kode tiket untuk melihat perkembangan verifikasi dan tindak lanjut laporan Anda.
                </p>
            </div>
            <a href="{{ route('public.reports.create') }}"
               class="px-4 py-2.5 rounded-lg bg-[#DC2626] hover:bg-red-700 text-white font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[42px] inline-flex items-center gap-1.5 shrink-0">
                <span>+ Buat Laporan Baru</span>
            </a>
        </div>

        <form method="GET" action="{{ route('public.track') }}" class="pt-1">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <label for="ticket-input" class="sr-only">Nomor Tiket Laporan</label>
                    <input type="text"
                           id="ticket-input"
                           name="ticket"
                           value="{{ $ticketQuery }}"
                           required
                           maxlength="32"
                           placeholder="Masukkan nomor tiket (contoh: RPT-SIM-8K4M9P2X)"
                           class="w-full min-h-[46px] px-4 py-2.5 bg-white border border-[#CBD5E1] rounded-lg font-telemetry text-sm font-bold uppercase tracking-wider text-[#0F172A] placeholder:font-normal placeholder:normal-case placeholder:text-slate-400 focus:outline-none focus:border-[#0F172A]">
                </div>
                <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[46px] inline-flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Cari Tiket</span>
                </button>
            </div>
        </form>

        <!-- Quick Demo Simulation Tickets for Testing/Jury Evaluation -->
        <div class="pt-2 border-t border-slate-100 flex flex-wrap items-center gap-2 text-xs font-telemetry text-slate-500">
            <span class="uppercase font-bold text-slate-600">Contoh Tiket Laporan Warga:</span>
            <a href="{{ route('public.track', ['ticket' => 'RPT-SIM-8K4M9P2X']) }}"
               data-tooltip="Lacak contoh laporan terverifikasi"
               class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-[#0F172A] font-bold transition hover:shadow-xs">
                RPT-SIM-8K4M9P2X (Terverifikasi)
            </a>
            <a href="{{ route('public.track', ['ticket' => 'RPT-SIM-6T9W4L8R']) }}"
               data-tooltip="Lacak contoh laporan antrean verifikasi"
               class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-[#0F172A] font-bold transition hover:shadow-xs">
                RPT-SIM-6T9W4L8R (Menunggu Verifikasi)
            </a>
            <a href="{{ route('public.track', ['ticket' => 'RPT-SIM-9P3F6Y2M']) }}"
               data-tooltip="Lacak contoh laporan ditolak"
               class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-[#0F172A] font-bold transition hover:shadow-xs">
                RPT-SIM-9P3F6Y2M (Ditolak)
            </a>
            <span class="uppercase font-bold text-slate-600 ml-1">Contoh Kode Respons Bantuan:</span>
            <a href="{{ route('public.track', ['ticket' => 'RSP-SIM-7M2K8N4Q']) }}"
               data-tooltip="Lacak respons terverifikasi &amp; terkonfirmasi"
               class="px-2 py-1 rounded bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-200 font-bold transition hover:shadow-xs">
                RSP-SIM-7M2K8N4Q (Terverifikasi • Terkonfirmasi)
            </a>
            <a href="{{ route('public.track', ['ticket' => 'RSP-SIM-9V5P3W6X']) }}"
               data-tooltip="Lacak respons dalam tahap penyaluran"
               class="px-2 py-1 rounded bg-blue-50 hover:bg-blue-100 text-blue-900 border border-blue-200 font-bold transition hover:shadow-xs">
                RSP-SIM-9V5P3W6X (Terverifikasi • Dalam Penyaluran)
            </a>
            <a href="{{ route('public.track', ['ticket' => 'RSP-SIM-2H8R4Y7L']) }}"
               data-tooltip="Lacak respons menunggu verifikasi"
               class="px-2 py-1 rounded bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 font-bold transition hover:shadow-xs">
                RSP-SIM-2H8R4Y7L (Menunggu Verifikasi • Direncanakan)
            </a>
        </div>
    </section>

    <!-- Search Results Area -->
    @if($searched)
        @if(!$report && !$aidResponse)
            <!-- Safe Not-Found Message -->
            <section class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-8 text-center space-y-3" role="alert">
                <div class="w-12 h-12 rounded-full bg-amber-100 text-[#D97706] mx-auto flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/>
                    </svg>
                </div>
                <h2 class="text-base sm:text-lg font-bold text-[#0F172A]">
                    Nomor Tiket / Kode Referensi Tidak Ditemukan
                </h2>
                <p class="text-sm text-slate-600 max-w-lg mx-auto leading-relaxed">
                    Kode tiket <code class="font-telemetry font-bold text-[#0F172A] bg-slate-100 px-1.5 py-0.5 rounded">{{ $ticketQuery }}</code> tidak ditemukan di dalam sistem. Periksa kembali huruf dan angka pada kode tiket Anda.
                </p>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    Informasi hanya ditampilkan jika kode tiket dimasukkan secara tepat untuk melindungi privasi.
                </p>
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

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                <!-- Left Column (7 cols): Aid Response Details & Dual Status -->
                <div class="lg:col-span-7 space-y-5">
                    <!-- Aid Response Card -->
                    <section class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-1 rounded bg-[#0F172A] text-white font-telemetry text-xs font-bold tracking-wider inline-flex items-center gap-1.5">
                                    <span>{{ $aidResponse->reference_code }}</span>
                                    <button type="button"
                                            data-copy-text="{{ $aidResponse->reference_code }}"
                                            data-tooltip="Salin kode respons"
                                            class="text-slate-400 hover:text-white transition p-0.5 cursor-pointer"
                                            aria-label="Salin kode respons">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </span>
                                @if($aidResponse->is_simulation)
                                    <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 border border-amber-300 font-telemetry text-[10px] font-bold uppercase">
                                        Data Simulasi
                                    </span>
                                @endif
                                <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase {{ $isDeliveredSubmission ? 'bg-indigo-50 text-indigo-900 border border-indigo-200' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                                    {{ $aidResponse->submissionTypeLabel() }}
                                </span>
                                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 font-telemetry text-[10px] font-bold uppercase">
                                    Respons Bantuan Masyarakat
                                </span>
                            </div>
                        </div>

                        <!-- Dua Status Terpisah: Pemeriksaan vs Operasional -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            <!-- Status 1: Pemeriksaan / Verifikasi Informasi -->
                            <div class="bg-[#F8FAFC] border border-slate-100 rounded-xl p-3.5 space-y-2">
                                <span class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-slate-500 block">
                                    1. Status Pemeriksaan / Verifikasi
                                </span>
                                <div>
                                    <x-verification-badge :status="$aidResponse->verification_status" />
                                </div>
                                <p class="text-xs text-slate-600 leading-snug">
                                    @if($aidResponse->isVerified())
                                        Informasi dan kelayakan respons telah diperiksa dan dinyatakan <strong>Terverifikasi</strong> oleh Tim Verifikator.
                                    @elseif($aidResponse->isRejected())
                                        Pemeriksaan respons dinyatakan <strong>Ditolak</strong> oleh Tim Verifikator.
                                    @else
                                        Menunggu pemeriksaan dan verifikasi kelayakan informasi oleh Tim Verifikator.
                                    @endif
                                </p>
                                @if($aidResponse->verification_notes)
                                    <div class="text-[11px] bg-white border border-slate-200 rounded p-2 text-slate-700">
                                        <span class="font-telemetry font-bold uppercase text-[9px] text-slate-500 block">Catatan Verifikator:</span>
                                        {{ $aidResponse->verification_notes }}
                                    </div>
                                @endif
                                @if($aidResponse->verified_at)
                                    <span class="font-telemetry text-[10px] text-slate-500 block">
                                        Waktu Pemeriksaan: {{ $aidResponse->verified_at->format('d M Y, H:i') }} WIB
                                    </span>
                                @endif
                            </div>

                            <!-- Status 2: Operasional Bantuan -->
                            <div class="bg-[#F8FAFC] border border-slate-100 rounded-xl p-3.5 space-y-2">
                                <span class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-slate-500 block">
                                    2. Status Operasional Bantuan
                                </span>
                                <div>
                                    <x-aid-status-badge :status="$aidResponse->status" />
                                </div>
                                <p class="text-xs text-slate-600 leading-snug">
                                    @if($aidResponse->isConfirmed())
                                        Penyerahan bantuan telah <strong>Dikonfirmasi</strong> di lapangan berdasarkan verifikasi bukti dokumentasi.
                                    @elseif($aidResponse->isDelivering())
                                        Bantuan saat ini sedang dalam proses <strong>Penyaluran</strong> ke lokasi tujuan.
                                    @else
                                        Bantuan berstatus <strong>Direncanakan</strong> dalam sistem koordinasi.
                                    @endif
                                </p>
                                @if($aidResponse->confirmation_notes)
                                    <div class="text-[11px] bg-white border border-slate-200 rounded p-2 text-slate-700">
                                        <span class="font-telemetry font-bold uppercase text-[9px] text-slate-500 block">Catatan Lapangan / Konfirmasi:</span>
                                        {{ $aidResponse->confirmation_notes }}
                                    </div>
                                @endif
                                @if($aidResponse->confirmed_at)
                                    <span class="font-telemetry text-[10px] text-slate-500 block">
                                        Waktu Konfirmasi: {{ $aidResponse->confirmed_at->format('d M Y, H:i') }} WIB
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Rincian Respons Bantuan -->
                        <div class="space-y-2 pt-2">
                            <div class="flex flex-wrap items-baseline justify-between gap-2">
                                <h2 class="text-lg sm:text-xl font-bold text-[#0F172A]">
                                    {{ $aidResponse->aid_item_title }}
                                </h2>
                                <span class="font-telemetry text-xs font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded">
                                    Kapasitas / Jumlah: {{ $aidResponse->quantity_label }}
                                </span>
                            </div>

                            @if($aidResponse->aid_description)
                                <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $aidResponse->aid_description }}</p>
                            @endif
                        </div>

                        <!-- Metadata Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            <div class="bg-[#F8FAFC] border border-slate-100 rounded-xl p-3">
                                <span class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-slate-500 block">
                                    Kategori Kebutuhan
                                </span>
                                <span class="font-telemetry text-xs font-bold text-[#0F172A]">
                                    {{ $needLabel }}
                                </span>
                            </div>

                            <div class="bg-[#F8FAFC] border border-slate-100 rounded-xl p-3">
                                <span class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-slate-500 block">
                                    Pemberi Respons
                                </span>
                                <span class="font-telemetry text-xs font-bold text-[#0F172A]">
                                    {{ $aidResponse->publicResponderName() }}
                                </span>
                                <span class="font-telemetry text-[10px] text-slate-500 block">
                                    {{ $responderTypes[$aidResponse->responder_type] ?? $aidResponse->responder_type }}
                                </span>
                            </div>

                            <div class="bg-[#F8FAFC] border border-slate-100 rounded-xl p-3">
                                <span class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-slate-500 block">
                                    Waktu Pencatatan
                                </span>
                                <span class="font-telemetry text-xs font-bold text-[#0F172A]">
                                    {{ $aidResponse->recorded_at?->format('d M Y, H:i') ?? '-' }} WIB
                                </span>
                            </div>

                            <div class="bg-[#F8FAFC] border border-slate-100 rounded-xl p-3">
                                <span class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-slate-500 block">
                                    Waktu Penyerahan (Dilaporkan)
                                </span>
                                <span class="font-telemetry text-xs font-bold text-[#0F172A]">
                                    {{ $aidResponse->delivered_at?->format('d M Y, H:i') ?? 'Belum diserahkan' }}
                                    @if($aidResponse->delivered_at) WIB @endif
                                </span>
                            </div>

                            <div class="bg-[#F8FAFC] border border-slate-100 rounded-xl p-3 sm:col-span-2">
                                <span class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-slate-500 block">
                                    Lokasi / Wilayah Tujuan Penyaluran
                                </span>
                                <span class="text-xs font-bold text-[#0F172A] block mt-0.5">
                                    {{ $aidResponse->target_location }}
                                </span>
                            </div>
                        </div>

                        @if($aidResponse->evidence_url)
                            <div class="pt-1">
                                <a href="{{ $aidResponse->evidence_url }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="font-telemetry text-xs font-bold text-[#2563EB] hover:underline inline-flex items-center gap-1">
                                   <span>Lihat Tautan Bukti Dokumentasi Lapangan</span>
                                   <span>&rarr;</span>
                                </a>
                            </div>
                        @endif

                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-600">
                            <strong class="font-telemetry uppercase text-slate-700">Perlindungan Privasi:</strong>
                            Kontak pribadi pemberi respons dirahasiakan dan tidak ditampilkan ke publik.
                        </div>
                    </section>

                    <!-- Linked Incident Card -->
                    @if($aidResponse->incident)
                        <section class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                                    Kejadian Bencana Terkait
                                </h2>
                                <span class="px-2 py-0.5 rounded bg-[#ECFDF5] text-[#059669] border border-[#6EE7B7] font-telemetry text-[10px] font-bold uppercase">
                                    Terhubung
                                </span>
                            </div>

                            <div class="bg-[#F8FAFC] border border-slate-100 rounded-xl p-4 space-y-3">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <x-severity-badge :severity="$aidResponse->incident->severity" />
                                    <span class="font-telemetry text-xs text-slate-500">
                                        Waktu Kejadian: {{ $aidResponse->incident->occurred_at?->format('d M Y, H:i') }} WIB
                                    </span>
                                </div>

                                <h3 class="text-base font-bold text-[#0F172A]">
                                    <a href="{{ route('public.incidents.show', $aidResponse->incident->slug) }}" class="hover:text-[#2563EB]">
                                        {{ $aidResponse->incident->title }}
                                    </a>
                                </h3>

                                <p class="text-xs text-slate-600">
                                    Lokasi Kejadian: <strong>{{ $aidResponse->incident->location_name }}</strong>
                                </p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                                    <div class="bg-white border border-slate-200 rounded p-2.5">
                                        <span class="font-telemetry text-[10px] font-bold uppercase text-slate-500 block mb-1">
                                            Status Verifikasi Kejadian
                                        </span>
                                        <x-verification-badge :status="$aidResponse->incident->verification_status" />
                                    </div>
                                    <div class="bg-white border border-slate-200 rounded p-2.5">
                                        <span class="font-telemetry text-[10px] font-bold uppercase text-slate-500 block mb-1">
                                            Status Tindak Lanjut Kejadian
                                        </span>
                                        <x-followup-badge :status="$aidResponse->incident->follow_up_status" />
                                    </div>
                                </div>

                                <div class="pt-2">
                                    <a href="{{ route('public.incidents.show', $aidResponse->incident->slug) }}"
                                       class="px-4 py-2 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white font-telemetry text-xs font-bold uppercase tracking-wider inline-flex items-center gap-1.5">
                                        <span>Buka Halaman Detail Kejadian</span>
                                        <span>&rarr;</span>
                                    </a>
                                </div>
                            </div>
                        </section>
                    @endif
                </div>

                <!-- Right Column (5 cols): Audit Trail / Status Timeline -->
                <div class="lg:col-span-5 space-y-5">
                    <section class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                                Riwayat Perkembangan Respons Bantuan ({{ $aidResponse->reference_code }})
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Jejak transparan status pemeriksaan dan status operasional bantuan masyarakat.
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

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                <!-- Left Column (7 cols): Report Summary & Linked Incident -->
                <div class="lg:col-span-7 space-y-5">
                    <!-- Report Summary Card (Sanitized — NO Reporter PII) -->
                    <section class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-1 rounded bg-[#0F172A] text-white font-telemetry text-xs font-bold tracking-wider inline-flex items-center gap-1.5">
                                    <span>{{ $report->ticket_number }}</span>
                                    <button type="button"
                                            data-copy-text="{{ $report->ticket_number }}"
                                            data-tooltip="Salin nomor tiket"
                                            class="text-slate-400 hover:text-white transition p-0.5 cursor-pointer"
                                            aria-label="Salin nomor tiket">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </span>
                                @if($report->is_simulation)
                                    <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 border border-amber-300 font-telemetry text-[10px] font-bold uppercase">
                                        Data Simulasi
                                    </span>
                                @endif
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200 font-telemetry text-xs font-bold uppercase">
                                    {{ $categoryLabel }}
                                </span>
                            </div>

                            <x-verification-badge :status="$report->verification_status" />
                        </div>

                        <div class="space-y-2">
                            <h2 class="text-lg sm:text-xl font-bold text-[#0F172A]">
                                {{ $report->title }}
                            </h2>
                            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $report->description }}</p>
                        </div>

                        <!-- Metadata Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            <div class="bg-[#F8FAFC] border border-slate-100 rounded-xl p-3">
                                <span class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-slate-500 block">
                                    Waktu Laporan Dibuat
                                </span>
                                <span class="font-telemetry text-xs font-bold text-[#0F172A]">
                                    {{ $report->created_at?->format('d M Y, H:i') ?? '-' }} WIB
                                </span>
                            </div>

                            <div class="bg-[#F8FAFC] border border-slate-100 rounded-xl p-3">
                                <span class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-slate-500 block">
                                    Waktu Kejadian Diamati
                                </span>
                                <span class="font-telemetry text-xs font-bold text-[#0F172A]">
                                    {{ ($report->occurred_at ?? $report->created_at)?->format('d M Y, H:i') ?? '-' }} WIB
                                </span>
                            </div>

                            <div class="bg-[#F8FAFC] border border-slate-100 rounded-xl p-3 sm:col-span-2">
                                <span class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-slate-500 block">
                                    Lokasi yang Dilaporkan
                                </span>
                                <span class="text-xs font-bold text-[#0F172A] block mt-0.5">
                                    {{ $report->location_name }}
                                </span>
                                @if($report->wilayahAdministratif)
                                    <span class="font-telemetry text-[11px] text-slate-500 block mt-0.5">
                                        Wilayah Administratif: {{ $report->wilayahAdministratif->nama }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if($report->evidence_url)
                            <div class="pt-1">
                                <a href="{{ $report->evidence_url }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="font-telemetry text-xs font-bold text-[#2563EB] hover:underline inline-flex items-center gap-1">
                                    <span>Lihat Tautan Sumber / Dokumentasi Pendukung</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        @endif

                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-600">
                            <strong class="font-telemetry uppercase text-slate-700">Perlindungan Privasi:</strong>
                            Nama dan nomor kontak pelapor dirahasiakan untuk melindungi privasi warga.
                        </div>
                    </section>

                    <!-- Linked Incident Section -->
                    <section class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                                Keterhubungan dengan Kejadian Publik
                            </h2>
                            @if($report->incident)
                                <span class="px-2 py-0.5 rounded bg-[#ECFDF5] text-[#059669] border border-[#6EE7B7] font-telemetry text-[10px] font-bold uppercase">
                                    Terhubung ke Kejadian
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-300 font-telemetry text-[10px] font-bold uppercase">
                                    Belum Terhubung ke Kejadian
                                </span>
                            @endif
                        </div>

                        @if($report->incident)
                            <div class="bg-[#F8FAFC] border border-slate-100 rounded-xl p-4 space-y-3">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <x-severity-badge :severity="$report->incident->severity" />
                                    <span class="font-telemetry text-xs text-slate-500">
                                        Waktu Kejadian: {{ $report->incident->occurred_at?->format('d M Y, H:i') }} WIB
                                    </span>
                                </div>

                                <h3 class="text-base font-bold text-[#0F172A]">
                                    <a href="{{ route('public.incidents.show', $report->incident->slug) }}" class="hover:text-[#2563EB]">
                                        {{ $report->incident->title }}
                                    </a>
                                </h3>

                                <p class="text-xs text-slate-600">
                                    Lokasi: <strong>{{ $report->incident->location_name }}</strong>
                                </p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                                    <div class="bg-white border border-slate-200 rounded p-2.5">
                                        <span class="font-telemetry text-[10px] font-bold uppercase text-slate-500 block mb-1">
                                            Status Verifikasi Kejadian
                                        </span>
                                        <x-verification-badge :status="$report->incident->verification_status" />
                                    </div>
                                    <div class="bg-white border border-slate-200 rounded p-2.5">
                                        <span class="font-telemetry text-[10px] font-bold uppercase text-slate-500 block mb-1">
                                            Status Tindak Lanjut Kejadian
                                        </span>
                                        <x-followup-badge :status="$report->incident->follow_up_status" />
                                    </div>
                                </div>

                                <div class="pt-2">
                                    <a href="{{ route('public.incidents.show', $report->incident->slug) }}"
                                       class="px-4 py-2 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white font-telemetry text-xs font-bold uppercase tracking-wider inline-flex items-center gap-1.5">
                                        <span>Buka Halaman Detail Kejadian</span>
                                        <span>&rarr;</span>
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="bg-[#F8FAFC] border border-slate-100 rounded-xl p-4 text-xs text-slate-600 leading-relaxed">
                                Laporan ini belum ditautkan ke kejadian tertentu. Tim verifikator sedang memeriksa kesesuaian laporan dengan kejadian aktif di sekitarnya.
                            </div>
                        @endif
                    </section>
                </div>

                <!-- Right Column (5 cols): Public Audit Trail for this Report -->
                <div class="lg:col-span-5 space-y-5">
                    <section class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                                Riwayat Status Laporan ({{ $report->ticket_number }})
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Jejak perubahan status verifikasi dan catatan tindak lanjut yang aman untuk publik.
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
@endsection
