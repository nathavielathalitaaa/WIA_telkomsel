@extends('layouts.app', [
    'title' => 'Panduan Penggunaan — Papan Transparansi TanggapTerang',
    'activeTab' => 'panduan',
])

@section('content')
    <!-- Master Institutional Hero Header Card -->
    <section class="bg-[#173B70] text-white border border-[#315D91] rounded-2xl p-5 sm:p-7 space-y-4 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div class="space-y-2 max-w-3xl">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-1 rounded bg-[#315D91] text-white border border-blue-300/30 font-telemetry text-xs font-bold uppercase tracking-wider">
                        Panduan Penggunaan
                    </span>
                    <span class="px-2.5 py-1 rounded bg-amber-400 text-[#173B70] font-telemetry text-xs font-bold uppercase tracking-wider">
                        Orientasi Publik
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold tracking-tight text-white leading-tight">
                    Panduan Penggunaan TanggapTerang
                </h1>
                <p class="text-xs sm:text-sm text-blue-100 leading-relaxed">
                    Panduan praktis bagi masyarakat dan relawan untuk memahami alur pelaporan kejadian, cara melacak tiket, membaca peta bencana, serta memahami status transparansi respons.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0 pt-1 lg:pt-0">
                <a href="{{ route('public.reports.create') }}"
                   class="px-4 py-2 rounded-lg btn-brand-gradient text-white font-telemetry text-xs font-bold uppercase tracking-wider min-h-[40px] inline-flex items-center gap-1.5 shadow-xs">
                    <span class="font-extrabold text-sm leading-none">+</span>
                    <span>Lapor Kejadian</span>
                </a>
                <a href="{{ route('public.track') }}"
                   class="px-4 py-2 rounded-lg border border-blue-300/40 hover:bg-[#315D91] text-white font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[40px] inline-flex items-center gap-1.5 shadow-xs">
                    <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Lacak Tiket</span>
                </a>
            </div>
        </div>

        <!-- Quick Jump Links -->
        <div class="pt-3 border-t border-blue-400/20 flex flex-wrap items-center gap-2 text-xs font-telemetry text-blue-100">
            <span class="text-white font-bold uppercase text-[10px] tracking-wider shrink-0">Lompat Ke:</span>
            <a href="#apa-itu" class="px-2.5 py-1 rounded bg-white/10 hover:bg-white/20 transition text-[11px] text-white font-medium">1. Apa Itu</a>
            <a href="#cara-lapor" class="px-2.5 py-1 rounded bg-white/10 hover:bg-white/20 transition text-[11px] text-white font-medium">2. Cara Melapor</a>
            <a href="#lacak-tiket" class="px-2.5 py-1 rounded bg-white/10 hover:bg-white/20 transition text-[11px] text-white font-medium">3. Lacak Tiket</a>
            <a href="#baca-peta" class="px-2.5 py-1 rounded bg-white/10 hover:bg-white/20 transition text-[11px] text-white font-medium">4. Membaca Peta</a>
            <a href="#status-alur" class="px-2.5 py-1 rounded bg-white/10 hover:bg-white/20 transition text-[11px] text-white font-medium">5. Arti Status</a>
            <a href="#sumber-data" class="px-2.5 py-1 rounded bg-white/10 hover:bg-white/20 transition text-[11px] text-white font-medium">6. Sumber Data</a>
            <a href="#batasan-sistem" class="px-2.5 py-1 rounded bg-white/10 hover:bg-white/20 transition text-[11px] text-white font-medium">7. Batasan &amp; Darurat</a>
        </div>
    </section>

    <!-- 1. Apa itu TanggapTerang -->
    <section id="apa-itu" class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 sm:p-6 space-y-4 scroll-mt-24">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg bg-[#173B70] text-white font-telemetry font-bold text-xs flex items-center justify-center shrink-0">
                01
            </span>
            <div>
                <h2 class="text-base sm:text-lg font-bold text-[#202938]">
                    Apa itu TanggapTerang?
                </h2>
                <p class="font-telemetry text-xs text-[#788397] uppercase tracking-wider">
                    Konsep &amp; Nilai Utama Produk
                </p>
            </div>
        </div>

        <div class="space-y-3 text-xs sm:text-sm text-[#202938]/85 leading-relaxed">
            <p>
                <strong>TanggapTerang</strong> adalah <strong>Papan Transparansi dan Koordinasi Respons Bencana</strong> dengan prinsip <em>&ldquo;Terlihat, Terlacak, dan Terukur.&rdquo;</em> Platform ini menghubungkan laporan kejadian dari warga, kebutuhan mendesak di lapangan, dan catatan perkembangan respons tim secara terbuka.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-1">
            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-4 space-y-1.5">
                <span class="font-telemetry text-xs font-bold uppercase text-[#173B70] block">
                    Terlihat
                </span>
                <p class="text-xs text-[#788397] leading-relaxed">
                    Peta sebaran geospasial menampilkan lokasi kejadian aktif, titik kebutuhan warga, dan cakupan respons secara langsung.
                </p>
            </div>
            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-4 space-y-1.5">
                <span class="font-telemetry text-xs font-bold uppercase text-[#315D91] block">
                    Terlacak
                </span>
                <p class="text-xs text-[#788397] leading-relaxed">
                    Setiap laporan dan bantuan memperoleh nomor tiket unik. Riwayat pemeriksaan dan kronologi perkembangan dapat dipantau tanpa harus login.
                </p>
            </div>
            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-4 space-y-1.5">
                <span class="font-telemetry text-xs font-bold uppercase text-[#059669] block">
                    Terukur
                </span>
                <p class="text-xs text-[#788397] leading-relaxed">
                    Kebutuhan dikelompokkan ke 4 kategori logistik (Kesehatan, Makanan &amp; Minuman, Bansos, Evakuasi) agar seimbang dan terpantau.
                </p>
            </div>
        </div>
    </section>

    <!-- 2. Bagaimana Cara Melaporkan Kejadian -->
    <section id="cara-lapor" class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 sm:p-6 space-y-4 scroll-mt-24">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg bg-[#D94350] text-white font-telemetry font-bold text-xs flex items-center justify-center shrink-0">
                02
            </span>
            <div>
                <h2 class="text-base sm:text-lg font-bold text-[#202938]">
                    Bagaimana Masyarakat Melaporkan Kejadian?
                </h2>
                <p class="font-telemetry text-xs text-[#788397] uppercase tracking-wider">
                    Pelaporan Mudah Tanpa Perlu Mendaftar Akun
                </p>
            </div>
        </div>

        <p class="text-xs sm:text-sm text-[#202938]/85 leading-relaxed">
            TanggapTerang dirancang agar masyarakat dalam situasi mendesak dapat mengirimkan laporan tanpa hambatan registrasi atau login. Identitas pelapor dirahasiakan dari publik.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-4 space-y-2">
                <div class="w-6 h-6 rounded-full bg-[#173B70] text-white font-telemetry text-xs font-bold flex items-center justify-center">
                    1
                </div>
                <h3 class="text-xs font-bold text-[#202938]">Buka Formulir Lapor</h3>
                <p class="text-xs text-[#788397] leading-relaxed">
                    Klik tombol <strong>+ Lapor Kejadian</strong> di navigasi atas atau tombol melayang di bagian bawah layar ponsel Anda.
                </p>
            </div>

            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-4 space-y-2">
                <div class="w-6 h-6 rounded-full bg-[#173B70] text-white font-telemetry text-xs font-bold flex items-center justify-center">
                    2
                </div>
                <h3 class="text-xs font-bold text-[#202938]">Pilih Wilayah &amp; Kebutuhan</h3>
                <p class="text-xs text-[#788397] leading-relaxed">
                    Pilih hierarki wilayah (Provinsi, Kab/Kota, Kecamatan), deskripsikan kondisi nyata di lokasi, dan centang kategori kebutuhan yang mendesak.
                </p>
            </div>

            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-4 space-y-2">
                <div class="w-6 h-6 rounded-full bg-[#173B70] text-white font-telemetry text-xs font-bold flex items-center justify-center">
                    3
                </div>
                <h3 class="text-xs font-bold text-[#202938]">Simpan Nomor Tiket</h3>
                <p class="text-xs text-[#788397] leading-relaxed">
                    Setelah terkirim, Anda langsung mendapatkan kode tiket acak (contoh: <code class="font-bold text-[#173B70]">RPT-SIM-8K4M9P2X</code>). Simpan kode ini untuk memantau proses verifikasi.
                </p>
            </div>
        </div>

        <div class="pt-1">
            <a href="{{ route('public.reports.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg btn-brand-gradient text-white font-telemetry text-xs font-bold uppercase tracking-wider shadow-xs">
                <span>+ Mulai Buat Laporan Warga</span>
                <span>&rarr;</span>
            </a>
        </div>
    </section>

    <!-- 3. Bagaimana Menggunakan Kode Tiket -->
    <section id="lacak-tiket" class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 sm:p-6 space-y-4 scroll-mt-24">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg bg-[#315D91] text-white font-telemetry font-bold text-xs flex items-center justify-center shrink-0">
                03
            </span>
            <div>
                <h2 class="text-base sm:text-lg font-bold text-[#202938]">
                    Bagaimana Kode Tiket Digunakan untuk Melacak Laporan?
                </h2>
                <p class="font-telemetry text-xs text-[#788397] uppercase tracking-wider">
                    Transparansi Perkembangan Tanpa Login
                </p>
            </div>
        </div>

        <div class="space-y-3 text-xs sm:text-sm text-[#202938]/85 leading-relaxed">
            <p>
                Kode tiket berfungsi sebagai kunci penelusuran mandiri bagi pelapor dan relawan. Cukup masukkan nomor tiket di menu <strong>Lacak Tiket</strong> untuk melihat perkembangan terkini.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-4 space-y-2">
                <span class="font-telemetry text-[11px] font-bold uppercase text-[#173B70] block">
                    Format Tiket Laporan Warga (RPT)
                </span>
                <p class="text-xs text-[#788397] leading-relaxed">
                    Diawali dengan kode <code class="font-bold text-[#173B70] bg-slate-200/60 px-1 py-0.5 rounded">RPT-</code>. Menampilkan hasil pemeriksaan fakta oleh Tim Verifikator, kejadian bencana yang ditautkan, dan catatan penanganan.
                </p>
                <div class="pt-1">
                    <a href="{{ route('public.track', ['ticket' => 'RPT-SIM-8K4M9P2X']) }}"
                       class="text-xs font-bold text-[#173B70] hover:underline inline-flex items-center gap-1 font-telemetry">
                        <span>Coba Lacak: RPT-SIM-8K4M9P2X (Terverifikasi)</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-4 space-y-2">
                <span class="font-telemetry text-[11px] font-bold uppercase text-[#315D91] block">
                    Format Kode Respons Bantuan (RSP)
                </span>
                <p class="text-xs text-[#788397] leading-relaxed">
                    Diawali dengan kode <code class="font-bold text-[#315D91] bg-slate-200/60 px-1 py-0.5 rounded">RSP-</code>. Menampilkan progres logistik bantuan: tahap Direncanakan, Dalam Penyaluran, hingga Penyerahan Dikonfirmasi.
                </p>
                <div class="pt-1">
                    <a href="{{ route('public.track', ['ticket' => 'RSP-SIM-7M2K8N4Q']) }}"
                       class="text-xs font-bold text-[#315D91] hover:underline inline-flex items-center gap-1 font-telemetry">
                        <span>Coba Lacak: RSP-SIM-7M2K8N4Q (Terkonfirmasi)</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Bagaimana Membaca Informasi pada Peta -->
    <section id="baca-peta" class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 sm:p-6 space-y-4 scroll-mt-24">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg bg-[#059669] text-white font-telemetry font-bold text-xs flex items-center justify-center shrink-0">
                04
            </span>
            <div>
                <h2 class="text-base sm:text-lg font-bold text-[#202938]">
                    Bagaimana Informasi Kejadian Ditampilkan pada Peta?
                </h2>
                <p class="font-telemetry text-xs text-[#788397] uppercase tracking-wider">
                    Memahami Simbol, Warna, &amp; Lapisan Filter
                </p>
            </div>
        </div>

        <p class="text-xs sm:text-sm text-[#202938]/85 leading-relaxed">
            Halaman <strong>Pantau</strong> menyajikan peta interaktif berbasis OpenStreetMap dan Leaflet yang mengelompokkan data ke dalam tiga lapisan informasi:
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3.5 space-y-1.5">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#D94350] shrink-0"></span>
                    <span class="font-telemetry text-xs font-bold text-[#202938]">Kejadian Kritis</span>
                </div>
                <p class="text-[11px] text-[#788397] leading-relaxed">
                    Bencana dengan dampak besar (korban luka/jiwa atau kerusakan parah) yang butuh atensi penanganan utama.
                </p>
            </div>

            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3.5 space-y-1.5">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-amber-500 shrink-0"></span>
                    <span class="font-telemetry text-xs font-bold text-[#202938]">Kejadian Sedang / Rendah</span>
                </div>
                <p class="text-[11px] text-[#788397] leading-relaxed">
                    Kejadian bencana skala lokal dengan dampak terbatas atau dalam tahap pemulihan.
                </p>
            </div>

            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3.5 space-y-1.5">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500 shrink-0"></span>
                    <span class="font-telemetry text-xs font-bold text-[#202938]">Titik Kebutuhan Warga</span>
                </div>
                <p class="text-[11px] text-[#788397] leading-relaxed">
                    Lokasi warga yang melaporkan kekurangan bahan logistik, medis, atau bantuan evakuasi.
                </p>
            </div>

            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3.5 space-y-1.5">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#059669] shrink-0"></span>
                    <span class="font-telemetry text-xs font-bold text-[#202938]">Respons Bantuan</span>
                </div>
                <p class="text-[11px] text-[#788397] leading-relaxed">
                    Titik penyaluran atau penyerahan bantuan oleh relawan/organisasi yang terdokumentasi.
                </p>
            </div>
        </div>

        <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-4 text-xs text-[#202938]/85 space-y-1">
            <strong>Tips Interaksi:</strong>
            <p class="text-[#788397]">
                Klik marker mana pun di peta untuk membuka popup ringkasan. Anda dapat menggunakan tombol <em>Filter Layer</em> (Semua, Kejadian, Kebutuhan, Bantuan) untuk menyaring data yang ditampilkan.
            </p>
        </div>
    </section>

    <!-- 5. Apa Arti Status Verifikasi & Tindak Lanjut -->
    <section id="status-alur" class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 sm:p-6 space-y-4 scroll-mt-24">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg bg-[#D97706] text-white font-telemetry font-bold text-xs flex items-center justify-center shrink-0">
                05
            </span>
            <div>
                <h2 class="text-base sm:text-lg font-bold text-[#202938]">
                    Apa Arti Status Verifikasi &amp; Tindak Lanjut?
                </h2>
                <p class="font-telemetry text-xs text-[#788397] uppercase tracking-wider">
                    Pemisahan Tegas Antara Keabsahan Fakta &amp; Penanganan Operasional
                </p>
            </div>
        </div>

        <p class="text-xs sm:text-sm text-[#202938]/85 leading-relaxed">
            TanggapTerang secara tegas memisahkan dua dimensi status agar tidak terjadi kerancuan antara penilaian kebenaran informasi dan progres penanganan di lapangan:
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Dimensi 1: Status Verifikasi Informasi -->
            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-4 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-200/80 pb-2">
                    <span class="font-telemetry text-xs font-bold uppercase text-[#173B70]">
                        1. Status Verifikasi (Keabsahan Fakta)
                    </span>
                    <span class="text-[10px] text-[#788397] font-telemetry">Tim Verifikator</span>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="flex items-start gap-2">
                        <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 border border-amber-300 font-telemetry text-[10px] font-bold shrink-0">
                            Menunggu Verifikasi
                        </span>
                        <p class="text-[#788397]">Laporan baru masuk, sedang diperiksa kesesuaian lokasi dan bukti pendukungnya.</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 border border-emerald-300 font-telemetry text-[10px] font-bold shrink-0">
                            Terverifikasi
                        </span>
                        <p class="text-[#788397]">Informasi telah dicocokkan dengan rujukan terbuka dan dinyatakan sah.</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 border border-rose-300 font-telemetry text-[10px] font-bold shrink-0">
                            Ditolak
                        </span>
                        <p class="text-[#788397]">Laporan tidak akurat, duplikat, atau tidak memenuhi kriteria verifikasi.</p>
                    </div>
                </div>
            </div>

            <!-- Dimensi 2: Status Tindak Lanjut Tim -->
            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-4 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-200/80 pb-2">
                    <span class="font-telemetry text-xs font-bold uppercase text-[#315D91]">
                        2. Status Tindak Lanjut (Operasional Tim)
                    </span>
                    <span class="text-[10px] text-[#788397] font-telemetry">Tim Operator</span>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="flex items-start gap-2">
                        <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-800 font-telemetry text-[10px] font-bold shrink-0">
                            Belum Ditindaklanjuti
                        </span>
                        <p class="text-[#788397]">Informasi terdaftar, menunggu alokasi langkah pendataan tim.</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="px-2 py-0.5 rounded bg-cyan-100 text-cyan-800 border border-cyan-300 font-telemetry text-[10px] font-bold shrink-0">
                            Sedang Ditindaklanjuti
                        </span>
                        <p class="text-[#788397]">Petugas sedang mengoordinasikan pendataan kebutuhan atau respons bantuan.</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 border border-indigo-300 font-telemetry text-[10px] font-bold shrink-0">
                            Dipantau
                        </span>
                        <p class="text-[#788397]">Kondisi tetap dalam pengawasan berkala tim pemantau.</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 border border-emerald-300 font-telemetry text-[10px] font-bold shrink-0">
                            Selesai
                        </span>
                        <p class="text-[#788397]">Dokumentasi dan pencatatan respons tahap ini telah rampung dicatat.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. Bagaimana Membaca Informasi Sumber Data -->
    <section id="sumber-data" class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 sm:p-6 space-y-4 scroll-mt-24">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg bg-[#4F46E5] text-white font-telemetry font-bold text-xs flex items-center justify-center shrink-0">
                06
            </span>
            <div>
                <h2 class="text-base sm:text-lg font-bold text-[#202938]">
                    Bagaimana Masyarakat Membaca Informasi Sumber Data?
                </h2>
                <p class="font-telemetry text-xs text-[#788397] uppercase tracking-wider">
                    Transparansi 4 Asal Data &amp; Status API
                </p>
            </div>
        </div>

        <p class="text-xs sm:text-sm text-[#202938]/85 leading-relaxed">
            TanggapTerang secara terbuka mencantumkan asal data pada setiap informasi kejadian dan rekaman. Seluruh detail provider dapat diperiksa di halaman <strong>Tentang Data</strong>:
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3.5 space-y-1.5">
                <span class="font-telemetry text-xs font-bold uppercase text-[#173B70] block">
                    1. Data Pemerintah &amp; Wilayah
                </span>
                <p class="text-xs text-[#788397] leading-relaxed">
                    Data kode dan hierarki wilayah administratif digunakan sebagai referensi lokasi. Informasi instansi pemerintah yang belum terhubung langsung dengan sumber resmi pada demonstrasi diberi label <strong>Sumber Pemerintah (Simulasi)</strong>.
                </p>
            </div>

            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3.5 space-y-1.5">
                <span class="font-telemetry text-xs font-bold uppercase text-[#315D91] block">
                    2. Integrasi API &amp; Sumber Terbuka
                </span>
                <p class="text-xs text-[#788397] leading-relaxed">
                    TanggapTerang mengolah informasi dari berbagai layanan eksternal (BMKG, PetaBencana, Open-Meteo, GDACS, GDELT) untuk mendukung pemantauan kejadian dan kondisi wilayah.
                </p>
            </div>

            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3.5 space-y-1.5">
                <span class="font-telemetry text-xs font-bold uppercase text-emerald-800 block">
                    3. Laporan Warga
                </span>
                <p class="text-xs text-[#788397] leading-relaxed">
                    Masyarakat dapat melaporkan kejadian dan kebutuhan tanpa akun menggunakan kode tiket unik. Setiap laporan menjadi informasi awal yang perlu dikelola dan diverifikasi.
                </p>
            </div>

            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3.5 space-y-1.5">
                <span class="font-telemetry text-xs font-bold uppercase text-amber-900 block">
                    4. Respons Bantuan Masyarakat
                </span>
                <p class="text-xs text-[#788397] leading-relaxed">
                    Komunitas dan relawan dapat mencatat partisipasi serta perkembangan bantuan melalui tahapan <em>Direncanakan</em>, <em>Dalam Penyaluran</em>, dan <em>Penyerahan Dikonfirmasi</em>.
                </p>
            </div>
        </div>

        <div class="pt-1">
            <a href="{{ route('public.sources.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-bold font-telemetry uppercase tracking-wider text-[#173B70] hover:underline">
                <span>Buka Rincian Status 8 Provider Sumber Data</span>
                <span>&rarr;</span>
            </a>
        </div>
    </section>

    <!-- 7. Batasan Sistem & Kontak Darurat Resmi -->
    <section id="batasan-sistem" class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 sm:p-6 space-y-4 scroll-mt-24">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg bg-red-600 text-white font-telemetry font-bold text-xs flex items-center justify-center shrink-0">
                07
            </span>
            <div>
                <h2 class="text-base sm:text-lg font-bold text-[#202938]">
                    Apa Batasan Sistem TanggapTerang?
                </h2>
                <p class="font-telemetry text-xs text-[#788397] uppercase tracking-wider">
                    Bukan Call Center Darurat • Nomor Kontak Resmi
                </p>
            </div>
        </div>

        <div class="bg-amber-50 border border-amber-200/80 rounded-xl p-4 text-xs sm:text-sm text-amber-950 space-y-2">
            <strong class="block text-amber-900 font-bold">PENTING: TanggapTerang Bukan Call Center Gawat Darurat</strong>
            <p class="leading-relaxed text-amber-900/90">
                Platform ini berfungsi untuk transparansi dokumentasi dan koordinasi informasi. Untuk keadaan gawat darurat yang membutuhkan pertolongan seketika (evakuasi medis darurat, bencana yang mengancam nyawa, kebakaran), segera hubungi nomor resmi darurat nasional:
            </p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1 font-telemetry text-xs">
                <div class="bg-white rounded-lg p-2.5 border border-amber-200">
                    <span class="block text-amber-800 font-bold text-base leading-none">112</span>
                    <span class="text-[10px] text-slate-600">Darurat Terpadu</span>
                </div>
                <div class="bg-white rounded-lg p-2.5 border border-amber-200">
                    <span class="block text-amber-800 font-bold text-base leading-none">115</span>
                    <span class="text-[10px] text-slate-600">Basarnas (SAR)</span>
                </div>
                <div class="bg-white rounded-lg p-2.5 border border-amber-200">
                    <span class="block text-amber-800 font-bold text-base leading-none">117</span>
                    <span class="text-[10px] text-slate-600">BNPB Pusdalops</span>
                </div>
                <div class="bg-white rounded-lg p-2.5 border border-amber-200">
                    <span class="block text-amber-800 font-bold text-base leading-none">119</span>
                    <span class="text-[10px] text-slate-600">Ambulans Medis</span>
                </div>
            </div>
        </div>

        <div class="space-y-2 text-xs sm:text-sm text-[#202938]/85 leading-relaxed">
            <p>
                <strong>Ketiadaan Catatan Bukan Berarti Ketiadaan Tindakan:</strong> Ketiadaan dokumentasi tindak lanjut pada TanggapTerang tidak berarti instansi pemerintah atau organisasi terkait tidak bergerak di lapangan. Sistem hanya menampilkan apa yang telah diverifikasi dan dicatat pada platform.
            </p>
            <p>
                <strong>Karakteristik Data Demonstrasi:</strong> Pada mode demonstrasi prototype, skenario kejadian simulasi diberi label secara transparan untuk membedakannya dari data historis resmi Pusdatin BNPB.
            </p>
        </div>
    </section>

    <!-- Bottom Action Card -->
    <section class="bg-[#173B70] text-white rounded-2xl p-5 sm:p-6 text-center space-y-3 shadow-xs">
        <h3 class="text-base sm:text-lg font-bold">
            Siap Berkontribusi atau Memantau Perkembangan?
        </h3>
        <p class="text-xs sm:text-sm text-blue-100 max-w-xl mx-auto">
            Gunakan platform TanggapTerang untuk saling melengkapi informasi demi respons bencana yang transparan, terkoordinasi, dan berkeadilan.
        </p>
        <div class="pt-2 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('public.monitor') }}"
               class="px-5 py-2.5 rounded-lg bg-white hover:bg-slate-100 text-[#173B70] font-telemetry text-xs font-bold uppercase tracking-wider transition shadow-xs">
                Buka Peta Pantau
            </a>
            <a href="{{ route('public.reports.create') }}"
               class="px-5 py-2.5 rounded-lg btn-brand-gradient text-white font-telemetry text-xs font-bold uppercase tracking-wider shadow-xs">
                + Kirim Laporan Warga
            </a>
            <a href="{{ route('public.track') }}"
               class="px-4 py-2.5 rounded-lg border border-blue-300/40 hover:bg-[#315D91] text-white font-telemetry text-xs font-bold uppercase tracking-wider transition">
                Lacak Nomor Tiket
            </a>
        </div>
    </section>
@endsection
