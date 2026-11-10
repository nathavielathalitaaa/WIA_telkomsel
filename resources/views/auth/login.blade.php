@extends('layouts.app', [
    'title' => 'Layanan Pelaporan & Akses Petugas — TanggapTerang',
    'activeTab' => '',
])

@section('content')
<div class="max-w-4xl mx-auto py-2 sm:py-6 space-y-12 sm:space-y-16">

    <!-- 1. Hero Section: Public-First Reporting Invitation -->
    <section class="text-center space-y-6 pt-2 sm:pt-4">
        <!-- Headline -->
        <div class="space-y-3 max-w-2xl mx-auto">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#202938] tracking-tight leading-tight">
                Lihat Kejadian Bencana di Sekitar Anda?
                <span class="block text-gradient-brand mt-1 sm:mt-2">Laporkan Sekarang.</span>
            </h1>
            <p class="text-sm sm:text-base text-[#788397] leading-relaxed max-w-xl mx-auto">
                Setiap informasi yang Anda sampaikan membantu mempercepat validasi di lapangan, memperjelas kebutuhan darurat warga terdampak, dan menjaga transparansi koordinasi respons bersama.
            </p>
        </div>

        <!-- Primary Call to Action -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="{{ route('public.reports.create') }}"
               class="w-full sm:w-auto px-7 py-3.5 rounded-xl btn-brand-gradient text-white font-telemetry text-sm font-bold uppercase tracking-wider inline-flex items-center justify-center gap-2.5 shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 active:translate-y-0">
                <svg class="w-5 h-5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>Laporkan Kejadian</span>
                <span class="text-xs opacity-90">&rarr;</span>
            </a>

            <a href="{{ route('public.monitor') }}"
               class="w-full sm:w-auto px-6 py-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-[#202938] font-telemetry text-sm font-bold uppercase tracking-wider inline-flex items-center justify-center gap-2 transition shadow-xs">
                <span>Pantau Kejadian Terkini</span>
            </a>
        </div>

        <!-- Trust Indicators -->
        <div class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2 pt-2 text-xs text-[#788397] font-telemetry">
            <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#059669]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                Tanpa perlu mendaftar atau login
            </span>
            <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#059669]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                Diberikan nomor tiket lacak mandiri
            </span>
            <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#059669]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                Privasi &amp; data pelapor terlindungi
            </span>
        </div>
    </section>

    <!-- 2. Public Information Section: Core Pillars -->
    <section class="space-y-6">
        <div class="text-center max-w-lg mx-auto space-y-1.5">
            <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#173B70]">
                Papan Transparansi &amp; Koordinasi
            </span>
            <h2 class="text-xl sm:text-2xl font-extrabold text-[#202938]">
                Bagaimana TanggapTerang Bekerja
            </h2>
            <p class="text-xs sm:text-sm text-[#788397] leading-relaxed">
                Tiga pilar informasi terbuka untuk menghubungkan laporan masyarakat dengan tindak lanjut penanganan bencana.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
            <!-- Card 1: Pemantauan Kejadian -->
            <div class="bg-white rounded-xl border border-slate-100 p-5 space-y-3 card-interactive">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-[#173B70] flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <h3 class="font-telemetry text-sm font-bold uppercase tracking-wide text-[#202938]">
                    1. Pemantauan Kejadian
                </h3>
                <p class="text-xs text-[#788397] leading-relaxed">
                    Setiap laporan diverifikasi dan dipetakan secara terbuka. Publik dapat memantau sebaran lokasi, tingkat keparahan, dan keabsahan fakta terkini.
                </p>
            </div>

            <!-- Card 2: Kebutuhan Lapangan -->
            <div class="bg-white rounded-xl border border-slate-100 p-5 space-y-3 card-interactive">
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h3 class="font-telemetry text-sm font-bold uppercase tracking-wide text-[#202938]">
                    2. Kebutuhan Lapangan
                </h3>
                <p class="text-xs text-[#788397] leading-relaxed">
                    Kebutuhan evakuasi, medis, tenda, makanan, dan logistik tercatat secara transparan agar dukungan relawan dan lembaga tepat sasaran.
                </p>
            </div>

            <!-- Card 3: Perkembangan Respons -->
            <div class="bg-white rounded-xl border border-slate-100 p-5 space-y-3 card-interactive">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="font-telemetry text-sm font-bold uppercase tracking-wide text-[#202938]">
                    3. Perkembangan Respons
                </h3>
                <p class="text-xs text-[#788397] leading-relaxed">
                    Setiap koordinasi tindak lanjut didokumentasikan dalam linimasa kronologis terbuka yang dapat ditelusuri riwayat penanganannya.
                </p>
            </div>
        </div>
    </section>

    <!-- 3. Internal Login Section: Dedicated Staff Portal at the Bottom -->
    <section id="login-petugas" class="pt-4 border-t border-slate-200/80 space-y-6">
        <div class="text-center max-w-md mx-auto space-y-2">
            <span class="inline-flex px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-telemetry text-[10px] font-bold uppercase tracking-wider">
                Khusus Petugas Internal
            </span>
            <h2 class="text-2xl font-extrabold text-[#202938]">
                Akses untuk Tim TanggapTerang
            </h2>
            <p class="text-xs sm:text-sm text-[#788397] leading-relaxed">
                Masuk ke Panel Pengelolaan TanggapTerang bagi petugas yang berwenang memverifikasi laporan masyarakat dan memperbarui koordinasi tindak lanjut lapangan.
            </p>
        </div>

        <div class="max-w-md mx-auto bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-5">
            @if($errors->any())
                <div class="bg-[#FEF2F2] border-l-4 border-[#DC2626] border-y border-r border-[#FCA5A5] rounded-lg p-3.5 text-xs text-[#991B1B] space-y-1" role="alert">
                    <div class="font-telemetry font-bold uppercase tracking-wider">Gagal Masuk</div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4" novalidate>
                @csrf

                <div>
                    <label for="email" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1.5">
                        Email Petugas Internal
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autocomplete="username"
                           placeholder="nama.petugas@tanggap-terang.id"
                           class="w-full min-h-[44px] px-3.5 py-2.5 bg-white border {{ $errors->has('email') ? 'border-[#DC2626]' : 'border-slate-200/90' }} rounded-lg text-sm text-[#202938] focus:outline-none focus:border-[#173B70] transition shadow-2xs">
                    @error('email')
                        <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                            Kata Sandi
                        </label>
                    </div>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           autocomplete="current-password"
                           placeholder="Masukkan kata sandi akun internal"
                           class="w-full min-h-[44px] px-3.5 py-2.5 bg-white border {{ $errors->has('password') ? 'border-[#DC2626]' : 'border-slate-200/90' }} rounded-lg text-sm text-[#202938] focus:outline-none focus:border-[#173B70] transition shadow-2xs">
                    @error('password')
                        <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full py-3 px-4 rounded-lg bg-[#173B70] hover:bg-[#0F2A52] active:scale-[0.99] text-white font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[44px] inline-flex items-center justify-center gap-2 cursor-pointer shadow-xs">
                        <span>Masuk ke Panel Internal</span>
                        <span>&rarr;</span>
                    </button>
                </div>
            </form>

            <div class="pt-2 border-t border-slate-100 text-center">
                <p class="text-xs text-[#788397]">
                    Bukan petugas? <a href="{{ route('public.monitor') }}" class="font-bold text-[#173B70] hover:underline">Kembali ke Beranda Publik</a> atau <a href="{{ route('public.track') }}" class="font-bold text-[#173B70] hover:underline">Lacak Tiket Laporan</a>
                </p>
            </div>
        </div>
    </section>

</div>
@endsection
