@props(['compact' => false])

<section {{ $attributes->merge(['class' => 'bg-white border border-slate-300 rounded-[8px] shadow-xs overflow-hidden']) }} aria-label="Diagram Alur Transparansi Informasi TanggapTerang">
    <div class="bg-slate-900 text-white px-4 py-3 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-blue-400"></span>
            <h2 class="text-xs font-extrabold uppercase tracking-wider font-telemetry">
                Alur Transparansi Informasi &amp; Klasifikasi Sumber Data
            </h2>
        </div>
        <span class="text-[11px] text-slate-300 font-telemetry">
            Data menampilkan informasi yang telah dilaporkan &amp; tercatat pada sistem (bukan klaim integrasi API pemerintah)
        </span>
    </div>

    <div class="p-4 {{ $compact ? 'sm:p-4' : 'sm:p-5' }} space-y-4">
        {{-- 4 Klasifikasi Sumber Data --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
            <div class="bg-[#EFF6FF] border border-[#93C5FD] rounded-[6px] p-2.5">
                <div class="font-telemetry text-[10px] font-bold uppercase text-[#1E40AF]">1. Data Pemerintah Terbuka</div>
                <p class="text-[11px] text-slate-700 mt-0.5 leading-snug">
                    Referensi kode/hirarki wilayah administratif resmi; rujukan instansi pada skenario demo ditandai sebagai <strong>Sumber Pemerintah (Simulasi)</strong>.
                </p>
            </div>
            <div class="bg-indigo-50 border border-indigo-200 rounded-[6px] p-2.5">
                <div class="font-telemetry text-[10px] font-bold uppercase text-indigo-800">2. Sumber Informasi Terbuka</div>
                <p class="text-[11px] text-slate-700 mt-0.5 leading-snug">
                    Rujukan pemberitaan media massa terbuka dan publikasi komunitas yang dicatat beserta tautan dan waktu publikasinya.
                </p>
            </div>
            <div class="bg-emerald-50 border border-emerald-200 rounded-[6px] p-2.5">
                <div class="font-telemetry text-[10px] font-bold uppercase text-emerald-800">3. Laporan Warga</div>
                <p class="text-[11px] text-slate-700 mt-0.5 leading-snug">
                    Laporan kejadian &amp; kebutuhan dari masyarakat tanpa akun dengan kode tiket unik non-sekuensial.
                </p>
            </div>
            <div class="bg-amber-50 border border-amber-300 rounded-[6px] p-2.5">
                <div class="font-telemetry text-[10px] font-bold uppercase text-amber-900">4. Respons Bantuan Masyarakat</div>
                <p class="text-[11px] text-slate-700 mt-0.5 leading-snug">
                    Catatan partisipasi komunitas, relawan, &amp; masyarakat dengan tahapan status <em>Direncanakan</em>, <em>Dalam Penyaluran</em>, dan <em>Penyerahan Dikonfirmasi</em>.
                </p>
            </div>
        </div>

        {{-- 6 Tahap Alur Transparansi --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 relative">
            {{-- Tahap 1 --}}
            <div class="bg-slate-50 border border-slate-200 rounded-[6px] p-3 flex flex-col justify-between relative">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="px-2 py-0.5 rounded-[4px] bg-slate-900 text-white text-[10px] font-bold font-telemetry">TAHAP 01</span>
                        <span class="text-[10px] font-bold uppercase text-slate-600 font-telemetry">Input</span>
                    </div>
                    <h3 class="text-xs font-bold text-slate-900 leading-snug">
                        Laporan Warga &amp; Sumber Terbuka
                    </h3>
                    <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                        Warga mengirim laporan kejadian/kebutuhan dan sistem mencatat rujukan sumber informasi terbuka.
                    </p>
                </div>
                <div class="mt-2 pt-2 border-t border-slate-200 flex items-center justify-between text-[10px] font-telemetry text-slate-600">
                    <span>Tiket Non-Sekuensial</span>
                    <span class="font-bold text-slate-900">&rarr;</span>
                </div>
            </div>

            {{-- Tahap 2 --}}
            <div class="bg-slate-50 border border-slate-200 rounded-[6px] p-3 flex flex-col justify-between relative">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="px-2 py-0.5 rounded-[4px] bg-slate-900 text-white text-[10px] font-bold font-telemetry">TAHAP 02</span>
                        <span class="text-[10px] font-bold uppercase text-slate-600 font-telemetry">Kurasi</span>
                    </div>
                    <h3 class="text-xs font-bold text-slate-900 leading-snug">
                        Pengelolaan Data
                    </h3>
                    <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                        Laporan dikelompokkan berdasarkan lokasi, waktu, dan keterkaitan dengan kejadian.
                    </p>
                </div>
                <div class="mt-2 pt-2 border-t border-slate-200 flex items-center justify-between text-[10px] font-telemetry text-slate-600">
                    <span>Klaster Kejadian</span>
                    <span class="font-bold text-slate-900">&rarr;</span>
                </div>
            </div>

            {{-- Tahap 3 --}}
            <div class="bg-slate-50 border border-slate-200 rounded-[6px] p-3 flex flex-col justify-between relative">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="px-2 py-0.5 rounded-[4px] bg-blue-700 text-white text-[10px] font-bold font-telemetry">TAHAP 03</span>
                        <span class="text-[10px] font-bold uppercase text-blue-800 font-telemetry">Verifikasi</span>
                    </div>
                    <h3 class="text-xs font-bold text-slate-900 leading-snug">
                        Verifikasi Informasi &amp; Pencatatan Kebutuhan
                    </h3>
                    <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                        Tim verifikator memeriksa silang sumber dan mencatat kategori kebutuhan yang dilaporkan.
                    </p>
                </div>
                <div class="mt-2 pt-2 border-t border-slate-200 flex items-center justify-between text-[10px] font-telemetry text-blue-800">
                    <span>Audit Trail</span>
                    <span class="font-bold">&rarr;</span>
                </div>
            </div>

            {{-- Tahap 4 --}}
            <div class="bg-slate-50 border border-slate-200 rounded-[6px] p-3 flex flex-col justify-between relative">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="px-2 py-0.5 rounded-[4px] bg-emerald-700 text-white text-[10px] font-bold font-telemetry">TAHAP 04</span>
                        <span class="text-[10px] font-bold uppercase text-emerald-800 font-telemetry">Terbuka</span>
                    </div>
                    <h3 class="text-xs font-bold text-slate-900 leading-snug">
                        Data Publik
                    </h3>
                    <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                        Informasi kejadian &amp; kebutuhan yang tercatat dipublikasikan tanpa membocorkan data pribadi.
                    </p>
                </div>
                <div class="mt-2 pt-2 border-t border-slate-200 flex items-center justify-between text-[10px] font-telemetry text-emerald-800">
                    <span>Akses Publik</span>
                    <span class="font-bold">&rarr;</span>
                </div>
            </div>

            {{-- Tahap 5 --}}
            <div class="bg-slate-50 border border-slate-200 rounded-[6px] p-3 flex flex-col justify-between relative">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="px-2 py-0.5 rounded-[4px] bg-blue-700 text-white text-[10px] font-bold font-telemetry">TAHAP 05</span>
                        <span class="text-[10px] font-bold uppercase text-blue-800 font-telemetry">Partisipasi</span>
                    </div>
                    <h3 class="text-xs font-bold text-slate-900 leading-snug">
                        Pemantauan Kejadian &amp; Respons Bantuan
                    </h3>
                    <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                        Masyarakat, komunitas, &amp; organisasi mencatat rencana atau penyaluran bantuan secara terbuka.
                    </p>
                </div>
                <div class="mt-2 pt-2 border-t border-slate-200 flex items-center justify-between text-[10px] font-telemetry text-blue-800">
                    <span>Respons Tercatat</span>
                    <span class="font-bold">&rarr;</span>
                </div>
            </div>

            {{-- Tahap 6 --}}
            <div class="bg-slate-50 border border-slate-200 rounded-[6px] p-3 flex flex-col justify-between relative">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="px-2 py-0.5 rounded-[4px] bg-slate-900 text-white text-[10px] font-bold font-telemetry">TAHAP 06</span>
                        <span class="text-[10px] font-bold uppercase text-slate-700 font-telemetry">Kronologi</span>
                    </div>
                    <h3 class="text-xs font-bold text-slate-900 leading-snug">
                        Pembaruan Status &amp; Riwayat Perkembangan
                    </h3>
                    <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                        Status bantuan (Direncanakan &rarr; Dalam Penyaluran &rarr; Dikonfirmasi) tercatat di lini masa.
                    </p>
                </div>
                <div class="mt-2 pt-2 border-t border-slate-200 flex items-center justify-between text-[10px] font-telemetry text-slate-700">
                    <span>SitRep &amp; Replay</span>
                    <span class="font-bold text-emerald-700">&#10003;</span>
                </div>
            </div>
        </div>
    </div>
</section>
