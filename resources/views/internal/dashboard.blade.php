@extends('layouts.internal', [
    'title' => 'Dashboard Pengelolaan Internal — TanggapTerang',
    'internalMenu' => 'dashboard',
])

@php
    $user = auth()->user();
@endphp

@section('content')
    <!-- Dashboard Header & Role Context -->
    <section class="bg-white border border-[#E2E8F0] rounded-lg p-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="space-y-1">
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-2 py-0.5 rounded bg-[#0F172A] text-white font-telemetry text-[10px] font-bold uppercase tracking-wider">
                    Panel Pengelolaan Informasi Publik
                </span>
                <span class="px-2 py-0.5 rounded bg-blue-50 text-[#2563EB] border border-blue-200 font-telemetry text-[10px] font-bold uppercase tracking-wider">
                    Wewenang: {{ $user->role?->label() ?? $user->role }}
                </span>
                <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 border border-amber-300 font-telemetry text-[10px] font-bold uppercase tracking-wider">
                    Data Simulasi Prototype
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#0F172A]">
                Dashboard Verifikasi Laporan &amp; Catatan Tindak Lanjut
            </h1>
            <p class="text-sm text-slate-600">
                Ringkasan antrean laporan masyarakat, status verifikasi informasi kejadian, dan perkembangan pencatatan tindak lanjut tim TanggapTerang.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <a href="{{ route('internal.reports.index') }}"
               class="px-4 py-2.5 rounded-lg border border-[#CBD5E1] bg-white hover:bg-slate-50 text-[#0F172A] font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[42px] inline-flex items-center gap-1.5">
                <span>Kelola Laporan Masuk ({{ $stats['pending_reports'] }} Antrean)</span>
            </a>
            @if($user->canManageOperations())
                <a href="{{ route('internal.incidents.create') }}"
                   class="px-4 py-2.5 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[42px] inline-flex items-center gap-1.5">
                    <span>+ Buat Data Kejadian Baru</span>
                </a>
            @endif
        </div>
    </section>

    <!-- Row 1: Ringkasan Laporan Masyarakat (4 Metrics) -->
    <section aria-label="Statistik Laporan Masyarakat" class="space-y-2">
        <div class="flex items-center justify-between">
            <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-slate-600">
                1. Ringkasan Laporan Masyarakat (Verifikasi Informasi)
            </h2>
            <a href="{{ route('internal.reports.index') }}" class="font-telemetry text-xs font-bold text-[#2563EB] hover:underline">
                Lihat Semua Laporan &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            <div class="bg-white border border-[#E2E8F0] rounded-lg p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-slate-500">Total Laporan Masuk</span>
                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-telemetry text-[10px] font-bold uppercase">Simulasi</span>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="font-telemetry text-3xl font-bold text-[#0F172A]">{{ $stats['total_reports'] }}</span>
                    <span class="text-xs text-slate-500">{{ $stats['unlinked_reports'] }} belum terhubung</span>
                </div>
            </div>

            <div class="bg-white border-l-4 border-[#D97706] border-y border-r border-[#FCD34D] rounded-lg p-4 flex flex-col justify-between">
                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#92400E]">Menunggu Verifikasi</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="font-telemetry text-3xl font-bold text-[#D97706]">{{ $stats['pending_reports'] }}</span>
                    <a href="{{ route('internal.reports.index', ['verification_status' => 'menunggu_verifikasi']) }}" class="font-telemetry text-xs font-bold text-[#92400E] hover:underline">
                        Periksa &rarr;
                    </a>
                </div>
            </div>

            <div class="bg-white border-l-4 border-[#2563EB] border-y border-r border-[#93C5FD] rounded-lg p-4 flex flex-col justify-between">
                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#1E40AF]">Laporan Terverifikasi</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="font-telemetry text-3xl font-bold text-[#2563EB]">{{ $stats['verified_reports'] }}</span>
                    <a href="{{ route('internal.reports.index', ['verification_status' => 'terverifikasi']) }}" class="font-telemetry text-xs font-bold text-[#1E40AF] hover:underline">
                        Filter &rarr;
                    </a>
                </div>
            </div>

            <div class="bg-white border-l-4 border-[#DC2626] border-y border-r border-[#FCA5A5] rounded-lg p-4 flex flex-col justify-between">
                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#991B1B]">Laporan Ditolak / Duplikat</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="font-telemetry text-3xl font-bold text-[#DC2626]">{{ $stats['rejected_reports'] }}</span>
                    <span class="font-telemetry text-xs text-slate-600">+ {{ $stats['duplicate_reports'] }} Duplikat</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Row 2: Ringkasan Kejadian & Tindak Lanjut (4 Metrics) -->
    <section aria-label="Statistik Kejadian dan Tindak Lanjut" class="space-y-2">
        <div class="flex items-center justify-between">
            <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-slate-600">
                2. Ringkasan Data Kejadian (Status Tindak Lanjut Terpisah)
            </h2>
            <a href="{{ route('internal.incidents.index') }}" class="font-telemetry text-xs font-bold text-[#2563EB] hover:underline">
                Kelola Semua Kejadian &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            <div class="bg-white border border-[#E2E8F0] rounded-lg p-4 flex flex-col justify-between">
                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-slate-500">Total Kejadian</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="font-telemetry text-3xl font-bold text-[#0F172A]">{{ $stats['total_incidents'] }}</span>
                    <span class="font-telemetry text-xs text-slate-500 uppercase">Terdaftar</span>
                </div>
            </div>

            <div class="bg-white border border-[#E2E8F0] rounded-lg p-4 flex flex-col justify-between">
                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-slate-600">Belum Ditindaklanjuti</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="font-telemetry text-3xl font-bold text-slate-800">{{ $stats['unhandled_incidents'] }}</span>
                    <a href="{{ route('internal.incidents.index', ['follow_up_status' => 'belum_ditindaklanjuti']) }}" class="font-telemetry text-xs font-bold text-slate-700 hover:underline">
                        Filter &rarr;
                    </a>
                </div>
            </div>

            <div class="bg-white border-l-4 border-[#D97706] border-y border-r border-[#FCD34D] rounded-lg p-4 flex flex-col justify-between">
                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#92400E]">Sedang Ditindaklanjuti / Dipantau</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="font-telemetry text-3xl font-bold text-[#D97706]">{{ $stats['active_followup_incidents'] }}</span>
                    <a href="{{ route('internal.incidents.index', ['follow_up_status' => 'sedang_ditindaklanjuti']) }}" class="font-telemetry text-xs font-bold text-[#92400E] hover:underline">
                        Filter &rarr;
                    </a>
                </div>
            </div>

            <div class="bg-white border-l-4 border-[#059669] border-y border-r border-[#6EE7B7] rounded-lg p-4 flex flex-col justify-between">
                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#065F46]">Kejadian Selesai</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="font-telemetry text-3xl font-bold text-[#059669]">{{ $stats['completed_incidents'] }}</span>
                    <a href="{{ route('internal.incidents.index', ['follow_up_status' => 'selesai']) }}" class="font-telemetry text-xs font-bold text-[#065F46] hover:underline">
                        Filter &rarr;
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Row 3: Latest Reports & Latest Incidents Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        <!-- Latest Reports (6 cols) -->
        <section class="lg:col-span-6 bg-white border border-[#E2E8F0] rounded-lg overflow-hidden">
            <div class="p-4 border-b border-[#E2E8F0] bg-[#F8FAFC] flex items-center justify-between">
                <div>
                    <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                        Daftar Laporan Masyarakat Terbaru
                    </h2>
                    <p class="text-xs text-slate-500">Klik nomor tiket atau judul untuk meninjau detail &amp; aksi verifikasi.</p>
                </div>
                <a href="{{ route('internal.reports.index') }}" class="font-telemetry text-xs font-bold text-[#2563EB] hover:underline">
                    Semua &rarr;
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($latestReports as $report)
                    <div class="p-4 hover:bg-slate-50 transition space-y-2">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('internal.reports.show', $report) }}"
                                   class="px-2 py-0.5 rounded bg-[#0F172A] text-white font-telemetry text-xs font-bold hover:bg-slate-800">
                                    {{ $report->ticket_number }}
                                </a>
                                @if($report->is_simulation)
                                    <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 font-telemetry text-[10px] font-bold uppercase">
                                        Simulasi
                                    </span>
                                @endif
                            </div>
                            <x-verification-badge :status="$report->verification_status" />
                        </div>

                        <h3 class="text-sm font-bold text-[#0F172A]">
                            <a href="{{ route('internal.reports.show', $report) }}" class="hover:text-[#2563EB]">
                                {{ $report->title }}
                            </a>
                        </h3>

                        <div class="flex flex-wrap items-center justify-between gap-2 text-xs font-telemetry text-slate-500">
                            <span>Lokasi: {{ $report->location_name }}</span>
                            <span>
                                @if($report->incident)
                                    Terhubung: <strong class="text-[#059669]">{{ \Illuminate\Support\Str::limit($report->incident->title, 28) }}</strong>
                                @else
                                    <span class="text-amber-700 font-bold">Belum Terhubung ke Kejadian</span>
                                @endif
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-sm text-slate-500">
                        Belum ada laporan masyarakat yang masuk.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Latest Incidents (6 cols) -->
        <section class="lg:col-span-6 bg-white border border-[#E2E8F0] rounded-lg overflow-hidden">
            <div class="p-4 border-b border-[#E2E8F0] bg-[#F8FAFC] flex items-center justify-between">
                <div>
                    <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                        Daftar Kejadian Terbaru
                    </h2>
                    <p class="text-xs text-slate-500">Menampilkan status verifikasi dan status tindak lanjut secara terpisah.</p>
                </div>
                <a href="{{ route('internal.incidents.index') }}" class="font-telemetry text-xs font-bold text-[#2563EB] hover:underline">
                    Semua &rarr;
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($latestIncidents as $incident)
                    <div class="p-4 hover:bg-slate-50 transition space-y-2.5">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-1.5">
                                @if($incident->is_simulation)
                                    <span class="px-1.5 py-0.5 rounded bg-slate-900 text-white font-telemetry text-[10px] font-bold uppercase">
                                        Simulasi
                                    </span>
                                @endif
                                <x-severity-badge :severity="$incident->severity" />
                            </div>
                            <span class="font-telemetry text-xs text-slate-500">
                                {{ $incident->occurred_at?->format('d M Y, H:i') }} WIB
                            </span>
                        </div>

                        <h3 class="text-sm font-bold text-[#0F172A]">
                            <a href="{{ route('internal.incidents.edit', $incident) }}" class="hover:text-[#2563EB]">
                                {{ $incident->title }}
                            </a>
                        </h3>

                        <div class="flex flex-wrap items-center gap-2">
                            <x-verification-badge :status="$incident->verification_status" />
                            <x-followup-badge :status="$incident->follow_up_status" />
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-2 text-xs font-telemetry text-slate-500 pt-1">
                            <span>{{ $incident->reports_count }} Laporan • {{ $incident->sources_count }} Sumber • {{ $incident->follow_up_records_count }} Tindak Lanjut</span>
                            <a href="{{ route('internal.incidents.edit', $incident) }}" class="text-[#2563EB] font-bold hover:underline">
                                Kelola Kejadian &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-sm text-slate-500">
                        Belum ada data kejadian yang tercatat.
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <!-- Row 4: Ringkasan Aktivitas Verifikasi & Tindak Lanjut Terbaru -->
    <section class="bg-white border border-[#E2E8F0] rounded-lg p-5 space-y-4">
        <div class="border-b border-slate-100 pb-3 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                    Ringkasan Aktivitas Internal Terbaru (Audit Trail Lengkap)
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Menampilkan petugas pencatat dan alasan verifikasi internal secara lengkap untuk kebutuhan audit tim.
                </p>
            </div>
            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-telemetry text-[10px] font-bold uppercase">
                Mode Internal Aktif
            </span>
        </div>

        <x-audit-timeline
            :verificationHistories="$recentVerifications"
            :followUpRecords="$recentFollowUps"
            :isInternal="true"
        />
    </section>
@endsection
