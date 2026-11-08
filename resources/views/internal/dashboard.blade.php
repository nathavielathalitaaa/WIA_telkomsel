@extends('layouts.internal', [
    'title' => 'Dashboard Pengelolaan Internal — TanggapTerang',
    'internalMenu' => 'dashboard',
])

@php
    $user = auth()->user();
    $roleVal = $user->role?->value ?? (string) $user->role;
    $isAdmin = $user->isAdmin();
    $isVerifikator = $roleVal === 'verifikator';
    $isOperator = $roleVal === 'operator';

    [$dashboardTitle, $dashboardSubtitle, $roleBannerTone, $roleFocusPoints] = match ($roleVal) {
        'verifikator' => [
            'Dashboard Tim Verifikator — Pemeriksaan & Validasi Informasi',
            'Fokus utama: memeriksa laporan masyarakat yang masuk, mencocokkan rujukan sumber informasi terbuka, serta memverifikasi informasi klaim/respons bantuan masyarakat.',
            'bg-blue-50 border-blue-200 text-blue-950',
            [
                'Verifikasi Laporan Warga (' . $stats['pending_reports'] . ' antrean)',
                'Verifikasi Kejadian & Rujukan Sumber (' . ($stats['pending_incidents'] ?? 0) . ' antrean)',
                'Verifikasi Respons Bantuan (' . ($stats['pending_aid_responses'] ?? 0) . ' menunggu pemeriksaan)',
            ],
        ],
        'operator' => [
            'Dashboard Tim Operator — Pengelolaan Kejadian & Tindak Lanjut Operasional',
            'Fokus utama: membuat & memperbarui data kejadian, menautkan laporan warga, mencatat tindak lanjut operasional, serta memperbarui penyaluran dan mengonfirmasi respons bantuan.',
            'bg-emerald-50 border-emerald-200 text-emerald-950',
            [
                'Penautan Laporan Warga ke Kejadian (' . $stats['unlinked_reports'] . ' belum tertaut)',
                'Pembaruan Tindak Lanjut Kejadian (' . $stats['unhandled_incidents'] . ' belum ditindaklanjuti)',
                'Penyaluran & Konfirmasi Bantuan (' . ($stats['unconfirmed_aid_responses'] ?? 0) . ' belum dikonfirmasi)',
            ],
        ],
        default => [
            'Dashboard Admin Sistem — Pengelolaan Menyeluruh Verifikasi, Operasional, & Sistem',
            'Wewenang penuh untuk mengawasi dan menjalankan seluruh alur verifikasi informasi, pengelolaan kejadian, tindak lanjut operasional, serta konfirmasi respons bantuan masyarakat.',
            'bg-amber-50 border-amber-300 text-amber-950',
            [
                'Pengawasan Sistem, Akun Tim Internal (' . ($stats['total_users'] ?? 3) . ' akun), & Master Sumber Terbuka (' . ($stats['total_sources'] ?? 0) . ' sumber)',
                'Wewenang Penuh Verifikasi Laporan (' . $stats['pending_reports'] . ' antrean) & Respons (' . ($stats['pending_aid_responses'] ?? 0) . ' antrean)',
                'Wewenang Penuh Operasional Kejadian & Konfirmasi Respons Bantuan (' . ($stats['unconfirmed_aid_responses'] ?? 0) . ' respons belum terkonfirmasi)',
            ],
        ],
    };
@endphp

@section('content')
    <!-- Dashboard Header & Role Context -->
    <section class="bg-white border border-[#E2E8F0] rounded-lg p-5 space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-[#0F172A] text-white font-telemetry text-[10px] font-bold uppercase tracking-wider">
                        Panel Pengelolaan Informasi Publik
                    </span>
                    <span class="px-2 py-0.5 rounded bg-blue-50 text-[#2563EB] border border-blue-200 font-telemetry text-[10px] font-bold uppercase tracking-wider">
                        Role Aktif: {{ $user->role?->label() ?? $user->role }}
                    </span>
                    <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 border border-amber-300 font-telemetry text-[10px] font-bold uppercase tracking-wider">
                        Data Simulasi Prototype
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-[#0F172A]">
                    {{ $dashboardTitle }}
                </h1>
                <p class="text-sm text-slate-600">
                    {{ $dashboardSubtitle }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                @if($isVerifikator)
                    <a href="{{ route('internal.reports.index', ['verification_status' => 'menunggu_verifikasi']) }}"
                       class="px-4 py-2.5 rounded-lg btn-brand-gradient font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[42px] inline-flex items-center gap-1.5 shadow-xs">
                        <span>Periksa Antrean Verifikasi ({{ $stats['pending_reports'] }})</span>
                    </a>
                    <a href="{{ route('internal.incidents.index') }}"
                       class="px-4 py-2.5 rounded-lg border border-[#CBD5E1] bg-white hover:bg-slate-50 text-[#0F172A] font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[42px] inline-flex items-center gap-1.5">
                        <span>Tinjau Verifikasi Kejadian</span>
                    </a>
                @elseif($isOperator)
                    <a href="{{ route('internal.incidents.create') }}"
                       class="px-4 py-2.5 rounded-lg btn-brand-gradient font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[42px] inline-flex items-center gap-1.5 shadow-xs">
                        <span>+ Buat Data Kejadian Baru</span>
                    </a>
                    <a href="{{ route('internal.reports.index', ['verification_status' => 'terverifikasi']) }}"
                       class="px-4 py-2.5 rounded-lg border border-[#CBD5E1] bg-white hover:bg-slate-50 text-[#0F172A] font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[42px] inline-flex items-center gap-1.5">
                        <span>Tautkan Laporan Warga ({{ $stats['unlinked_reports'] }} Belum Tertaut)</span>
                    </a>
                @else
                    <a href="{{ route('internal.reports.index') }}"
                       class="px-4 py-2.5 rounded-lg border border-[#CBD5E1] bg-white hover:bg-slate-50 text-[#0F172A] font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[42px] inline-flex items-center gap-1.5">
                        <span>Kelola Laporan Masuk ({{ $stats['pending_reports'] }} Antrean)</span>
                    </a>
                    <a href="{{ route('internal.incidents.create') }}"
                       class="px-4 py-2.5 rounded-lg btn-brand-gradient font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[42px] inline-flex items-center gap-1.5 shadow-xs">
                        <span>+ Buat Data Kejadian Baru</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Role Responsibility Summary Strip -->
        <div class="rounded-lg border p-3.5 {{ $roleBannerTone }}">
            <div class="font-telemetry text-[11px] font-bold uppercase tracking-wider mb-1.5">
                Cakupan Tugas &amp; Wewenang Role {{ $user->role?->label() ?? $user->role }}:
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-xs">
                @foreach($roleFocusPoints as $point)
                    <div class="flex items-start gap-2 bg-white/80 rounded px-3 py-2 border border-black/5">
                        <span class="font-bold text-slate-900">&bull;</span>
                        <span class="font-medium">{{ $point }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if($isAdmin)
        <!-- Admin System Management Overview -->
        <section aria-label="Ringkasan Pengelolaan Sistem Admin" class="bg-white border border-[#E2E8F0] rounded-lg p-4 sm:p-5 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-2.5">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-amber-500 text-slate-950 font-telemetry text-[10px] font-bold uppercase">
                        Khusus Admin
                    </span>
                    <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                        Pengawasan Sistem, Akun Tim Internal, &amp; Master Sumber Informasi
                    </h2>
                </div>
                <span class="font-telemetry text-xs text-slate-500">
                    {{ $stats['total_sources'] ?? 0 }} Sumber Terbuka Terdaftar • {{ ($internalUsers ?? collect())->count() }} Akun Tim Internal
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @foreach(($internalUsers ?? collect()) as $staff)
                    <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-3 flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-[#0F172A] truncate">{{ $staff->name }}</div>
                            <div class="font-telemetry text-[11px] text-slate-500 truncate">{{ $staff->email }}</div>
                        </div>
                        <span class="px-2 py-0.5 rounded bg-slate-900 text-white font-telemetry text-[10px] font-bold uppercase shrink-0">
                            {{ $staff->role?->label() ?? $staff->role }}
                        </span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Row 1: Ringkasan Laporan Masyarakat (4 Metrics) -->
    <section aria-label="Statistik Laporan Masyarakat" class="space-y-2">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-slate-600">
                    1. Ringkasan Laporan Masyarakat &amp; Antrean Verifikasi
                </h2>
                @if($isVerifikator)
                    <span class="px-2 py-0.5 rounded bg-blue-600 text-white font-telemetry text-[10px] font-bold uppercase">
                        Tugas Utama Verifikator
                    </span>
                @elseif($isOperator)
                    <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-telemetry text-[10px] font-bold uppercase">
                        Penautan ke Kejadian (Operator)
                    </span>
                @endif
            </div>
            <a href="{{ route('internal.reports.index') }}" class="font-telemetry text-xs font-bold text-[#2563EB] hover:underline">
                {{ $isVerifikator ? 'Buka Antrean Verifikasi Laporan' : 'Lihat Semua Laporan' }} &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            <a href="{{ route('internal.reports.index') }}"
               data-tooltip="Buka seluruh daftar laporan masuk"
               class="bg-white border border-[#E2E8F0] rounded-lg p-4 flex flex-col justify-between card-interactive cursor-pointer group">
                <div class="flex items-center justify-between">
                    <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-slate-500 group-hover:text-[#0F172A] transition-colors">Total Laporan Masuk</span>
                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-telemetry text-[10px] font-bold uppercase">Simulasi</span>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="font-telemetry text-3xl font-bold text-[#0F172A]">{{ $stats['total_reports'] }}</span>
                    <span class="text-xs text-slate-500">{{ $stats['unlinked_reports'] }} belum terhubung</span>
                </div>
            </a>

            <a href="{{ route('internal.reports.index', ['verification_status' => 'menunggu_verifikasi']) }}"
               data-tooltip="Periksa antrean laporan yang menunggu verifikasi"
               class="bg-white border-l-4 border-[#D97706] border-y border-r border-[#FCD34D] rounded-lg p-4 flex flex-col justify-between card-interactive cursor-pointer group">
                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#92400E]">Menunggu Verifikasi</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="font-telemetry text-3xl font-bold text-[#D97706]">{{ $stats['pending_reports'] }}</span>
                    <span class="font-telemetry text-xs font-bold text-[#92400E] group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                        Periksa &rarr;
                    </span>
                </div>
            </a>

            <a href="{{ route('internal.reports.index', ['verification_status' => 'terverifikasi']) }}"
               data-tooltip="Buka daftar laporan yang sudah terverifikasi"
               class="bg-white border-l-4 border-[#2563EB] border-y border-r border-[#93C5FD] rounded-lg p-4 flex flex-col justify-between card-interactive cursor-pointer group">
                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#1E40AF]">Laporan Terverifikasi</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="font-telemetry text-3xl font-bold text-[#2563EB]">{{ $stats['verified_reports'] }}</span>
                    <span class="font-telemetry text-xs font-bold text-[#1E40AF] group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                        Filter &rarr;
                    </span>
                </div>
            </a>

            <a href="{{ route('internal.reports.index', ['verification_status' => 'ditolak']) }}"
               data-tooltip="Buka daftar laporan yang ditolak atau duplikat"
               class="bg-white border-l-4 border-[#DC2626] border-y border-r border-[#FCA5A5] rounded-lg p-4 flex flex-col justify-between card-interactive cursor-pointer group">
                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#991B1B]">Laporan Ditolak / Duplikat</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="font-telemetry text-3xl font-bold text-[#DC2626]">{{ $stats['rejected_reports'] }}</span>
                    <span class="font-telemetry text-xs text-slate-600">+ {{ $stats['duplicate_reports'] }} Duplikat</span>
                </div>
            </a>
        </div>
    </section>

    <!-- Row 2: Ringkasan Kejadian & Tindak Lanjut (4 Metrics) -->
    <section aria-label="Statistik Kejadian dan Tindak Lanjut" class="space-y-2">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-slate-600">
                    2. Ringkasan Data Kejadian &amp; Status Tindak Lanjut Operasional
                </h2>
                @if($isOperator)
                    <span class="px-2 py-0.5 rounded bg-emerald-600 text-white font-telemetry text-[10px] font-bold uppercase">
                        Tugas Utama Operator
                    </span>
                @elseif($isVerifikator)
                    <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-telemetry text-[10px] font-bold uppercase">
                        Verifikasi Kejadian (Verifikator)
                    </span>
                @endif
            </div>
            <a href="{{ route('internal.incidents.index') }}" class="font-telemetry text-xs font-bold text-[#2563EB] hover:underline">
                {{ $isVerifikator ? 'Tinjau Kejadian & Sumber' : 'Kelola Semua Kejadian' }} &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            <a href="{{ route('internal.incidents.index') }}"
               data-tooltip="Buka seluruh daftar kejadian bencana"
               class="bg-white border border-[#E2E8F0] rounded-lg p-4 flex flex-col justify-between card-interactive cursor-pointer group">
                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-slate-500 group-hover:text-[#0F172A] transition-colors">Total Kejadian</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="font-telemetry text-3xl font-bold text-[#0F172A]">{{ $stats['total_incidents'] }}</span>
                    <span class="font-telemetry text-xs text-slate-500">{{ $stats['pending_incidents'] ?? 0 }} menunggu verifikasi</span>
                </div>
            </a>

            <a href="{{ route('internal.incidents.index', ['follow_up_status' => 'belum_ditindaklanjuti']) }}"
               data-tooltip="Filter kejadian yang belum ada catatan tindak lanjut"
               class="bg-white border border-[#E2E8F0] rounded-lg p-4 flex flex-col justify-between card-interactive cursor-pointer group">
                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-slate-600 group-hover:text-[#0F172A] transition-colors">Belum Ditindaklanjuti</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="font-telemetry text-3xl font-bold text-slate-800">{{ $stats['unhandled_incidents'] }}</span>
                    <span class="font-telemetry text-xs font-bold text-slate-700 group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                        Filter &rarr;
                    </span>
                </div>
            </a>

            <a href="{{ route('internal.incidents.index', ['follow_up_status' => 'sedang_ditindaklanjuti']) }}"
               data-tooltip="Filter kejadian yang sedang dalam penanganan aktif"
               class="bg-white border-l-4 border-[#D97706] border-y border-r border-[#FCD34D] rounded-lg p-4 flex flex-col justify-between card-interactive cursor-pointer group">
                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#92400E]">Sedang Ditindaklanjuti / Dipantau</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="font-telemetry text-3xl font-bold text-[#D97706]">{{ $stats['active_followup_incidents'] }}</span>
                    <span class="font-telemetry text-xs font-bold text-[#92400E] group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                        Filter &rarr;
                    </span>
                </div>
            </a>

            <a href="{{ route('internal.incidents.index', ['follow_up_status' => 'selesai']) }}"
               data-tooltip="Filter kejadian yang telah selesai ditindaklanjuti"
               class="bg-white border-l-4 border-[#059669] border-y border-r border-[#6EE7B7] rounded-lg p-4 flex flex-col justify-between card-interactive cursor-pointer group">
                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#065F46]">Kejadian Selesai</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="font-telemetry text-3xl font-bold text-[#059669]">{{ $stats['completed_incidents'] }}</span>
                    <span class="font-telemetry text-xs font-bold text-[#065F46] group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                        Filter &rarr;
                    </span>
                </div>
            </a>
        </div>
    </section>

    <!-- Row 2B: Ringkasan Respons Bantuan Publik (Tawaran Rencana vs Laporan Bantuan Sudah Diberikan) -->
    <section aria-label="Statistik Respons Bantuan Publik" class="space-y-2">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-slate-600">
                    3. Ringkasan Respons Bantuan Publik (Tawaran Bantuan &amp; Laporan Bantuan Sudah Diberikan)
                </h2>
                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 font-telemetry text-[10px] font-bold uppercase">
                    {{ $user->canManageOperations() ? 'Pemeriksaan & Konfirmasi (Operator / Admin)' : 'Mode Tinjau (Verifikator)' }}
                </span>
            </div>
            <a href="{{ route('internal.incidents.index') }}" class="font-telemetry text-xs font-bold text-[#2563EB] hover:underline">
                Periksa pada Halaman Kejadian &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            <a href="{{ route('internal.incidents.index') }}"
               data-tooltip="Buka daftar kejadian untuk mengelola respons bantuan"
               class="bg-white border border-[#E2E8F0] rounded-lg p-4 flex flex-col justify-between card-interactive cursor-pointer group">
                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-slate-500 group-hover:text-[#0F172A] transition-colors">Total Respons Tercatat</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="font-telemetry text-3xl font-bold text-[#0F172A]">{{ $stats['total_aid_responses'] ?? 0 }}</span>
                    <span class="font-telemetry text-xs text-slate-500">Entri publik</span>
                </div>
            </a>

            <a href="{{ route('internal.incidents.index') }}"
               data-tooltip="Lihat komitmen dan rencana bantuan masuk"
               class="bg-white border-l-4 border-[#2563EB] border-y border-r border-[#93C5FD] rounded-lg p-4 flex flex-col justify-between card-interactive cursor-pointer group">
                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#1E40AF]">Tawaran / Rencana Bantuan</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="font-telemetry text-3xl font-bold text-[#2563EB]">{{ $stats['tawaran_rencana_count'] ?? 0 }}</span>
                    <span class="font-telemetry text-xs text-slate-500">Inisiatif / komitmen</span>
                </div>
            </a>

            <a href="{{ route('internal.incidents.index') }}"
               data-tooltip="Lihat laporan bantuan yang sudah diserahkan di lapangan"
               class="bg-white border-l-4 border-indigo-600 border-y border-r border-indigo-200 rounded-lg p-4 flex flex-col justify-between card-interactive cursor-pointer group">
                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-indigo-900">Laporan Bantuan Sudah Diberikan</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="font-telemetry text-3xl font-bold text-indigo-700">{{ $stats['laporan_penyerahan_count'] ?? 0 }}</span>
                    <span class="font-telemetry text-xs text-slate-500">Dilaporkan diserahkan</span>
                </div>
            </a>

            <a href="{{ route('internal.incidents.index') }}"
               data-tooltip="Lihat bantuan yang telah terkonfirmasi oleh petugas"
               class="bg-white border-l-4 border-[#059669] border-y border-r border-[#6EE7B7] rounded-lg p-4 flex flex-col justify-between card-interactive cursor-pointer group">
                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#065F46]">Penyerahan Dikonfirmasi</span>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="font-telemetry text-3xl font-bold text-[#059669]">{{ $stats['confirmed_aid_responses'] ?? 0 }}</span>
                    <span class="font-telemetry text-xs text-amber-800 font-semibold">{{ $stats['unconfirmed_aid_responses'] ?? 0 }} menunggu konfirmasi</span>
                </div>
            </a>
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
                            <span>{{ $incident->reports_count }} Laporan • {{ $incident->sources_count }} Sumber • {{ $incident->aid_responses_count ?? 0 }} Respons Bantuan</span>
                            <a href="{{ route('internal.incidents.edit', $incident) }}" class="text-[#2563EB] font-bold hover:underline">
                                {{ $isVerifikator ? 'Verifikasi & Tinjau' : ($isOperator ? 'Kelola Operasional' : 'Kelola Kejadian') }} &rarr;
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

    <!-- Row 3B: Daftar Respons Bantuan Masyarakat Terbaru (Tawaran vs Laporan Penyerahan) -->
    <section class="bg-white border border-[#E2E8F0] rounded-lg overflow-hidden">
        <div class="p-4 border-b border-[#E2E8F0] bg-[#F8FAFC] flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                    Daftar Respons Bantuan Publik Terbaru (Pemeriksaan &amp; Konfirmasi)
                </h2>
                <p class="text-xs text-slate-500">
                    Membedakan antara <strong>Tawaran / Rencana Bantuan</strong> dan <strong>Laporan Bantuan Sudah Diberikan</strong>. Konfirmasi dilakukan pada halaman kelola kejadian terkait.
                </p>
            </div>
            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 font-telemetry text-[10px] font-bold uppercase">
                {{ ($recentAidResponses ?? collect())->count() }} Entri Terbaru
            </span>
        </div>

        @if(($recentAidResponses ?? collect())->isEmpty())
            <div class="p-6 text-center text-sm text-slate-500">
                Belum ada respons bantuan publik yang tercatat.
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($recentAidResponses as $aid)
                    <div class="p-4 hover:bg-slate-50 transition flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                        <div class="space-y-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2 py-0.5 rounded bg-slate-900 text-white font-telemetry text-xs font-bold">
                                    {{ $aid->reference_code }}
                                </span>
                                <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase {{ $aid->isReportedDelivered() ? 'bg-indigo-50 text-indigo-900 border border-indigo-200' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                                    {{ $aid->submissionTypeLabel() }}
                                </span>
                                <div class="flex items-center gap-1">
                                    <span class="font-telemetry text-[9px] uppercase font-bold text-slate-500">Pemeriksaan:</span>
                                    <x-verification-badge :status="$aid->verification_status" />
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="font-telemetry text-[9px] uppercase font-bold text-slate-500">Operasional:</span>
                                    <x-aid-status-badge :status="$aid->status" />
                                </div>
                            </div>
                            <div class="text-sm font-bold text-[#0F172A]">
                                {{ $aid->aid_item_title }} <span class="font-telemetry text-xs font-semibold text-emerald-800">({{ $aid->quantity_label }})</span>
                            </div>
                            <div class="text-xs font-telemetry text-slate-500">
                                Organisasi: <strong class="text-slate-700">{{ $aid->organization_name }}</strong>
                                • Lokasi: <strong class="text-slate-700">{{ $aid->target_location }}</strong>
                                @if($aid->delivered_at)
                                    • Waktu Penyerahan (Dilaporkan): <strong class="text-indigo-800">{{ $aid->delivered_at->format('d M Y, H:i') }} WIB</strong>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if($aid->incident)
                                <a href="{{ route('internal.incidents.edit', $aid->incident) }}"
                                   class="px-3 py-1.5 rounded border border-[#CBD5E1] bg-white hover:bg-slate-100 text-[#0F172A] font-telemetry text-xs font-bold uppercase tracking-wider">
                                    {{ $user->canManageOperations() ? 'Periksa & Konfirmasi' : 'Tinjau pada Kejadian' }} &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Row 4: Ringkasan Aktivitas Verifikasi, Tindak Lanjut, & Respons Bantuan Terbaru -->
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
            :aidResponses="$recentAidResponses ?? collect()"
            :isInternal="true"
        />
    </section>
@endsection
