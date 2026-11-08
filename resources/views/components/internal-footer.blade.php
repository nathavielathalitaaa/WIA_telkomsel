<!-- Internal Footer Component - TanggapTerang -->
<footer class="bg-white border-t border-[#E2E8F0] mt-12 py-5 text-slate-600 no-print" role="contentinfo">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-3 text-xs">
        <div class="flex flex-wrap items-center justify-center md:justify-start gap-2.5 text-center md:text-left">
            <img src="{{ asset('images/logo-tanggap-terang.png') }}"
                 alt="Logo TanggapTerang"
                 class="h-6 w-auto object-contain shrink-0">
            <span class="font-extrabold text-[#0F172A]">TanggapTerang</span>
            <span class="text-slate-300 hidden md:inline">|</span>
            <span class="text-slate-600">
                Panel Pengelolaan, Verifikasi Informasi, &amp; Tindak Lanjut Kejadian
            </span>
        </div>

        <div class="flex flex-wrap items-center justify-center md:justify-end gap-3 font-telemetry text-[11px] text-slate-500">
            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200 uppercase font-bold text-[10px]">
                Internal Console
            </span>
            <span>&copy; {{ date('Y') }} TanggapTerang.</span>
            <a href="{{ route('public.monitor') }}"
               class="text-[#2563EB] hover:underline font-bold inline-flex items-center gap-0.5"
               target="_blank">
                <span>Buka Publik</span>
                <span>&nearr;</span>
            </a>
        </div>
    </div>
</footer>

