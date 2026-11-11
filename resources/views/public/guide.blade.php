@extends('layouts.app', [
    'title' => 'Panduan Penggunaan — Papan Transparansi TanggapTerang',
    'activeTab' => 'panduan',
])

@section('content')
<div class="space-y-8 sm:space-y-10 lg:space-y-12">
    <!-- Header Panduan (Master Hero Card Alignment) -->
    <section class="bg-[#173B70] text-white border border-[#315D91] rounded-2xl p-5 sm:p-7 space-y-4 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div class="space-y-2 max-w-2xl">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold uppercase tracking-wider bg-[#315D91] text-white border border-blue-300/30 font-telemetry">
                        Panduan Penggunaan
                    </span>
                    <span class="text-xs text-blue-200">•</span>
                    <span class="text-xs text-blue-100 font-telemetry">Panduan Partisipasi Publik</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-tight">
                    Panduan Penggunaan TanggapTerang
                </h1>
                <p class="text-xs sm:text-sm text-blue-100 leading-relaxed">
                    Panduan ringkas cara melapor, menyimpan kode tiket, melacak proses verifikasi, dan memantau kejadian di peta.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="{{ route('public.track') }}"
                   class="px-4 py-2.5 rounded-lg bg-white hover:bg-slate-100 text-[#173B70] font-telemetry text-xs font-bold uppercase tracking-wider transition inline-flex items-center gap-1.5 shadow-2xs min-h-[38px]">
                    <svg class="w-3.5 h-3.5 text-[#173B70]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Lacak Tiket</span>
                </a>
                <a href="{{ route('public.reports.create') }}"
                   class="px-4 py-2.5 rounded-lg btn-brand-gradient text-white font-telemetry text-xs font-bold uppercase tracking-wider inline-flex items-center gap-1.5 shadow-xs transition min-h-[38px]">
                    <span class="font-extrabold text-sm leading-none">+</span>
                    <span>Kirim Laporan Warga</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Ringkasan Alur 5 Langkah (Horizontal Flow Illustration) -->
    <section class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 space-y-4" aria-label="Alur 5 Langkah Panduan">
        <div class="border-b border-slate-100 pb-3">
            <h2 class="font-telemetry text-xs sm:text-sm font-bold uppercase tracking-wider text-[#202938]">
                Alur Praktis 5 Langkah
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Semua fitur publik dapat diakses langsung tanpa registrasi atau kewajiban login.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- 1. Laporkan Kejadian Tanpa Akun -->
            <div class="bg-slate-50/70 border border-slate-100 rounded-xl p-3.5 space-y-2 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="w-6 h-6 rounded-full bg-[#173B70] text-white text-[11px] font-bold flex items-center justify-center font-telemetry">1</span>
                        <span class="text-[9px] font-bold uppercase text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded font-telemetry">Tanpa Akun</span>
                    </div>
                    <h3 class="text-xs font-bold text-[#202938] mt-2">Laporkan Kejadian</h3>
                    <p class="text-[11px] text-slate-500 leading-relaxed mt-1">
                        Buka formulir lapor, isi lokasi serta kebutuhan mendesak. Tidak perlu daftar akun.
                    </p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('public.reports.create') }}" class="text-[11px] font-bold font-telemetry link-text">
                        Form lapor
                    </a>
                </div>
            </div>

            <!-- 2. Simpan Kode Tiket -->
            <div class="bg-slate-50/70 border border-slate-100 rounded-xl p-3.5 space-y-2 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="w-6 h-6 rounded-full bg-[#173B70] text-white text-[11px] font-bold flex items-center justify-center font-telemetry">2</span>
                        <span class="text-[9px] font-bold uppercase text-slate-600 bg-slate-200 px-1.5 py-0.5 rounded font-telemetry">Kode Unik</span>
                    </div>
                    <h3 class="text-xs font-bold text-[#202938] mt-2">Simpan Kode Tiket</h3>
                    <p class="text-[11px] text-slate-500 leading-relaxed mt-1">
                        Setelah kirim, catat kode unik (misal: <code class="font-bold text-[#173B70]">RPT-SIM-8K4M9P2X</code>) untuk penelusuran mandiri.
                    </p>
                </div>
                <div class="pt-2 text-[11px] text-slate-400 font-telemetry">
                    Kunci Lacak Mandiri
                </div>
            </div>

            <!-- 3. Lacak Perkembangan Laporan -->
            <div class="bg-slate-50/70 border border-slate-100 rounded-xl p-3.5 space-y-2 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="w-6 h-6 rounded-full bg-[#173B70] text-white text-[11px] font-bold flex items-center justify-center font-telemetry">3</span>
                        <span class="text-[9px] font-bold uppercase text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded font-telemetry">Transparan</span>
                    </div>
                    <h3 class="text-xs font-bold text-[#202938] mt-2">Lacak Perkembangan</h3>
                    <p class="text-[11px] text-slate-500 leading-relaxed mt-1">
                        Masukkan kode tiket di menu Lacak untuk memantau catatan verifikator dan kronologi lapangan.
                    </p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('public.track') }}" class="text-[11px] font-bold font-telemetry link-text">
                        Lacak tiket
                    </a>
                </div>
            </div>

            <!-- 4. Pantau Kejadian Melalui Peta -->
            <div class="bg-slate-50/70 border border-slate-100 rounded-xl p-3.5 space-y-2 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="w-6 h-6 rounded-full bg-[#173B70] text-white text-[11px] font-bold flex items-center justify-center font-telemetry">4</span>
                        <span class="text-[9px] font-bold uppercase text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded font-telemetry">Geospasial</span>
                    </div>
                    <h3 class="text-xs font-bold text-[#202938] mt-2">Pantau Melalui Peta</h3>
                    <p class="text-[11px] text-slate-500 leading-relaxed mt-1">
                        Peta interaktif menampilkan sebaran bencana aktif, kebutuhan warga, dan respons logistik.
                    </p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('public.monitor') }}" class="text-[11px] font-bold font-telemetry link-text">
                        Buka peta
                    </a>
                </div>
            </div>

            <!-- 5. Pahami Status Verifikasi & Tindak Lanjut -->
            <div class="bg-slate-50/70 border border-slate-100 rounded-xl p-3.5 space-y-2 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="w-6 h-6 rounded-full bg-[#059669] text-white text-[11px] font-bold flex items-center justify-center font-telemetry">5</span>
                        <span class="text-[9px] font-bold uppercase text-emerald-800 bg-emerald-100 px-1.5 py-0.5 rounded font-telemetry">Dua Dimensi</span>
                    </div>
                    <h3 class="text-xs font-bold text-[#202938] mt-2">Pahami Status</h3>
                    <p class="text-[11px] text-slate-500 leading-relaxed mt-1">
                        Bedakan status verifikasi fakta dari status tindak lanjut operasional di lapangan.
                    </p>
                </div>
                <div class="pt-2 text-[11px] text-emerald-700 font-telemetry font-semibold">
                    Akuntabel &amp; Sah
                </div>
            </div>
        </div>
    </section>

    <!-- Detail 5 Penjelasan Sesuai Standar Uji -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- 1. Apa itu TanggapTerang? -->
        <section id="apa-itu" class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4 sm:p-5 space-y-3">
            <div class="flex items-center gap-2.5 border-b border-slate-100 pb-2.5">
                <span class="w-2 h-2 rounded-full bg-[#173B70]" aria-hidden="true"></span>
                <h2 class="font-telemetry text-xs sm:text-sm font-bold uppercase tracking-wider text-[#202938]">
                    Apa itu TanggapTerang?
                </h2>
            </div>
            <p class="text-xs text-slate-600 leading-relaxed">
                TanggapTerang adalah platform transparansi informasi kebencanaan dengan prinsip <em>Terlihat, Terlacak, dan Terukur</em>. Menghubungkan laporan warga, kebutuhan lapangan, dan respons bantuan secara terbuka.
            </p>
            <div class="grid grid-cols-3 gap-2 pt-1 text-[11px] font-telemetry text-slate-600">
                <div class="p-2 bg-slate-50 rounded-lg">
                    <strong class="text-[#173B70] block">Terlihat</strong>
                    Peta sebaran geospasial
                </div>
                <div class="p-2 bg-slate-50 rounded-lg">
                    <strong class="text-[#315D91] block">Terlacak</strong>
                    Nomor tiket mandiri
                </div>
                <div class="p-2 bg-slate-50 rounded-lg">
                    <strong class="text-emerald-700 block">Terukur</strong>
                    4 kategori logistik
                </div>
            </div>
        </section>

        <!-- 2. Bagaimana Masyarakat Melaporkan Kejadian? -->
        <section id="cara-lapor" class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4 sm:p-5 space-y-3">
            <div class="flex items-center gap-2.5 border-b border-slate-100 pb-2.5">
                <span class="w-2 h-2 rounded-full bg-[#315D91]" aria-hidden="true"></span>
                <h2 class="font-telemetry text-xs sm:text-sm font-bold uppercase tracking-wider text-[#202938]">
                    Bagaimana Masyarakat Melaporkan Kejadian?
                </h2>
            </div>
            <p class="text-xs text-slate-600 leading-relaxed">
                Warga cukup membuka formulir pelaporan, memilih hierarki wilayah, mendeskripsikan situasi, dan mencentang kebutuhan mendesak. Tidak dipungut biaya dan tidak perlu login. Identitas pelapor dilindungi privasi.
            </p>
            <div class="pt-1">
                <a href="{{ route('public.reports.create') }}" class="text-xs font-bold font-telemetry link-text">
                    Mulai kirim laporan
                </a>
            </div>
        </section>

        <!-- 3. Bagaimana Kode Tiket Digunakan untuk Melacak Laporan? -->
        <section id="lacak-tiket" class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4 sm:p-5 space-y-3">
            <div class="flex items-center gap-2.5 border-b border-slate-100 pb-2.5">
                <span class="w-2 h-2 rounded-full bg-[#315D91]" aria-hidden="true"></span>
                <h2 class="font-telemetry text-xs sm:text-sm font-bold uppercase tracking-wider text-[#202938]">
                    Bagaimana Kode Tiket Digunakan untuk Melacak Laporan?
                </h2>
            </div>
            <p class="text-xs text-slate-600 leading-relaxed">
                Nomor tiket (format <code class="font-bold text-[#173B70] bg-slate-100 px-1 py-0.5 rounded">RPT-SIM-XXXXXX</code> untuk laporan warga dan <code class="font-bold text-[#315D91] bg-slate-100 px-1 py-0.5 rounded">RSP-SIM-XXXXXX</code> untuk respons bantuan) dimasukkan pada kolom pencarian tiket untuk melihat catatan penanganan tanpa perlu autentikasi.
            </p>
            <div class="pt-1">
                <a href="{{ route('public.track', ['ticket' => 'RPT-SIM-8K4M9P2X']) }}" class="text-xs font-bold font-telemetry link-text">
                    Coba lacak: RPT-SIM-8K4M9P2X
                </a>
            </div>
        </section>

        <!-- 4. Apa Arti Status Verifikasi & Tindak Lanjut? -->
        <section id="status-alur" class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4 sm:p-5 space-y-3">
            <div class="flex items-center gap-2.5 border-b border-slate-100 pb-2.5">
                <span class="w-2 h-2 rounded-full bg-emerald-600" aria-hidden="true"></span>
                <h2 class="font-telemetry text-xs sm:text-sm font-bold uppercase tracking-wider text-[#202938]">
                    Apa Arti Status Verifikasi &amp; Tindak Lanjut?
                </h2>
            </div>
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="space-y-1">
                    <span class="font-telemetry font-bold text-[10px] uppercase text-[#173B70] block">1. Status Verifikasi:</span>
                    <p class="text-slate-600">
                        <strong>Menunggu:</strong> Sedang diperiksa.<br>
                        <strong>Terverifikasi:</strong> Sah &amp; terkonfirmasi.<br>
                        <strong>Ditolak:</strong> Tidak akurat/duplikat.
                    </p>
                </div>
                <div class="space-y-1">
                    <span class="font-telemetry font-bold text-[10px] uppercase text-emerald-800 block">2. Status Tindak Lanjut:</span>
                    <p class="text-slate-600">
                        <strong>Direncanakan:</strong> Alokasi awal.<br>
                        <strong>Penyaluran:</strong> Bergerak di lokasi.<br>
                        <strong>Dikonfirmasi:</strong> Bukti serah terima sah.
                    </p>
                </div>
            </div>
        </section>
    </div>

    <!-- 5. Apa Batasan Sistem TanggapTerang? & Kontak Darurat -->
    <section id="batasan-sistem" class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4 sm:p-5 space-y-3">
        <div class="flex items-center gap-2.5 border-b border-slate-100 pb-2.5">
            <span class="w-2 h-2 rounded-full bg-amber-500" aria-hidden="true"></span>
            <h2 class="font-telemetry text-xs sm:text-sm font-bold uppercase tracking-wider text-[#202938]">
                Apa Batasan Sistem TanggapTerang?
            </h2>
        </div>

        <div class="bg-amber-50/70 border border-amber-200 rounded-xl p-3.5 text-xs text-amber-950 space-y-2">
            <strong>TanggapTerang adalah sistem transparansi dan koordinasi informasi, bukan call center darurat seketika.</strong>
            <p>Untuk situasi yang mengancam keselamatan jiwa, segera hubungi nomor resmi darurat nasional:</p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1 font-telemetry text-xs">
                <div class="bg-white rounded p-2 border border-amber-200">
                    <span class="block text-amber-800 font-bold text-sm">112</span>
                    <span class="text-[10px] text-slate-500">Darurat Terpadu</span>
                </div>
                <div class="bg-white rounded p-2 border border-amber-200">
                    <span class="block text-amber-800 font-bold text-sm">115</span>
                    <span class="text-[10px] text-slate-500">Basarnas (SAR)</span>
                </div>
                <div class="bg-white rounded p-2 border border-amber-200">
                    <span class="block text-amber-800 font-bold text-sm">117</span>
                    <span class="text-[10px] text-slate-500">BNPB Pusdalops</span>
                </div>
                <div class="bg-white rounded p-2 border border-amber-200">
                    <span class="block text-amber-800 font-bold text-sm">119</span>
                    <span class="text-[10px] text-slate-500">Ambulans Medis</span>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
