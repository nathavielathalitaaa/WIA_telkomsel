<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'TanggapTerang — Informasi Kejadian & Transparansi Tindak Lanjut' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-tanggap-terang.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-tanggap-terang.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-[#F8FAFC] text-[#0F172A] antialiased min-h-screen flex flex-col pb-20 lg:pb-0"
      @if(session('success')) data-flash-success="{{ session('success') }}" @endif
      @if(session('error')) data-flash-error="{{ session('error') }}" @endif>
    <!-- Toast Notification Container -->
    <div id="tanggap-toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-2.5 max-w-sm pointer-events-none" aria-live="polite"></div>
    <!-- Sticky Institutional Top Header -->
    <header class="sticky top-0 z-40 bg-white border-b border-[#E2E8F0] no-print">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-2 sm:gap-4">
            <!-- Brand Identity -->
            <a href="{{ route('public.monitor') }}" class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                <img src="{{ asset('images/logo-tanggap-terang.png') }}"
                     alt="Logo TanggapTerang"
                     class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-base sm:text-lg font-extrabold tracking-tight text-[#0F172A] leading-none whitespace-nowrap">TanggapTerang</span>
                        <span class="hidden md:inline-flex px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 border border-amber-300 font-telemetry text-[10px] font-bold uppercase shrink-0">
                            Prototype Simulasi
                        </span>
                    </div>
                    <p class="hidden sm:block font-telemetry text-[11px] text-slate-500 uppercase tracking-wider truncate mt-0.5">
                        Terlihat, Terlacak, dan Terukur • Papan Transparansi Respons Bencana
                    </p>
                </div>
            </a>

            <!-- Desktop Main Navigation (4 Tabs) -->
            @php
                $activeTab = $activeTab ?? '';
                $replayHref = \Illuminate\Support\Facades\Route::has('public.replay') ? route('public.replay') : '#';
                $sitrepHref = \Illuminate\Support\Facades\Route::has('public.sitrep') ? route('public.sitrep') : '#';
                $welcomeHref = \Illuminate\Support\Facades\Route::has('public.welcome') ? route('public.welcome') : route('public.monitor');
                $loginHref = \Illuminate\Support\Facades\Route::has('login') ? route('login') : '#';
                $internalHref = \Illuminate\Support\Facades\Route::has('internal.dashboard') ? route('internal.dashboard') : '#';
            @endphp
            <nav class="hidden lg:flex items-center gap-1" aria-label="Navigasi Utama">
                <a href="{{ route('public.monitor') }}"
                   class="px-3.5 py-2 rounded-lg font-telemetry text-xs font-bold uppercase tracking-wider transition {{ $activeTab === 'pantau' ? 'nav-active-gradient' : 'text-slate-600 hover:bg-slate-100 hover:text-[#0F172A]' }}">
                    Pantau
                </a>
                <a href="{{ route('public.track') }}"
                   class="px-3.5 py-2 rounded-lg font-telemetry text-xs font-bold uppercase tracking-wider transition {{ $activeTab === 'lacak' ? 'nav-active-gradient' : 'text-slate-600 hover:bg-slate-100 hover:text-[#0F172A]' }}">
                    Lacak Tiket
                </a>
                <a href="{{ $replayHref }}"
                   class="px-3.5 py-2 rounded-lg font-telemetry text-xs font-bold uppercase tracking-wider transition {{ $activeTab === 'replay' ? 'nav-active-gradient' : 'text-slate-600 hover:bg-slate-100 hover:text-[#0F172A]' }}">
                    Linimasa
                </a>
                <a href="{{ $sitrepHref }}"
                   class="px-3.5 py-2 rounded-lg font-telemetry text-xs font-bold uppercase tracking-wider transition {{ $activeTab === 'laporan' ? 'nav-active-gradient' : 'text-slate-600 hover:bg-slate-100 hover:text-[#0F172A]' }}">
                    SitRep
                </a>
                <a href="{{ route('public.sources.index') }}"
                   class="px-3.5 py-2 rounded-lg font-telemetry text-xs font-bold uppercase tracking-wider transition {{ $activeTab === 'sumber_data' ? 'nav-active-gradient' : 'text-slate-600 hover:bg-slate-100 hover:text-[#0F172A]' }}">
                    Tentang Data
                </a>
            </nav>

            <!-- Action Triggers (Report & Internal Portal) -->
            <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                <a href="{{ route('public.reports.create') }}"
                   @if(request()->routeIs('public.reports.create')) aria-current="page" @endif
                   class="px-2.5 sm:px-3.5 py-2 sm:py-2.5 rounded-lg btn-brand-gradient font-telemetry text-[11px] sm:text-xs font-bold uppercase tracking-wider inline-flex items-center gap-1 sm:gap-1.5 min-h-[40px] sm:min-h-[42px] shadow-xs {{ request()->routeIs('public.reports.create') ? 'ring-2 ring-[#0F172A] ring-offset-1' : '' }}">
                    <span class="font-extrabold text-sm leading-none">+</span>
                    <span class="hidden sm:inline">Lapor Kejadian</span>
                    <span class="sm:hidden">Lapor</span>
                </a>

                @auth
                    <a href="{{ $internalHref }}"
                       class="px-2.5 sm:px-3 py-2 sm:py-2.5 rounded-lg border border-[#CBD5E1] bg-white hover:bg-slate-50 text-[#0F172A] font-telemetry text-[11px] sm:text-xs font-bold uppercase tracking-wider transition min-h-[40px] sm:min-h-[42px] inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-[#059669]"></span>
                        <span class="hidden sm:inline">Panel Internal</span>
                        <span class="sm:hidden">Internal</span>
                    </a>
                @else
                    <a href="{{ $loginHref }}"
                       class="px-2.5 sm:px-3 py-2 sm:py-2.5 rounded-lg border border-[#CBD5E1] bg-white hover:bg-slate-50 text-[#0F172A] font-telemetry text-[11px] sm:text-xs font-bold uppercase tracking-wider transition min-h-[40px] sm:min-h-[42px] inline-flex items-center">
                        <span>Login Tim</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-5 space-y-5">
        <x-simulation-banner />

        @if(session('success'))
            <div class="bg-[#ECFDF5] border-l-4 border-[#059669] border-y border-r border-[#6EE7B7] rounded-lg p-4 text-[#065F46] text-sm font-medium no-print" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-[#FEF2F2] border-l-4 border-[#DC2626] border-y border-r border-[#FCA5A5] rounded-lg p-4 text-[#991B1B] text-sm font-medium no-print" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if(isset($slot) && (is_string($slot) || $slot instanceof \Illuminate\Contracts\Support\Htmlable))
            {{ $slot }}
        @endif
        @yield('content')
    </main>

    <!-- Institutional Public Footer -->
    <x-public-footer />

    <!-- Mobile Bottom Navigation Bar (4 Main Public Tabs) -->
    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white border-t border-[#E2E8F0] h-16 grid grid-cols-4 p-1 gap-1 no-print" aria-label="Navigasi Bawah Mobile">
        <a href="{{ route('public.monitor') }}"
           class="flex flex-col items-center justify-center gap-0.5 rounded-lg min-h-[48px] transition {{ $activeTab === 'pantau' ? 'nav-active-gradient font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            <span class="font-telemetry text-[10px] uppercase tracking-wider font-bold">Pantau</span>
        </a>
        <a href="{{ route('public.track') }}"
           class="flex flex-col items-center justify-center gap-0.5 rounded-lg min-h-[48px] transition {{ $activeTab === 'lacak' ? 'nav-active-gradient font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span class="font-telemetry text-[10px] uppercase tracking-wider font-bold">Lacak</span>
        </a>
        <a href="{{ $replayHref }}"
           class="flex flex-col items-center justify-center gap-0.5 rounded-lg min-h-[48px] transition {{ $activeTab === 'replay' ? 'nav-active-gradient font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-telemetry text-[10px] uppercase tracking-wider font-bold">Replay</span>
        </a>
        <a href="{{ $sitrepHref }}"
           class="flex flex-col items-center justify-center gap-0.5 rounded-lg min-h-[48px] transition {{ $activeTab === 'laporan' ? 'nav-active-gradient font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="font-telemetry text-[10px] uppercase tracking-wider font-bold">Laporan</span>
        </a>
    </nav>

    @stack('scripts')
</body>
</html>
