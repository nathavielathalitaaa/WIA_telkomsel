@extends('layouts.app', [
    'title' => 'Lacak Status Tiket Laporan — TanggapTerang',
    'activeTab' => 'lacak',
])

@section('content')
    <!-- Ticket Confirmation Banner (Shown right after submitting a new report) -->
    @if(session('created_ticket'))
        <section class="bg-[#ECFDF5] border-2 border-[#059669] rounded-lg p-5 space-y-3" role="status">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <span class="px-2.5 py-1 rounded bg-[#059669] text-white font-telemetry text-xs font-bold uppercase tracking-wider">
                    Laporan Berhasil Tercatat
                </span>
                <span class="font-telemetry text-xs text-[#065F46] font-semibold">
                    Status Awal: MENUNGGU VERIFIKASI
                </span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-[#6EE7B7] rounded-lg p-4">
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
            </div>
        </section>
    @endif

    <!-- Search Header & Ticket Lookup Form -->
    <section class="bg-white border border-[#E2E8F0] rounded-lg p-5 sm:p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="space-y-1">
                <span class="px-2 py-0.5 rounded bg-[#0F172A] text-white font-telemetry text-[10px] font-bold uppercase tracking-wider">
                    Pelacakan Tiket Laporan Warga
                </span>
                <h1 class="text-xl sm:text-2xl font-extrabold text-[#0F172A]">
                    Lacak Status Laporan Berdasarkan Nomor Tiket
                </h1>
                <p class="text-sm text-slate-600">
                    Masukkan kode tiket unik yang Anda terima saat mengirim laporan untuk melihat status verifikasi dan keterhubungannya dengan kejadian.
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
            <span class="uppercase font-bold text-slate-600">Contoh Tiket Data Simulasi:</span>
            <a href="{{ route('public.track', ['ticket' => 'RPT-SIM-8K4M9P2X']) }}"
               class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-[#0F172A] font-bold transition">
                RPT-SIM-8K4M9P2X (Terverifikasi)
            </a>
            <a href="{{ route('public.track', ['ticket' => 'RPT-SIM-6T9W4L8R']) }}"
               class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-[#0F172A] font-bold transition">
                RPT-SIM-6T9W4L8R (Menunggu Verifikasi)
            </a>
            <a href="{{ route('public.track', ['ticket' => 'RPT-SIM-9P3F6Y2M']) }}"
               class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-[#0F172A] font-bold transition">
                RPT-SIM-9P3F6Y2M (Ditolak)
            </a>
        </div>
    </section>

    <!-- Search Results Area -->
    @if($searched)
        @if(!$report)
            <!-- Safe Not-Found Message -->
            <section class="bg-white border border-[#E2E8F0] rounded-lg p-8 text-center space-y-3" role="alert">
                <div class="w-12 h-12 rounded-full bg-amber-100 text-[#D97706] mx-auto flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/>
                    </svg>
                </div>
                <h2 class="text-base sm:text-lg font-bold text-[#0F172A]">
                    Nomor Tiket Tidak Ditemukan
                </h2>
                <p class="text-sm text-slate-600 max-w-lg mx-auto leading-relaxed">
                    Kami tidak menemukan laporan yang sesuai dengan kode tiket <code class="font-telemetry font-bold text-[#0F172A] bg-slate-100 px-1.5 py-0.5 rounded">{{ $ticketQuery }}</code>. Pastikan penulisan karakter tiket sudah benar dan lengkap.
                </p>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    Demi melindungi privasi pelapor, sistem hanya menampilkan data laporan jika nomor tiket unik cocok secara tepat.
                </p>
            </section>
        @else
            @php
                $categoryLabel = $categories[$report->category] ?? ucwords(str_replace('_', ' ', $report->category));
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                <!-- Left Column (7 cols): Report Summary & Linked Incident -->
                <div class="lg:col-span-7 space-y-5">
                    <!-- Report Summary Card (Sanitized — NO Reporter PII) -->
                    <section class="bg-white border border-[#E2E8F0] rounded-lg p-5 space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-1 rounded bg-[#0F172A] text-white font-telemetry text-xs font-bold tracking-wider">
                                    {{ $report->ticket_number }}
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
                            <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-3">
                                <span class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-slate-500 block">
                                    Waktu Laporan Dibuat
                                </span>
                                <span class="font-telemetry text-xs font-bold text-[#0F172A]">
                                    {{ $report->created_at?->format('d M Y, H:i') ?? '-' }} WIB
                                </span>
                            </div>

                            <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-3">
                                <span class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-slate-500 block">
                                    Waktu Kejadian Diamati
                                </span>
                                <span class="font-telemetry text-xs font-bold text-[#0F172A]">
                                    {{ ($report->occurred_at ?? $report->created_at)?->format('d M Y, H:i') ?? '-' }} WIB
                                </span>
                            </div>

                            <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-3 sm:col-span-2">
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

                        <div class="bg-slate-50 border border-slate-200 rounded p-3 text-xs text-slate-600">
                            <strong class="font-telemetry uppercase text-slate-700">Perlindungan Privasi:</strong>
                            Data pribadi dan nomor kontak pelapor disembunyikan pada halaman pelacakan publik ini.
                        </div>
                    </section>

                    <!-- Linked Incident Section -->
                    <section class="bg-white border border-[#E2E8F0] rounded-lg p-5 space-y-3">
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
                            <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-4 space-y-3">
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
                            <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-4 text-xs text-slate-600 leading-relaxed">
                                Laporan ini saat ini berdiri sendiri dan belum dihubungkan ke kejadian tertentu. Tim verifikator TanggapTerang akan memeriksa apakah laporan ini berkaitan dengan kejadian yang sudah ada atau memerlukan pembuatan data kejadian baru.
                            </div>
                        @endif
                    </section>
                </div>

                <!-- Right Column (5 cols): Public Audit Trail for this Report -->
                <div class="lg:col-span-5 space-y-5">
                    <section class="bg-white border border-[#E2E8F0] rounded-lg p-5 space-y-4">
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
