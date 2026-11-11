@props(['compact' => false])

<section {{ $attributes->merge(['class' => 'bg-white border border-slate-100/90 rounded-2xl shadow-xs overflow-hidden']) }} aria-label="Diagram Alur Transparansi Informasi TanggapTerang">
    <!-- Header Command Bar -->
    <div class="bg-[#173B70] text-white px-4 sm:px-6 py-4 border-b border-[#315D91] flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div class="flex items-center gap-2.5">
            <span class="inline-block w-2.5 h-2.5 rounded-full bg-blue-300"></span>
            <h2 class="text-xs sm:text-sm font-extrabold uppercase tracking-wider font-telemetry">
                Alur Transparansi Informasi &amp; Klasifikasi Sumber Data
            </h2>
        </div>
        <div class="flex items-center gap-3">
            <span class="hidden md:inline-block text-[11px] text-blue-100 font-telemetry">
                Transparansi Laporan Warga • Referensi Terbuka • Layanan API Eksternal
            </span>
            <a href="{{ route('public.sources.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#315D91] hover:bg-white hover:text-[#173B70] text-white font-telemetry text-xs font-bold uppercase tracking-wider transition shadow-2xs">
                <span>Transparansi Sumber Data</span>
                <span>&rarr;</span>
            </a>
        </div>
    </div>

    <!-- Deskripsi Pengantar Alur -->
    <div class="px-4 sm:px-6 py-3 bg-[#F8F9FC] border-b border-slate-100 text-xs text-[#202938]/80 leading-relaxed">
        <p>
            TanggapTerang menampilkan asal sumber, waktu pembaruan, dan status verifikasi agar seluruh informasi dapat ditelusuri secara terbuka.
        </p>
    </div>

    <div class="p-4 {{ $compact ? 'sm:p-4' : 'sm:p-5' }} space-y-5">
        <!-- 4 Klasifikasi Sumber Data -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- 1. Data Pemerintah dan Referensi Wilayah -->
            <div class="bg-blue-50/70 border border-blue-200/80 rounded-xl p-3.5 flex flex-col justify-between">
                <div>
                    <div class="font-telemetry text-[11px] font-bold uppercase text-[#173B70]">
                        1. Data Pemerintah &amp; Wilayah
                    </div>
                    <p class="text-[11px] text-[#202938]/85 mt-1.5 leading-relaxed">
                        Data kode dan hierarki wilayah administratif digunakan sebagai referensi lokasi. Informasi instansi pemerintah yang belum terhubung langsung dengan sumber resmi pada demonstrasi diberi label <strong>Sumber Pemerintah (Simulasi)</strong>.
                    </p>
                </div>
            </div>

            <!-- 2. Integrasi API dan Sumber Informasi Terbuka -->
            <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 flex flex-col justify-between">
                <div>
                    <div class="font-telemetry text-[11px] font-bold uppercase text-[#315D91]">
                        2. Integrasi API &amp; Sumber Informasi Terbuka
                    </div>
                    <p class="text-[11px] text-[#202938]/85 mt-1.5 leading-relaxed">
                        TanggapTerang mengolah informasi dari berbagai layanan eksternal untuk mendukung pemantauan kejadian dan kondisi wilayah.
                    </p>
                    <ul class="text-[10px] text-[#202938]/85 mt-1.5 space-y-0.5 list-disc list-inside">
                        <li><strong>BMKG</strong> — informasi gempa bumi dan prakiraan cuaca.</li>
                        <li><strong>PetaBencana</strong> — laporan kejadian berbasis partisipasi masyarakat.</li>
                        <li><strong>Open-Meteo</strong> — prakiraan cuaca dan indikator hidrologi.</li>
                        <li><strong>GDACS</strong> — informasi peringatan multi-bahaya.</li>
                        <li><strong>GDELT</strong> — referensi pemberitaan global.</li>
                    </ul>
                    <p class="text-[10px] text-[#788397] mt-2 leading-relaxed">
                        Setiap sumber dilengkapi informasi status provider, waktu pembaruan terakhir, jumlah data yang diterima, serta catatan keterbatasan sumber.
                    </p>
                    <div class="mt-3 pt-2.5 border-t border-slate-200 flex items-center justify-between text-[11px] font-telemetry">
                        <span class="text-[#315D91] font-bold">Status 8 Provider</span>
                        <a href="{{ route('public.sources.index') }}" class="font-bold text-[#173B70] hover:underline inline-flex items-center gap-1">
                            <span>Detail Sumber Data</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. Laporan Warga -->
            <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-xl p-3.5 flex flex-col justify-between">
                <div>
                    <div class="font-telemetry text-[11px] font-bold uppercase text-emerald-900">
                        3. Laporan Warga
                    </div>
                    <p class="text-[11px] text-[#202938]/85 mt-1.5 leading-relaxed">
                        Masyarakat dapat melaporkan kejadian dan kebutuhan tanpa akun menggunakan kode tiket unik. Setiap laporan menjadi informasi awal yang perlu dikelola dan diverifikasi.
                    </p>
                </div>
            </div>

            <!-- 4. Respons Bantuan Masyarakat -->
            <div class="bg-amber-50/70 border border-amber-200/80 rounded-xl p-3.5 flex flex-col justify-between">
                <div>
                    <div class="font-telemetry text-[11px] font-bold uppercase text-amber-950">
                        4. Respons Bantuan Masyarakat
                    </div>
                    <p class="text-[11px] text-[#202938]/85 mt-1.5 leading-relaxed">
                        Komunitas dan relawan dapat mencatat partisipasi serta perkembangan bantuan melalui tahapan <em>Direncanakan</em>, <em>Dalam Penyaluran</em>, dan <em>Penyerahan Dikonfirmasi</em>.
                    </p>
                </div>
            </div>
        </div>

        <!-- 6 Tahap Alur Transparansi -->
        <div>
            <div class="text-[11px] font-bold font-telemetry uppercase text-[#788397] tracking-wider mb-2.5">
                Alur Tahapan Pengelolaan &amp; Publikasi Informasi (Tahap 01 — 06)
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                <!-- Tahap 1 -->
                <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-1.5 mb-1.5">
                            <span class="px-1.5 py-0.5 rounded-[4px] bg-[#173B70] text-white text-[10px] font-bold font-telemetry">TAHAP 01</span>
                            <span class="text-[10px] font-bold uppercase text-[#788397] font-telemetry">Input</span>
                        </div>
                        <h3 class="text-xs font-bold text-[#202938] leading-snug">
                            Laporan Warga &amp; Data Eksternal
                        </h3>
                        <p class="text-[11px] text-[#788397] mt-1 leading-relaxed">
                            Warga mengirimkan laporan kejadian dan sistem mengambil informasi dari sumber eksternal yang tersedia melalui API.
                        </p>
                    </div>
                    <div class="mt-2.5 pt-2 border-t border-slate-200/80 flex items-center justify-between text-[10px] font-telemetry text-[#788397]">
                        <span class="font-medium truncate">Tiket Non-Sekuensial</span>
                        <span class="font-bold text-[#173B70] shrink-0">&rarr; Rekaman Data</span>
                    </div>
                </div>

                <!-- Tahap 2 -->
                <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-1.5 mb-1.5">
                            <span class="px-1.5 py-0.5 rounded-[4px] bg-[#173B70] text-white text-[10px] font-bold font-telemetry">TAHAP 02</span>
                            <span class="text-[10px] font-bold uppercase text-[#788397] font-telemetry">Kurasi</span>
                        </div>
                        <h3 class="text-xs font-bold text-[#202938] leading-snug">
                            Pengelolaan Data
                        </h3>
                        <p class="text-[11px] text-[#788397] mt-1 leading-relaxed">
                            Informasi dikelompokkan berdasarkan lokasi, waktu, kategori, dan keterkaitan dengan kejadian.
                        </p>
                    </div>
                    <div class="mt-2.5 pt-2 border-t border-slate-200/80 flex items-center justify-between text-[10px] font-telemetry text-[#788397]">
                        <span class="font-medium">Klaster Kejadian</span>
                        <span class="font-bold text-[#173B70]">&rarr;</span>
                    </div>
                </div>

                <!-- Tahap 3 -->
                <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-1.5 mb-1.5">
                            <span class="px-1.5 py-0.5 rounded-[4px] bg-[#315D91] text-white text-[10px] font-bold font-telemetry">TAHAP 03</span>
                            <span class="text-[10px] font-bold uppercase text-[#315D91] font-telemetry">Verifikasi</span>
                        </div>
                        <h3 class="text-xs font-bold text-[#202938] leading-snug">
                            Pemeriksaan &amp; Kebutuhan
                        </h3>
                        <p class="text-[11px] text-[#788397] mt-1 leading-relaxed">
                            Tim verifikator memeriksa silang laporan dan referensi yang tersedia.
                        </p>
                    </div>
                    <div class="mt-2.5 pt-2 border-t border-slate-200/80 flex items-center justify-between text-[10px] font-telemetry text-[#315D91]">
                        <span class="font-medium">Audit Trail</span>
                        <span class="font-bold">&rarr;</span>
                    </div>
                </div>

                <!-- Tahap 4 -->
                <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-1.5 mb-1.5">
                            <span class="px-1.5 py-0.5 rounded-[4px] bg-emerald-600 text-white text-[10px] font-bold font-telemetry">TAHAP 04</span>
                            <span class="text-[10px] font-bold uppercase text-emerald-800 font-telemetry">Terbuka</span>
                        </div>
                        <h3 class="text-xs font-bold text-[#202938] leading-snug">
                            Publikasi Informasi
                        </h3>
                        <p class="text-[11px] text-[#788397] mt-1 leading-relaxed">
                            Informasi kejadian dan kebutuhan yang telah dikelola ditampilkan kepada publik secara transparan.
                        </p>
                    </div>
                    <div class="mt-2.5 pt-2 border-t border-slate-200/80 flex items-center justify-between text-[10px] font-telemetry text-emerald-800">
                        <span class="font-medium">Akses Publik</span>
                        <span class="font-bold">&rarr;</span>
                    </div>
                </div>

                <!-- Tahap 5 -->
                <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-1.5 mb-1.5">
                            <span class="px-1.5 py-0.5 rounded-[4px] bg-[#173B70] text-white text-[10px] font-bold font-telemetry">TAHAP 05</span>
                            <span class="text-[10px] font-bold uppercase text-[#173B70] font-telemetry">Partisipasi</span>
                        </div>
                        <h3 class="text-xs font-bold text-[#202938] leading-snug">
                            Pemantauan &amp; Respons
                        </h3>
                        <p class="text-[11px] text-[#788397] mt-1 leading-relaxed">
                            Masyarakat dan komunitas dapat mencatat rencana maupun penyaluran bantuan secara terbuka.
                        </p>
                    </div>
                    <div class="mt-2.5 pt-2 border-t border-slate-200/80 flex items-center justify-between text-[10px] font-telemetry text-[#173B70]">
                        <span class="font-medium">Respons Tercatat</span>
                        <span class="font-bold">&rarr;</span>
                    </div>
                </div>

                <!-- Tahap 6 -->
                <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-1.5 mb-1.5">
                            <span class="px-1.5 py-0.5 rounded-[4px] bg-[#173B70] text-white text-[10px] font-bold font-telemetry">TAHAP 06</span>
                            <span class="text-[10px] font-bold uppercase text-[#788397] font-telemetry">Kronologi</span>
                        </div>
                        <h3 class="text-xs font-bold text-[#202938] leading-snug">
                            Pembaruan Status &amp; Riwayat
                        </h3>
                        <p class="text-[11px] text-[#788397] mt-1 leading-relaxed">
                            Perubahan informasi dan status bantuan dicatat dalam lini masa sehingga dapat ditelusuri melalui SitRep dan Replay.
                        </p>
                    </div>
                    <div class="mt-2.5 pt-2 border-t border-slate-200/80 flex items-center justify-between text-[10px] font-telemetry text-[#788397]">
                        <span class="font-medium">SitRep &amp; Replay</span>
                        <span class="font-bold text-emerald-700">&#10003;</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
