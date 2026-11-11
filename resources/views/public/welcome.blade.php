@extends('layouts.app', [
    'title' => 'TanggapTerang — Papan Transparansi dan Koordinasi Informasi Kebencanaan',
    'activeTab' => '',
])

@section('content')
    <!-- 1. Hero Section -->
    <section class="bg-[#173B70] text-white border border-[#315D91] rounded-2xl p-5 sm:p-7 space-y-4 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-5">
            <img src="{{ asset('images/logo-tanggap-terang.png') }}"
                 alt="Logo TanggapTerang"
                 class="h-14 sm:h-16 w-auto object-contain rounded-xl self-start sm:self-center shrink-0">
            <div class="space-y-1.5">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded bg-amber-400 text-[#173B70] font-telemetry text-xs font-bold uppercase tracking-wider">
                        Prototype Kompetisi Multimedia In Action 2026
                    </span>
                    <span class="px-2.5 py-0.5 rounded bg-[#315D91] text-white border border-blue-300/30 font-telemetry text-xs font-bold uppercase tracking-wider">
                        Data Historis BNPB &amp; Simulasi
                    </span>
                </div>
                <p class="font-telemetry text-xs text-blue-200 uppercase tracking-wider">
                    Papan Transparansi dan Koordinasi Informasi Kebencanaan
                </p>
            </div>
        </div>

        <div class="max-w-3xl space-y-2">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold tracking-tight leading-tight">
                Terlihat Sebarannya, Terlacak Prosesnya, Terukur Responsnya
            </h1>
            <p class="text-xs sm:text-sm text-blue-100 leading-relaxed">
                Menghubungkan laporan masyarakat, pemetaan kebutuhan mendesak di lapangan, dan dokumentasi perkembangan respons bencana dalam satu platform terbuka.
            </p>
        </div>

        <div class="pt-2 flex flex-wrap items-center gap-3">
            <a href="{{ route('public.reports.create') }}"
               class="px-5 py-2.5 rounded-lg btn-brand-gradient font-telemetry text-xs font-bold uppercase tracking-wider min-h-[42px] inline-flex items-center gap-2 shadow-xs">
                <span>+ Kirim Laporan Warga</span>
            </a>
            <a href="{{ route('public.monitor') }}"
               class="px-5 py-2.5 rounded-lg bg-white hover:bg-slate-100 text-[#173B70] font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[42px] inline-flex items-center gap-2 shadow-xs">
                <span>Buka Peta Pantau</span>
                <span>&rarr;</span>
            </a>
            <a href="{{ route('public.track') }}"
               class="px-4 py-2.5 rounded-lg border border-blue-300/40 hover:bg-[#315D91] text-white font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[42px] inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span>Lacak Tiket</span>
            </a>
            <a href="{{ route('public.guide') }}"
               class="px-4 py-2.5 rounded-lg border border-blue-300/40 hover:bg-[#315D91] text-white font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[42px] inline-flex items-center">
                Panduan Penggunaan
            </a>
        </div>
    </section>

    <!-- 2. Ringkasan Cara Kerja (5 Tahap Respons Horizontal) -->
    <section class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 space-y-4" aria-label="Alur Respons TanggapTerang">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-[#173B70] font-telemetry">
                    Alur Kerja: 5 Tahapan Penanganan Laporan &amp; Respons
                </h2>
                <p class="text-xs text-[#788397] mt-0.5">
                    Proses transparan dari penerimaan laporan hingga konfirmasi penyaluran di lapangan.
                </p>
            </div>
            <a href="{{ route('public.track') }}" class="text-xs font-bold font-telemetry text-[#173B70] hover:underline self-start sm:self-auto">
                Lacak Status Tiket &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-5 gap-2.5">
            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="w-6 h-6 rounded-full bg-[#173B70] text-white text-[11px] font-bold flex items-center justify-center font-telemetry">1</span>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded font-telemetry">Dilaporkan</span>
                </div>
                <h3 class="text-xs font-bold text-[#202938] pt-1">Laporan Warga</h3>
                <p class="text-[11px] text-[#788397] leading-relaxed">Warga mengirimkan laporan dan menerima kode tiket unik tanpa perlu registrasi.</p>
            </div>

            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="w-6 h-6 rounded-full bg-[#173B70] text-white text-[11px] font-bold flex items-center justify-center font-telemetry">2</span>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded font-telemetry">Diperiksa</span>
                </div>
                <h3 class="text-xs font-bold text-[#202938] pt-1">Pemeriksaan Awal</h3>
                <p class="text-[11px] text-[#788397] leading-relaxed">Petugas meneliti kelengkapan koordinat lokasi, kronologi waktu, dan bukti pendukung.</p>
            </div>

            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="w-6 h-6 rounded-full bg-[#173B70] text-white text-[11px] font-bold flex items-center justify-center font-telemetry">3</span>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded font-telemetry">Diverifikasi</span>
                </div>
                <h3 class="text-xs font-bold text-[#202938] pt-1">Verifikasi Silang</h3>
                <p class="text-[11px] text-[#788397] leading-relaxed">Informasi dicocokkan dengan sumber rujukan resmi sebelum ditetapkan valid.</p>
            </div>

            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="w-6 h-6 rounded-full bg-[#173B70] text-white text-[11px] font-bold flex items-center justify-center font-telemetry">4</span>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-cyan-700 bg-cyan-50 px-1.5 py-0.5 rounded font-telemetry">Ditindaklanjuti</span>
                </div>
                <h3 class="text-xs font-bold text-[#202938] pt-1">Koordinasi Bantuan</h3>
                <p class="text-[11px] text-[#788397] leading-relaxed">Kebutuhan dipetakan ke 4 kategori logistik untuk penyaluran terkoordinasi.</p>
            </div>

            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="w-6 h-6 rounded-full bg-[#059669] text-white text-[11px] font-bold flex items-center justify-center font-telemetry">5</span>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-emerald-800 bg-emerald-100 px-1.5 py-0.5 rounded font-telemetry">Dikonfirmasi</span>
                </div>
                <h3 class="text-xs font-bold text-[#202938] pt-1">Penyerahan Sah</h3>
                <p class="text-[11px] text-[#788397] leading-relaxed">Bantuan dicatat selesai setelah disertai konfirmasi bukti dokumentasi penyerahan.</p>
            </div>
        </div>
    </section>

    <!-- 2b. Komponen Pemantauan Saat Ini (BMKG Nowcasting Alert Carousel) -->
    <x-regional-incidents-carousel :incidents="$regionalIncidents" />

    <!-- 3. Peta & Informasi Kejadian (Visual Element Utama) -->
    <section class="bg-white rounded-2xl shadow-xs border border-slate-100/90 overflow-hidden" aria-label="Preview Peta Kejadian">
        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100">
            <div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-[#173B70] font-telemetry flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#D94350]"></span>
                    Peta Sebaran Bencana &amp; Kebutuhan Aktif
                </h2>
                <p class="text-xs text-[#788397] mt-0.5">
                    {{ $summary['incidents_count'] }} kejadian terdata • {{ $summary['reports_count'] }} laporan warga • {{ $summary['historical_count'] }} data historis Pusdatin BNPB
                </p>
            </div>
            <a href="{{ route('public.monitor') }}"
               class="px-4 py-2 rounded-lg bg-[#173B70] hover:bg-[#315D91] text-white font-telemetry text-xs font-bold uppercase tracking-wider transition inline-flex items-center gap-1.5 self-start sm:self-auto shrink-0 shadow-xs">
                <span>Buka Peta Pantau Penuh</span>
                <span>&rarr;</span>
            </a>
        </div>

        <div id="welcome-preview-map" class="w-full h-[320px] sm:h-[380px] bg-slate-100"></div>

        <div class="p-4 bg-[#F8F9FC] border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 font-telemetry text-[11px]">
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-[#D94350]"></span>
                    <span class="text-[#202938]">Kejadian Kritis</span>
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    <span class="text-[#202938]">Kejadian Sedang</span>
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                    <span class="text-[#202938]">Laporan Kebutuhan</span>
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-600"></span>
                    <span class="text-[#202938]">Respons Bantuan</span>
                </span>
            </div>
            <span class="text-[#788397] text-[11px]">
                Klik sembarang titik di peta untuk melihat ringkasan status.
            </span>
        </div>
    </section>

    <!-- 4. Sumber Informasi (Tabel Ringkas Provider & Status) -->
    <section class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 space-y-4" aria-label="Sumber Data & Provider">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-[#173B70] font-telemetry">
                    Sumber Rujukan &amp; Integrasi Layanan Terbuka
                </h2>
                <p class="text-xs text-[#788397] mt-0.5">
                    TanggapTerang mengolah data dari berbagai layanan eksternal untuk mendukung pemantauan situasi secara transparan.
                </p>
            </div>
            <a href="{{ route('public.sources.index') }}" class="text-xs font-bold font-telemetry text-[#173B70] hover:underline self-start sm:self-auto">
                Detail Transparansi Data &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-[#788397] font-telemetry uppercase text-[10px] tracking-wider">
                        <th class="py-2.5 px-3">Provider</th>
                        <th class="py-2.5 px-3">Cakupan Informasi</th>
                        <th class="py-2.5 px-3">Kategori</th>
                        <th class="py-2.5 px-3 text-right">Status Integrasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[#202938]">
                    <tr>
                        <td class="py-2.5 px-3 font-bold">BMKG</td>
                        <td class="py-2.5 px-3 text-[#788397]">Informasi gempa bumi dan prakiraan cuaca wilayah</td>
                        <td class="py-2.5 px-3">Dalam Negeri</td>
                        <td class="py-2.5 px-3 text-right">
                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-telemetry text-[10px] font-bold">Terhubung</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2.5 px-3 font-bold">PetaBencana</td>
                        <td class="py-2.5 px-3 text-[#788397]">Laporan kejadian berbasis partisipasi masyarakat</td>
                        <td class="py-2.5 px-3">Dalam Negeri</td>
                        <td class="py-2.5 px-3 text-right">
                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-telemetry text-[10px] font-bold">Terhubung</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2.5 px-3 font-bold">Open-Meteo</td>
                        <td class="py-2.5 px-3 text-[#788397]">Prakiraan cuaca numerik dan indikator hidrologi banjir</td>
                        <td class="py-2.5 px-3">Internasional</td>
                        <td class="py-2.5 px-3 text-right">
                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-telemetry text-[10px] font-bold">Terhubung</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2.5 px-3 font-bold">GDACS</td>
                        <td class="py-2.5 px-3 text-[#788397]">Peringatan multi-bahaya bencana global</td>
                        <td class="py-2.5 px-3">Internasional</td>
                        <td class="py-2.5 px-3 text-right">
                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-telemetry text-[10px] font-bold">Terhubung</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2.5 px-3 font-bold">GDELT</td>
                        <td class="py-2.5 px-3 text-[#788397]">Referensi pemberitaan kebencanaan multi-bahasa</td>
                        <td class="py-2.5 px-3">Internasional</td>
                        <td class="py-2.5 px-3 text-right">
                            <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 font-telemetry text-[10px] font-bold">Akses Terbatas</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2.5 px-3 font-bold">Pusdatin BNPB (DIBI)</td>
                        <td class="py-2.5 px-3 text-[#788397]">Arsip resmi kejadian bencana historis 2022–2024</td>
                        <td class="py-2.5 px-3">Pemerintah</td>
                        <td class="py-2.5 px-3 text-right">
                            <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200 font-telemetry text-[10px] font-bold">Arsip Terintegrasi</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- 5. Panduan Singkat (3 Langkah Visual: Laporkan -> Lacak -> Pantau) -->
    <section class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 space-y-4" aria-label="Panduan Singkat Partisipasi">
        <div class="border-b border-slate-100 pb-3">
            <h2 class="text-sm font-bold uppercase tracking-wider text-[#173B70] font-telemetry">
                Tiga Langkah Mudah Partisipasi Publik
            </h2>
            <p class="text-xs text-[#788397] mt-0.5">
                Cara menggunakan TanggapTerang untuk saling melengkapi informasi respons bencana.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-4 space-y-2">
                <div class="w-7 h-7 rounded-lg bg-[#D94350] text-white font-telemetry font-bold text-xs flex items-center justify-center">
                    01
                </div>
                <h3 class="text-sm font-bold text-[#202938]">Laporkan Kejadian</h3>
                <p class="text-xs text-[#788397] leading-relaxed">
                    Sampaikan kejadian dan kebutuhan darurat di wilayah Anda tanpa perlu akun. Identitas Anda dirahasiakan dari publik.
                </p>
                <div class="pt-1">
                    <a href="{{ route('public.reports.create') }}" class="text-xs font-bold text-[#D94350] hover:underline font-telemetry">
                        Kirim Laporan Sekarang &rarr;
                    </a>
                </div>
            </div>

            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-4 space-y-2">
                <div class="w-7 h-7 rounded-lg bg-[#173B70] text-white font-telemetry font-bold text-xs flex items-center justify-center">
                    02
                </div>
                <h3 class="text-sm font-bold text-[#202938]">Lacak Nomor Tiket</h3>
                <p class="text-xs text-[#788397] leading-relaxed">
                    Gunakan kode tiket unik Anda untuk memantau status pemeriksaan verifikator dan kronologi tindak lanjut secara transparan.
                </p>
                <div class="pt-1">
                    <a href="{{ route('public.track') }}" class="text-xs font-bold text-[#173B70] hover:underline font-telemetry">
                        Buka Lacak Tiket &rarr;
                    </a>
                </div>
            </div>

            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-4 space-y-2">
                <div class="w-7 h-7 rounded-lg bg-[#059669] text-white font-telemetry font-bold text-xs flex items-center justify-center">
                    03
                </div>
                <h3 class="text-sm font-bold text-[#202938]">Pantau Peta Bencana</h3>
                <p class="text-xs text-[#788397] leading-relaxed">
                    Amati sebaran titik bencana, lokasi kebutuhan logistik, dan penyaluran bantuan masyarakat langsung pada peta interaktif.
                </p>
                <div class="pt-1">
                    <a href="{{ route('public.monitor') }}" class="text-xs font-bold text-[#059669] hover:underline font-telemetry">
                        Buka Peta Pantau &rarr;
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Script Initialization for Welcome Preview -->
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const mapEl = document.getElementById('welcome-preview-map');
                if (!mapEl || typeof window.L === 'undefined') return;

                const previewMap = window.L.map(mapEl, {
                    zoomControl: true,
                    attributionControl: false,
                    scrollWheelZoom: false,
                }).setView([-6.2, 106.8], 8);

                window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 18,
                }).addTo(previewMap);

                const incidents = @json($previewIncidents ?? []);
                const bounds = [];

                incidents.forEach(item => {
                    if (typeof item.latitude !== 'number' || typeof item.longitude !== 'number') return;
                    const isHistorical = !item.is_simulation;
                    const markerColor = isHistorical ? '#173B70' : '#D94350';

                    const marker = window.L.circleMarker([item.latitude, item.longitude], {
                        radius: 8,
                        fillColor: markerColor,
                        color: '#FFFFFF',
                        weight: 2,
                        fillOpacity: 0.9,
                    }).addTo(previewMap);

                    marker.bindPopup(`
                        <div style="font-family: 'Poppins', sans-serif; font-size: 12px; color: #202938;">
                            <div style="font-weight: 700; margin-bottom: 2px;">${item.title}</div>
                            <div style="color: #788397; font-size: 11px; margin-bottom: 6px;">${item.incident_type}</div>
                            <a href="/incidents/${item.slug}" style="display: block; text-align: center; background: #173B70; color: #FFF; padding: 4px 8px; border-radius: 4px; text-decoration: none; font-size: 10px; font-weight: 700; text-transform: uppercase;">
                                Lihat Detail &rarr;
                            </a>
                        </div>
                    `);
                    bounds.push([item.latitude, item.longitude]);
                });

                if (bounds.length > 0) {
                    previewMap.fitBounds(bounds, { padding: [30, 30], maxZoom: 10 });
                }
            });
        </script>
    @endpush
@endsection
