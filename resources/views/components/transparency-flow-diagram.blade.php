@props(['compact' => false])

<section {{ $attributes->merge(['class' => 'bg-white border border-slate-300 rounded-[8px] shadow-xs overflow-hidden']) }} aria-label="Diagram Alur Transparansi Informasi TanggapTerang">
    <!-- Header Command Bar -->
    <div class="bg-slate-900 text-white px-4 py-3.5 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div class="flex items-center gap-2.5">
            <span class="inline-block w-2.5 h-2.5 rounded-full bg-blue-400"></span>
            <h2 class="text-xs sm:text-sm font-extrabold uppercase tracking-wider font-telemetry">
                Alur Transparansi Informasi &amp; Klasifikasi Sumber Data
            </h2>
        </div>
        <div class="flex items-center gap-3">
            <span class="hidden md:inline-block text-[11px] text-slate-300 font-telemetry">
                Transparansi Laporan Warga • Referensi Terbuka • Layanan API Eksternal
            </span>
            <a href="{{ route('public.sources.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-blue-600 hover:bg-blue-500 text-white font-telemetry text-xs font-bold uppercase tracking-wider transition shadow-2xs">
                <span>Transparansi Sumber Data</span>
                <span>&rarr;</span>
            </a>
        </div>
    </div>

    <!-- Deskripsi Pengantar Alur -->
    <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 text-xs text-slate-600 leading-relaxed">
        <p>
            TanggapTerang mengelola informasi dari laporan masyarakat, referensi terbuka, dan layanan API eksternal. Setiap informasi ditampilkan berdasarkan asal sumber, waktu pengambilan, serta status pengelolaannya agar penilai dan masyarakat dapat menelusuri dasar informasi yang disajikan.
        </p>
    </div>

    <div class="p-4 {{ $compact ? 'sm:p-4' : 'sm:p-5' }} space-y-5">
        <!-- 4 Klasifikasi Sumber Data -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- 1. Data Pemerintah dan Referensi Wilayah -->
            <div class="bg-[#EFF6FF] border border-[#93C5FD] rounded-[6px] p-3 flex flex-col justify-between">
                <div>
                    <div class="font-telemetry text-[11px] font-bold uppercase text-[#1E40AF]">
                        1. Data Pemerintah dan Referensi Wilayah
                    </div>
                    <p class="text-[11px] text-slate-700 mt-1.5 leading-relaxed">
                        Data kode dan hierarki wilayah administratif digunakan sebagai referensi lokasi. Informasi instansi pemerintah pada skenario demonstrasi yang belum terhubung langsung dengan sumber resmi diberi label <strong>Sumber Pemerintah (Simulasi)</strong>.
                    </p>
                </div>
            </div>

            <!-- 2. Integrasi API dan Sumber Informasi Terbuka -->
            <div class="bg-indigo-50/70 border border-indigo-200 rounded-[6px] p-3 flex flex-col justify-between">
                <div>
                    <div class="font-telemetry text-[11px] font-bold uppercase text-indigo-900">
                        2. Integrasi API dan Sumber Informasi Terbuka
                    </div>
                    <p class="text-[11px] text-slate-700 mt-1.5 leading-relaxed">
                        Sistem mengambil informasi dari layanan eksternal melalui API, antara lain:
                    </p>
                    <ul class="text-[10px] text-slate-700 mt-1.5 space-y-0.5 list-disc list-inside">
                        <li><strong>BMKG:</strong> informasi gempa bumi dan prakiraan cuaca.</li>
                        <li><strong>PetaBencana:</strong> laporan kejadian berbasis partisipasi masyarakat.</li>
                        <li><strong>Open-Meteo:</strong> prakiraan cuaca dan indikator hidrologi.</li>
                        <li><strong>GDACS:</strong> informasi peringatan multi-bahaya.</li>
                        <li><strong>GDELT:</strong> referensi pemberitaan global, bergantung pada ketersediaan layanan.</li>
                    </ul>
                    <p class="text-[10px] text-slate-600 mt-2 leading-relaxed">
                        Setiap sumber dilengkapi informasi status pengambilan, waktu pembaruan terakhir, jumlah data yang diterima, serta catatan keterbatasan sumber. Data yang berhasil diperoleh dapat ditelusuri melalui detail rekaman dan informasi sumbernya.
                    </p>
                    <div class="mt-3 pt-2.5 border-t border-indigo-200/80 flex items-center justify-between text-[11px] font-telemetry">
                        <span class="text-indigo-800 font-bold">Status 8 Provider</span>
                        <a href="{{ route('public.sources.index') }}" class="font-bold text-blue-700 hover:text-blue-900 inline-flex items-center gap-1 underline underline-offset-2">
                            <span>Buka Transparansi Sumber Data</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. Laporan Warga -->
            <div class="bg-emerald-50 border border-emerald-200 rounded-[6px] p-3 flex flex-col justify-between">
                <div>
                    <div class="font-telemetry text-[11px] font-bold uppercase text-emerald-900">
                        3. Laporan Warga
                    </div>
                    <p class="text-[11px] text-slate-700 mt-1.5 leading-relaxed">
                        Masyarakat dapat mengirimkan laporan kejadian dan kebutuhan tanpa akun menggunakan kode tiket unik non-sekuensial. Laporan menjadi informasi awal yang perlu melalui proses pengelolaan dan verifikasi.
                    </p>
                </div>
            </div>

            <!-- 4. Respons Bantuan Masyarakat -->
            <div class="bg-amber-50 border border-amber-300 rounded-[6px] p-3 flex flex-col justify-between">
                <div>
                    <div class="font-telemetry text-[11px] font-bold uppercase text-amber-950">
                        4. Respons Bantuan Masyarakat
                    </div>
                    <p class="text-[11px] text-slate-700 mt-1.5 leading-relaxed">
                        Komunitas, relawan, dan masyarakat dapat mencatat partisipasi serta perkembangan respons bantuan melalui tahapan <em>Direncanakan</em>, <em>Dalam Penyaluran</em>, dan <em>Penyerahan Dikonfirmasi</em>.
                    </p>
                </div>
            </div>
        </div>

        <!-- 6 Tahap Alur Transparansi -->
        <div>
            <div class="text-[11px] font-bold font-telemetry uppercase text-slate-500 tracking-wider mb-2.5">
                Alur Tahapan Pengelolaan &amp; Publikasi Informasi (Tahap 01 — 06)
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                <!-- Tahap 1 -->
                <div class="bg-slate-50 border border-slate-200 rounded-[6px] p-3 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-1.5 mb-1.5">
                            <span class="px-1.5 py-0.5 rounded-[4px] bg-slate-900 text-white text-[10px] font-bold font-telemetry">TAHAP 01</span>
                            <span class="text-[10px] font-bold uppercase text-slate-600 font-telemetry">Input</span>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 leading-snug">
                            Laporan Warga &amp; Pengambilan Data Eksternal
                        </h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Warga mengirimkan laporan kejadian dan sistem mengambil informasi dari sumber eksternal yang tersedia melalui API.
                        </p>
                    </div>
                    <div class="mt-2.5 pt-2 border-t border-slate-200 flex items-center justify-between text-[10px] font-telemetry text-slate-700">
                        <span class="font-medium truncate">Tiket Non-Sekuensial</span>
                        <span class="font-bold text-slate-900 shrink-0">&rarr; Rekaman Sumber Data</span>
                    </div>
                </div>

                <!-- Tahap 2 -->
                <div class="bg-slate-50 border border-slate-200 rounded-[6px] p-3 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-1.5 mb-1.5">
                            <span class="px-1.5 py-0.5 rounded-[4px] bg-slate-900 text-white text-[10px] font-bold font-telemetry">TAHAP 02</span>
                            <span class="text-[10px] font-bold uppercase text-slate-600 font-telemetry">Kurasi</span>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 leading-snug">
                            Pengelolaan dan Pengelompokan Data
                        </h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Informasi dikelompokkan berdasarkan lokasi, waktu, kategori, dan keterkaitan dengan kejadian. Data dari sumber berbeda tetap memiliki identitas dan asal sumber masing-masing.
                        </p>
                    </div>
                    <div class="mt-2.5 pt-2 border-t border-slate-200 flex items-center justify-between text-[10px] font-telemetry text-slate-700">
                        <span class="font-medium">Klaster Kejadian</span>
                        <span class="font-bold text-slate-900">&rarr;</span>
                    </div>
                </div>

                <!-- Tahap 3 -->
                <div class="bg-slate-50 border border-slate-200 rounded-[6px] p-3 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-1.5 mb-1.5">
                            <span class="px-1.5 py-0.5 rounded-[4px] bg-blue-700 text-white text-[10px] font-bold font-telemetry">TAHAP 03</span>
                            <span class="text-[10px] font-bold uppercase text-blue-800 font-telemetry">Verifikasi</span>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 leading-snug">
                            Pemeriksaan Informasi dan Pencatatan Kebutuhan
                        </h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Tim verifikator memeriksa silang laporan dan referensi yang tersedia. Data API tidak otomatis dianggap sebagai laporan kejadian terverifikasi.
                        </p>
                    </div>
                    <div class="mt-2.5 pt-2 border-t border-slate-200 flex items-center justify-between text-[10px] font-telemetry text-blue-800">
                        <span class="font-medium">Audit Trail</span>
                        <span class="font-bold">&rarr;</span>
                    </div>
                </div>

                <!-- Tahap 4 -->
                <div class="bg-slate-50 border border-slate-200 rounded-[6px] p-3 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-1.5 mb-1.5">
                            <span class="px-1.5 py-0.5 rounded-[4px] bg-emerald-700 text-white text-[10px] font-bold font-telemetry">TAHAP 04</span>
                            <span class="text-[10px] font-bold uppercase text-emerald-800 font-telemetry">Terbuka</span>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 leading-snug">
                            Publikasi Informasi
                        </h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Informasi kejadian dan kebutuhan yang telah dikelola ditampilkan kepada publik tanpa mengungkapkan data pribadi. Asal sumber dan status informasinya tetap dapat ditelusuri.
                        </p>
                    </div>
                    <div class="mt-2.5 pt-2 border-t border-slate-200 flex items-center justify-between text-[10px] font-telemetry text-emerald-800">
                        <span class="font-medium">Akses Publik</span>
                        <span class="font-bold">&rarr;</span>
                    </div>
                </div>

                <!-- Tahap 5 -->
                <div class="bg-slate-50 border border-slate-200 rounded-[6px] p-3 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-1.5 mb-1.5">
                            <span class="px-1.5 py-0.5 rounded-[4px] bg-blue-700 text-white text-[10px] font-bold font-telemetry">TAHAP 05</span>
                            <span class="text-[10px] font-bold uppercase text-blue-800 font-telemetry">Partisipasi</span>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 leading-snug">
                            Pemantauan Kejadian dan Respons Bantuan
                        </h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Masyarakat, komunitas, dan organisasi dapat mencatat rencana maupun perkembangan penyaluran bantuan secara terbuka.
                        </p>
                    </div>
                    <div class="mt-2.5 pt-2 border-t border-slate-200 flex items-center justify-between text-[10px] font-telemetry text-blue-800">
                        <span class="font-medium">Respons Tercatat</span>
                        <span class="font-bold">&rarr;</span>
                    </div>
                </div>

                <!-- Tahap 6 -->
                <div class="bg-slate-50 border border-slate-200 rounded-[6px] p-3 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-1.5 mb-1.5">
                            <span class="px-1.5 py-0.5 rounded-[4px] bg-slate-900 text-white text-[10px] font-bold font-telemetry">TAHAP 06</span>
                            <span class="text-[10px] font-bold uppercase text-slate-700 font-telemetry">Kronologi</span>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 leading-snug">
                            Pembaruan Status dan Riwayat Perkembangan
                        </h3>
                        <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                            Perubahan informasi dan status bantuan dicatat dalam lini masa sehingga perkembangan kejadian dapat ditelusuri melalui SitRep dan Replay.
                        </p>
                    </div>
                    <div class="mt-2.5 pt-2 border-t border-slate-200 flex items-center justify-between text-[10px] font-telemetry text-slate-700">
                        <span class="font-medium">SitRep &amp; Replay</span>
                        <span class="font-bold text-emerald-700">&#10003;</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
