<!-- Internal Footer Component - TanggapTerang -->
<footer class="bg-white border-t border-slate-100 mt-12 py-5 text-[#788397] no-print" role="contentinfo">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-3 text-xs">
        <div class="flex flex-wrap items-center justify-center md:justify-start gap-2.5 text-center md:text-left">
            <img src="{{ asset('images/logo-tanggap-terang.png') }}"
                 alt="Logo TanggapTerang"
                 class="h-6 w-auto object-contain shrink-0">
            <span class="font-extrabold text-[#173B70]">TanggapTerang</span>
            <span class="text-slate-300 hidden md:inline">|</span>
            <span class="text-[#202938]/80">
                Panel Pengelolaan, Verifikasi Informasi, &amp; Tindak Lanjut Kejadian
            </span>
        </div>

        <div class="flex flex-wrap items-center justify-center md:justify-end gap-3 font-telemetry text-[11px] text-[#788397]">
            <span class="px-2 py-0.5 rounded bg-slate-100 text-[#202938] border border-slate-200/80 uppercase font-bold text-[10px]">
                Internal Console
            </span>
            <span>&copy; {{ date('Y') }} TanggapTerang.</span>
            <a href="{{ route('public.monitor') }}"
               class="link-text font-bold"
               target="_blank" rel="noopener">
                Buka publik<span class="sr-only"> (membuka tab baru)</span>
            </a>
        </div>
    </div>
</footer>

