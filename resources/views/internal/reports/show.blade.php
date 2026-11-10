@extends('layouts.internal', [
    'title' => 'Detail Pengelolaan Laporan ' . $report->ticket_number . ' — TanggapTerang',
    'internalMenu' => 'reports',
])

@php
    $user = auth()->user();
    $categoryLabel = $categories[$report->category] ?? ucwords(str_replace('_', ' ', $report->category));
@endphp

@section('content')
    <!-- Breadcrumb -->
    <nav class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('internal.reports.index') }}"
           class="font-telemetry text-xs font-bold text-slate-600 hover:text-[#202938] inline-flex items-center gap-1">
            <span>&larr; Kembali ke Daftar Laporan</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('public.track', ['ticket' => $report->ticket_number]) }}"
               target="_blank"
               class="px-3 py-1.5 rounded border border-slate-200/90 bg-white hover:bg-slate-50 text-[#202938] font-telemetry text-xs font-bold uppercase tracking-wider">
                Lihat Tampilan Lacak Publik &nearr;
            </a>
        </div>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        <!-- Left Column (7 cols): Report Details & Audit History -->
        <div class="lg:col-span-7 space-y-5">
            <!-- Report Full Details Card (Including Internal-Only Reporter Info) -->
            <section class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-1 rounded bg-[#173B70] text-white font-telemetry text-xs font-bold">
                            {{ $report->ticket_number }}
                        </span>
                        @if($report->is_simulation)
                            <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 border border-amber-300 font-telemetry text-[10px] font-bold uppercase">
                                Data Simulasi
                            </span>
                        @endif
                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-800 font-telemetry text-xs font-bold uppercase">
                            {{ $categoryLabel }}
                        </span>
                    </div>

                    <x-verification-badge :status="$report->verification_status" />
                </div>

                <div class="space-y-2">
                    <h1 class="text-lg sm:text-xl font-extrabold text-[#202938]">
                        {{ $report->title }}
                    </h1>
                    <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $report->description }}</p>
                </div>

                <!-- Internal Metadata Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3">
                        <span class="font-telemetry text-[10px] font-bold uppercase text-slate-500 block">
                            Nama Pelapor (Rahasia Internal)
                        </span>
                        <span class="text-xs font-bold text-[#202938]">
                            {{ $report->reporter_name ?: 'Anonim (Tidak Diisi)' }}
                        </span>
                    </div>

                    <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3">
                        <span class="font-telemetry text-[10px] font-bold uppercase text-slate-500 block">
                            Kontak Pelapor (Rahasia Internal)
                        </span>
                        <span class="font-telemetry text-xs font-bold text-[#202938]">
                            {{ $report->reporter_contact ?: 'Tidak Diisi' }}
                        </span>
                    </div>

                    <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3">
                        <span class="font-telemetry text-[10px] font-bold uppercase text-slate-500 block">
                            Waktu Kejadian Diamati
                        </span>
                        <span class="font-telemetry text-xs font-bold text-[#202938]">
                            {{ ($report->occurred_at ?? $report->created_at)?->format('d M Y, H:i') }} WIB
                        </span>
                    </div>

                    <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3">
                        <span class="font-telemetry text-[10px] font-bold uppercase text-slate-500 block">
                            Waktu Laporan Masuk
                        </span>
                        <span class="font-telemetry text-xs font-bold text-[#202938]">
                            {{ $report->created_at?->format('d M Y, H:i') }} WIB
                        </span>
                    </div>

                    <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3 sm:col-span-2">
                        <span class="font-telemetry text-[10px] font-bold uppercase text-slate-500 block">
                            Lokasi &amp; Koordinat
                        </span>
                        <span class="text-xs font-bold text-[#202938] block mt-0.5">
                            {{ $report->location_name }}
                        </span>
                        @if($report->wilayahAdministratif)
                            <span class="font-telemetry text-xs text-slate-600 block">
                                Wilayah Administratif: {{ $report->wilayahAdministratif->nama }}
                            </span>
                        @endif
                        @if($report->latitude !== null && $report->longitude !== null)
                            <span class="font-telemetry text-xs text-slate-500 block mt-0.5">
                                Koordinat: {{ $report->latitude }}, {{ $report->longitude }}
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
                            <span>Buka Tautan Bukti / Sumber Pendukung</span>
                            <span>&nearr;</span>
                        </a>
                    </div>
                @endif
            </section>

            <!-- Complete Internal Audit Trail -->
            <section class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                        Riwayat Verifikasi &amp; Penautan Laporan (Mode Internal)
                    </h2>
                    <p class="text-xs text-slate-500">
                        Menampilkan alasan lengkap dan identitas petugas yang melakukan perubahan status.
                    </p>
                </div>

                <x-audit-timeline
                    :verificationHistories="$report->verificationHistories"
                    :followUpRecords="$report->followUpRecords"
                    :isInternal="true"
                />
            </section>
        </div>

        <!-- Right Column (5 cols): Role-Gated Action Panels -->
        <div class="lg:col-span-5 space-y-5">
            <!-- Panel 1: Keputusan Verifikasi Laporan (Verifikator & Admin) -->
            <section class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                            1. Keputusan Verifikasi Laporan
                        </h2>
                        <p class="text-xs text-slate-500">Wewenang: Verifikator &amp; Admin</p>
                    </div>
                    <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase {{ $user->canVerify() ? 'bg-blue-100 text-[#2563EB]' : 'bg-slate-100 text-slate-500' }}">
                        {{ $user->canVerify() ? 'Akses Diizinkan' : 'Khusus Verifikator/Admin' }}
                    </span>
                </div>

                @if($user->canVerify())
                    <!-- Form A: Verifikasi Laporan -->
                    <form method="POST"
                          action="{{ route('internal.reports.verify', $report) }}"
                          onsubmit="return confirm('Konfirmasi: Tetapkan laporan {{ $report->ticket_number }} sebagai TERVERIFIKASI?');"
                          class="bg-[#EFF6FF] border border-[#93C5FD] rounded-lg p-4 space-y-3">
                        @csrf
                        <div class="font-telemetry text-xs font-bold uppercase text-[#1E40AF]">
                            A. Setujui &amp; Verifikasi Laporan
                        </div>

                        <div>
                            <label for="verify-confidence" class="block font-telemetry text-[11px] font-bold uppercase text-slate-700 mb-1">
                                Tingkat Keyakinan Informasi
                            </label>
                            <select id="verify-confidence"
                                    name="confidence_level"
                                    class="w-full min-h-[40px] px-3 py-1.5 bg-white border border-slate-200/90 rounded text-xs text-[#202938]">
                                @foreach(\App\Enums\ConfidenceLevel::cases() as $cCase)
                                    <option value="{{ $cCase->value }}" @selected(($report->confidence_level?->value ?? 'tinggi') === $cCase->value)>
                                        {{ $cCase->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="verify-reason" class="block font-telemetry text-[11px] font-bold uppercase text-slate-700 mb-1">
                                Catatan Dasar Verifikasi (Internal) <span class="text-[#DC2626]">*</span>
                            </label>
                            <textarea id="verify-reason"
                                      name="reason"
                                      rows="2"
                                      required
                                      placeholder="Contoh: Dikonfirmasi silang dengan foto lokasi dan rilis sumber terbuka..."
                                      class="w-full px-3 py-2 bg-white border border-slate-200/90 rounded text-xs text-[#202938]"></textarea>
                        </div>

                        <button type="submit"
                                class="w-full px-4 py-2 rounded bg-[#2563EB] hover:bg-blue-700 text-white font-telemetry text-xs font-bold uppercase tracking-wider transition cursor-pointer">
                            Tetapkan Status: Terverifikasi
                        </button>
                    </form>

                    <!-- Form B: Tandai Duplikat -->
                    <form method="POST"
                          action="{{ route('internal.reports.duplicate', $report) }}"
                          onsubmit="return confirm('Konfirmasi: Tandai laporan {{ $report->ticket_number }} sebagai DUPLIKAT? Laporan tetap tersimpan di database.');"
                          class="bg-[#FFFBEB] border border-[#FCD34D] rounded-lg p-4 space-y-3">
                        @csrf
                        <div class="font-telemetry text-xs font-bold uppercase text-[#92400E]">
                            B. Tandai Laporan Duplikat
                        </div>

                        <div>
                            <label for="duplicate-reason" class="block font-telemetry text-[11px] font-bold uppercase text-slate-700 mb-1">
                                Rujukan Tiket / Alasan Duplikat <span class="text-[#DC2626]">*</span>
                            </label>
                            <textarea id="duplicate-reason"
                                      name="reason"
                                      rows="2"
                                      required
                                      placeholder="Contoh: Melaporkan titik dan waktu yang sama dengan tiket RPT-SIM-8K4M9P2X..."
                                      class="w-full px-3 py-2 bg-white border border-slate-200/90 rounded text-xs text-[#202938]"></textarea>
                        </div>

                        <button type="submit"
                                class="w-full px-4 py-2 rounded bg-[#D97706] hover:bg-amber-700 text-white font-telemetry text-xs font-bold uppercase tracking-wider transition cursor-pointer">
                            Tetapkan Status: Duplikat
                        </button>
                    </form>

                    <!-- Form C: Tolak Laporan -->
                    <form method="POST"
                          action="{{ route('internal.reports.reject', $report) }}"
                          onsubmit="return confirm('Konfirmasi: Tolak laporan {{ $report->ticket_number }}? Laporan tetap disimpan sebagai arsip audit.');"
                          class="bg-[#FEF2F2] border border-[#FCA5A5] rounded-lg p-4 space-y-3">
                        @csrf
                        <div class="font-telemetry text-xs font-bold uppercase text-[#991B1B]">
                            C. Tolak Laporan (Dengan Alasan)
                        </div>

                        <div>
                            <label for="reject-reason" class="block font-telemetry text-[11px] font-bold uppercase text-slate-700 mb-1">
                                Alasan Penolakan <span class="text-[#DC2626]">*</span>
                            </label>
                            <textarea id="reject-reason"
                                      name="reason"
                                      rows="2"
                                      required
                                      placeholder="Contoh: Informasi tidak sesuai fakta lapangan / foto kejadian tahun sebelumnya..."
                                      class="w-full px-3 py-2 bg-white border border-slate-200/90 rounded text-xs text-[#202938]"></textarea>
                        </div>

                        <button type="submit"
                                class="w-full px-4 py-2 rounded bg-[#DC2626] hover:bg-red-700 text-white font-telemetry text-xs font-bold uppercase tracking-wider transition cursor-pointer">
                            Tetapkan Status: Ditolak
                        </button>
                    </form>
                @else
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 text-xs text-slate-600">
                        Role Anda saat ini adalah <strong>{{ $user->role?->label() }}</strong>. Sesuai pemisahan wewenang TanggapTerang, keputusan verifikasi, penolakan, dan penandaan duplikat laporan hanya dapat dilakukan oleh <strong>Verifikator</strong> atau <strong>Admin</strong>.
                    </div>
                @endif
            </section>

            <!-- Panel 2: Hubungkan Laporan dengan Kejadian (Operator & Admin) -->
            <section class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                            2. Hubungkan Laporan dengan Kejadian
                        </h2>
                        <p class="text-xs text-slate-500">Wewenang: Operator &amp; Admin</p>
                    </div>
                    <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase {{ $user->canLinkReportToIncident() ? 'bg-emerald-100 text-[#059669]' : 'bg-slate-100 text-slate-500' }}">
                        {{ $user->canLinkReportToIncident() ? 'Akses Diizinkan' : 'Khusus Operator/Admin' }}
                    </span>
                </div>

                @if($report->incident)
                    <div class="bg-[#ECFDF5] border border-[#6EE7B7] rounded-lg p-3.5 text-xs space-y-1">
                        <span class="font-telemetry font-bold uppercase text-[#065F46] block">Saat Ini Terhubung ke:</span>
                        <a href="{{ route('internal.incidents.edit', $report->incident) }}" class="font-bold text-[#202938] hover:underline block text-sm">
                            {{ $report->incident->title }}
                        </a>
                        <span class="text-slate-600 block">Lokasi: {{ $report->incident->location_name }}</span>
                    </div>
                @endif

                @if($user->canLinkReportToIncident())
                    <form method="POST"
                          action="{{ route('internal.reports.link-incident', $report) }}"
                          onsubmit="return confirm('Konfirmasi: Hubungkan laporan {{ $report->ticket_number }} ke kejadian terpilih?');"
                          class="space-y-3">
                        @csrf

                        <div>
                            <label for="incident_id" class="block font-telemetry text-[11px] font-bold uppercase text-slate-700 mb-1">
                                Pilih Kejadian yang Relevan <span class="text-[#DC2626]">*</span>
                            </label>
                            <select id="incident_id"
                                    name="incident_id"
                                    required
                                    class="w-full min-h-[42px] px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-xs text-[#202938]">
                                <option value="">-- Pilih Kejadian --</option>
                                @foreach($availableIncidents as $inc)
                                    <option value="{{ $inc->id }}" @selected($report->incident_id === $inc->id)>
                                        {{ $inc->title }} ({{ $inc->location_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="link-notes" class="block font-telemetry text-[11px] font-bold uppercase text-slate-700 mb-1">
                                Catatan Penautan (Opsional)
                            </label>
                            <input type="text"
                                   id="link-notes"
                                   name="notes"
                                   placeholder="Contoh: Lokasi berada pada klaster banjir yang sama..."
                                   class="w-full min-h-[40px] px-3 py-1.5 bg-white border border-slate-200/90 rounded-lg text-xs text-[#202938]">
                        </div>

                        <button type="submit"
                                class="w-full px-4 py-2.5 rounded-lg bg-[#173B70] hover:bg-[#0F2A52] text-white font-telemetry text-xs font-bold uppercase tracking-wider transition cursor-pointer">
                            Simpan Keterhubungan Kejadian
                        </button>
                    </form>

                    @if(!$report->incident)
                        <div class="pt-3 border-t border-slate-200 space-y-2">
                            <span class="font-telemetry text-[11px] font-bold uppercase text-slate-600 block">
                                Belum Ada Kejadian yang Sesuai?
                            </span>
                            <a href="{{ route('internal.incidents.create', ['from_report' => $report->id]) }}"
                               class="w-full px-4 py-2.5 rounded-lg border border-emerald-600 bg-emerald-50 hover:bg-emerald-100 text-emerald-950 font-telemetry text-xs font-bold uppercase tracking-wider transition inline-flex items-center justify-center gap-1.5">
                                <span>+ Buat Kejadian Baru dari Laporan Ini</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    @endif
                @else
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 text-xs text-slate-600">
                        Role Anda saat ini adalah <strong>{{ $user->role?->label() }}</strong>. Penautan laporan masyarakat ke data kejadian dilakukan oleh <strong>Operator</strong> atau <strong>Admin</strong>.
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection
