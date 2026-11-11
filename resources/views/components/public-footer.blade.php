<!-- Public Footer Component - TanggapTerang -->
<footer class="bg-white border-t border-slate-100 mt-16 pt-12 pb-24 lg:pb-8 text-[#788397] no-print" role="contentinfo">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 lg:gap-10 pb-10">
            <!-- A. Brand Section (5 cols) -->
            <div class="lg:col-span-5 space-y-4">
                <a href="{{ route('public.monitor') }}" class="inline-flex items-center gap-2.5 group">
                    <img src="{{ asset('images/logo-tanggap-terang.png') }}"
                         alt="Logo TanggapTerang"
                         class="h-9 sm:h-10 w-auto object-contain shrink-0 group-hover:opacity-90 transition-opacity">
                    <div class="flex flex-col">
                        <span class="font-extrabold text-xl text-[#173B70] tracking-tight group-hover:text-[#315D91] transition-colors">
                            TanggapTerang
                        </span>
                        <span class="font-telemetry text-[10px] uppercase tracking-wider text-[#788397] font-bold">
                            Transparansi Respons Bencana
                        </span>
                    </div>
                </a>

                <p class="text-xs sm:text-sm text-[#202938]/80 leading-relaxed max-w-md">
                    Platform transparansi informasi kejadian dan dokumentasi respons bencana berbasis kolaborasi laporan masyarakat serta rujukan sumber informasi terbuka.
                </p>

                <div class="p-3 rounded-lg bg-[#F8F9FC] border border-slate-200/70 border-l-4 border-l-[#173B70]">
                    <p class="font-telemetry text-xs font-bold text-[#202938] leading-snug">
                        &ldquo;Lihat siapa yang merespons, kapan, dan di mana bantuan belum terdokumentasi.&rdquo;
                    </p>
                </div>
            </div>

            <!-- B. Navigasi Halaman (3 cols) -->
            <div class="lg:col-span-3 space-y-3">
                <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                    Navigasi Halaman
                </h3>
                <ul class="space-y-2 text-xs">
                    <li>
                        <a href="{{ route('public.monitor') }}"
                           class="inline-flex items-center gap-1.5 text-[#788397] hover:text-[#173B70] focus:outline-hidden focus:text-[#173B70] transition-colors py-0.5">
                            <span class="text-slate-400">&rsaquo;</span>
                            <span>Pantau Kejadian</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('public.track') }}"
                           class="inline-flex items-center gap-1.5 text-[#788397] hover:text-[#173B70] focus:outline-hidden focus:text-[#173B70] transition-colors py-0.5">
                            <span class="text-slate-400">&rsaquo;</span>
                            <span>Lacak Tiket &amp; Respons</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('public.replay') }}"
                           class="inline-flex items-center gap-1.5 text-[#788397] hover:text-[#173B70] focus:outline-hidden focus:text-[#173B70] transition-colors py-0.5">
                            <span class="text-slate-400">&rsaquo;</span>
                            <span>Linimasa (Replay)</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('public.sitrep') }}"
                           class="inline-flex items-center gap-1.5 text-[#788397] hover:text-[#173B70] focus:outline-hidden focus:text-[#173B70] transition-colors py-0.5">
                            <span class="text-slate-400">&rsaquo;</span>
                            <span>Ringkasan SitRep</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('public.sources.index') }}"
                           class="inline-flex items-center gap-1.5 text-[#788397] hover:text-[#173B70] focus:outline-hidden focus:text-[#173B70] transition-colors py-0.5">
                            <span class="text-slate-400">&rsaquo;</span>
                            <span>Tentang Sumber Data</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('public.reports.create') }}"
                           class="inline-flex items-center gap-1.5 text-[#D94350] hover:text-[#C23541] focus:outline-hidden focus:text-[#C23541] transition-colors py-0.5 font-semibold">
                            <span class="font-bold">+</span>
                            <span>Laporkan Kejadian</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('public.guide') }}"
                           class="inline-flex items-center gap-1.5 text-[#788397] hover:text-[#173B70] focus:outline-hidden focus:text-[#173B70] transition-colors py-0.5">
                            <span class="text-slate-400">&rsaquo;</span>
                            <span>Panduan Penggunaan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('public.welcome') }}"
                           class="inline-flex items-center gap-1.5 text-[#788397] hover:text-[#173B70] focus:outline-hidden focus:text-[#173B70] transition-colors py-0.5">
                            <span class="text-slate-400">&rsaquo;</span>
                            <span>Tentang TanggapTerang</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- C. Informasi Platform & Disclaimer (4 cols) -->
            <div class="lg:col-span-4 space-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                        Informasi Platform Prototype
                    </h3>
                </div>

                <div class="space-y-2.5 text-xs text-[#202938]/85 leading-relaxed bg-[#F8F9FC] border border-slate-100 rounded-xl p-3.5">
                    <p>
                        <strong>TanggapTerang</strong> merupakan sistem prototipe keterbukaan informasi kejadian bencana dan dokumentasi respons masyarakat yang dikembangkan untuk tujuan demonstrasi dan kompetisi.
                    </p>
                    <p>
                        Data yang ditampilkan dapat berupa <strong>data simulasi</strong> dan bukan informasi situasi kedaruratan real-time.
                    </p>
                    <p class="text-[11px] text-[#788397] border-t border-slate-200/80 pt-2">
                        Platform ini bukan merupakan portal resmi pemerintah, BNPB, BPBD, atau lembaga kebencanaan terkait. Untuk kondisi darurat nyata, selalu hubungi nomor darurat resmi instansi terkait.
                    </p>
                </div>
            </div>
        </div>

        <!-- D. Bottom Bar -->
        <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[#788397]">
            <div class="flex flex-wrap items-center gap-2 font-telemetry text-center sm:text-left">
                <span>&copy; {{ date('Y') }} TanggapTerang. Seluruh hak cipta dilindungi.</span>
            </div>
            <div class="flex items-center gap-2 font-telemetry">
                <span class="px-2.5 py-1 rounded bg-[#173B70] text-white text-[10px] font-bold uppercase tracking-wider">
                    Prototype Platform
                </span>
                <span class="text-[11px] text-[#788397] hidden sm:inline">•</span>
                <span class="text-[11px] text-[#788397] hidden sm:inline">Multimedia In Action 2026</span>
            </div>
        </div>
    </div>
</footer>

