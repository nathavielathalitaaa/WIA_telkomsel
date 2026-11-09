@extends('layouts.app', [
    'title' => 'Tentang Prototype TanggapTerang — Informasi Kejadian & Transparansi Tindak Lanjut',
    'activeTab' => '',
])

@section('content')
    <!-- Hero Introduction Card -->
    <section class="bg-[#0F172A] text-white border border-slate-800 rounded-lg p-6 sm:p-8 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-5">
            <img src="{{ asset('images/logo-tanggap-terang.png') }}"
                 alt="Logo TanggapTerang"
                 class="h-16 sm:h-20 w-auto object-contain rounded-2xl self-start sm:self-center shrink-0">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-1 rounded bg-amber-400 text-[#0F172A] font-telemetry text-xs font-bold uppercase tracking-wider">
                        Prototype Kompetisi Multimedia In Action 2026
                    </span>
                    <span class="px-2.5 py-1 rounded bg-slate-800 text-slate-200 border border-slate-700 font-telemetry text-xs font-bold uppercase tracking-wider">
                        Data Historis BNPB &amp; Simulasi Terstruktur
                    </span>
                </div>
                <p class="font-telemetry text-xs text-slate-300 uppercase tracking-wider">
                    Platform Informasi Kejadian &amp; Transparansi Tindak Lanjut
                </p>
            </div>
        </div>

        <div class="max-w-3xl space-y-3">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight leading-tight">
                Papan Transparansi dan Koordinasi Informasi Kebencanaan
            </h1>
            <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                TanggapTerang menghubungkan laporan warga, kebutuhan di lokasi bencana, dan catatan perkembangan respons tim dalam satu tempat. Terlihat sebarannya, terlacak prosesnya, dan terukur penanganannya.
            </p>
        </div>

        <div class="pt-2 flex flex-wrap items-center gap-3">
            <a href="{{ route('public.monitor') }}"
               class="px-5 py-3 rounded-lg bg-white hover:bg-slate-100 text-[#0F172A] font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[44px] inline-flex items-center gap-2">
                <span>Buka Peta Pantau Kejadian</span>
                <span>&rarr;</span>
            </a>
            <a href="{{ route('public.reports.create') }}"
               class="px-5 py-3 rounded-lg btn-brand-gradient font-telemetry text-xs font-bold uppercase tracking-wider min-h-[44px] inline-flex items-center gap-2 shadow-xs">
                <span>+ Kirim Laporan Masyarakat</span>
            </a>
            <a href="{{ route('public.replay') }}"
               class="px-4 py-3 rounded-lg border border-slate-600 hover:bg-slate-800 text-slate-200 font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[44px] inline-flex items-center">
                Mode Replay Simulasi
            </a>
            <a href="{{ route('public.sitrep') }}"
               class="px-4 py-3 rounded-lg border border-slate-600 hover:bg-slate-800 text-slate-200 font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[44px] inline-flex items-center">
                Laporan SitRep
            </a>
        </div>
    </section>

    <!-- Mekanisme Penanganan Laporan & Respons Faktual -->
    <x-response-pipeline />

    <!-- Diagram Alur Transparansi & Klasifikasi Sumber Data -->
    <x-transparency-flow-diagram />

    <!-- Core Pillars: How Reporting, Verification, and Follow-Up Work -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white border border-[#E2E8F0] rounded-lg p-5 space-y-2.5">
            <div class="w-8 h-8 rounded bg-[#DC2626] text-white font-telemetry font-bold text-xs flex items-center justify-center">
                01
            </div>
            <h2 class="text-base font-bold text-[#0F172A]">
                Pelaporan Tanpa Akun &amp; Nomor Tiket Unik
            </h2>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                Masyarakat dapat mengirimkan laporan kejadian tanpa harus mendaftar akun. Setiap laporan memperoleh kode tiket acak non-sekuensial (contoh: <code>RPT-SIM-8K4M9P2X</code>) dan identitas pelapor disembunyikan dari publik.
            </p>
        </div>

        <div class="bg-white border border-[#E2E8F0] rounded-lg p-5 space-y-2.5">
            <div class="w-8 h-8 rounded bg-[#2563EB] text-white font-telemetry font-bold text-xs flex items-center justify-center">
                02
            </div>
            <h2 class="text-base font-bold text-[#0F172A]">
                Verifikasi Silang dengan Sumber Terbuka
            </h2>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                Laporan baru tidak otomatis tampil sebagai kejadian terverifikasi. Tim Verifikator memeriksa kesesuaian laporan warga dengan rujukan sumber informasi terbuka dan mencatat riwayat perubahan statusnya.
            </p>
        </div>

        <div class="bg-white border border-[#E2E8F0] rounded-lg p-5 space-y-2.5">
            <div class="w-8 h-8 rounded bg-[#059669] text-white font-telemetry font-bold text-xs flex items-center justify-center">
                03
            </div>
            <h2 class="text-base font-bold text-[#0F172A]">
                Pemisahan Status Verifikasi &amp; Tindak Lanjut
            </h2>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                TanggapTerang memisahkan secara tegas antara <strong>Status Verifikasi Informasi</strong> (kebenaran laporan) dan <strong>Status Tindak Lanjut Tim</strong> (perkembangan pengelolaan dan dokumentasi informasi kejadian).
            </p>
        </div>
    </section>

    <!-- Simulation & Scope Explanation -->
    <section class="bg-white border border-[#E2E8F0] rounded-lg p-5 sm:p-6 space-y-3">
        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
            Batasan Produk &amp; Ketentuan Data Simulasi
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs sm:text-sm text-slate-600 leading-relaxed">
            <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-4 space-y-1.5">
                <strong class="text-[#0F172A] block">Perbedaan Informasi Kejadian dan Tindak Lanjut</strong>
                <p>
                    Status tindak lanjut pada TanggapTerang mencatat aktivitas tim internal dalam menghimpun, mengelompokkan, dan memutakhirkan informasi kejadian. Ketiadaan catatan tindak lanjut pada TanggapTerang tidak berarti instansi eksternal tidak sedang menangani kejadian di lapangan.
                </p>
            </div>
            <div class="bg-[#FFFBEB] border border-[#FCD34D] rounded-lg p-4 space-y-1.5 text-[#92400E]">
                <strong class="text-[#0F172A] block">Sumber Kejadian Nyata &amp; Batasan Simulasi</strong>
                <p>
                    Kejadian bencana riil ({{ $summary['historical_count'] }} kejadian) bersumber dari arsip resmi <strong>Pusdatin BNPB (DIBI)</strong>. Modul simulasi ({{ $summary['simulation_count'] }} skenario, {{ $summary['reports_count'] }} laporan warga) disediakan terpisah untuk demonstrasi alur tanpa mencampurkan data fiktif ke statistik riil.
                </p>
            </div>
        </div>
    </section>
@endsection
