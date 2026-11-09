<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Panel Internal — TanggapTerang' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-tanggap-terang.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-tanggap-terang.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-[#F8FAFC] text-[#0F172A] antialiased min-h-screen flex flex-col"
      @if(session('success')) data-flash-success="{{ session('success') }}" @endif
      @if(session('error')) data-flash-error="{{ session('error') }}" @endif>
    <!-- Toast Notification Container -->
    <div id="tanggap-toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-2.5 max-w-sm pointer-events-none" aria-live="polite"></div>
    <!-- Internal Top Command Bar -->
    @php
        $internalMenu = $internalMenu ?? '';
        $currentUser = auth()->user();
        $roleValue = $currentUser?->role?->value ?? (string) ($currentUser?->role ?? '');
        $roleLabel = $currentUser?->role?->label() ?? ucfirst($roleValue ?: 'Internal');
        $canOperate = $currentUser?->canManageOperations() ?? false;

        [$roleBadgeClass, $headerSubtitle, $reportsMenuLabel, $incidentsMenuLabel] = match ($roleValue) {
            'verifikator' => [
                'bg-blue-600 text-white border border-blue-400',
                'Pemeriksaan & Verifikasi Informasi',
                'Verifikasi Laporan',
                'Verifikasi Kejadian',
            ],
            'operator' => [
                'bg-emerald-600 text-white border border-emerald-400',
                'Pengelolaan Kejadian, Penautan, & Tindak Lanjut',
                'Penautan Laporan',
                'Operasional Kejadian',
            ],
            default => [
                'bg-amber-500 text-slate-950 border border-amber-300',
                'Pengelolaan Sistem, Verifikasi, & Operasional',
                'Kelola Laporan',
                'Kelola Kejadian',
            ],
        };
    @endphp
    <header class="bg-[#0F172A] text-white border-b border-slate-800 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 min-h-[64px] py-2 flex items-center justify-between gap-2 sm:gap-4">
            <div class="flex items-center gap-2.5 sm:gap-4 min-w-0">
                <a href="{{ route('internal.dashboard') }}" class="flex items-center gap-2.5 min-w-0">
                    <img src="{{ asset('images/logo-tanggap-terang.png') }}"
                         alt="Logo TanggapTerang"
                         class="h-9 sm:h-10 w-auto object-contain rounded-lg shrink-0">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                            <span class="font-bold text-sm sm:text-base tracking-tight text-white leading-tight">TanggapTerang</span>
                            <span class="px-1.5 py-0.5 rounded font-telemetry text-[9px] sm:text-[10px] font-bold uppercase leading-none shrink-0 {{ $roleBadgeClass }}">
                                {{ $roleLabel }}
                            </span>
                        </div>
                        <p class="hidden xl:block font-telemetry text-[10px] text-slate-400 uppercase tracking-wider truncate mt-0.5">
                            {{ $headerSubtitle }}
                        </p>
                    </div>
                </a>

                <nav class="hidden lg:flex items-center gap-1 ml-2 xl:ml-4 shrink-0" aria-label="Navigasi Internal">
                    <a href="{{ route('internal.dashboard') }}"
                       class="px-3 py-1.5 rounded font-telemetry text-xs font-bold uppercase tracking-wider transition {{ $internalMenu === 'dashboard' ? 'nav-active-gradient' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('internal.reports.index') }}"
                       class="px-3 py-1.5 rounded font-telemetry text-xs font-bold uppercase tracking-wider transition {{ $internalMenu === 'reports' ? 'nav-active-gradient' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        {{ $reportsMenuLabel }}
                    </a>
                    <a href="{{ route('internal.incidents.index') }}"
                       class="px-3 py-1.5 rounded font-telemetry text-xs font-bold uppercase tracking-wider transition {{ $internalMenu === 'incidents' ? 'nav-active-gradient' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        {{ $incidentsMenuLabel }}
                    </a>
                    @if($currentUser?->isAdmin())
                        <a href="{{ route('internal.intelligence.index') }}"
                           class="px-3 py-1.5 rounded font-telemetry text-xs font-bold uppercase tracking-wider transition {{ $internalMenu === 'intelligence' ? 'nav-active-gradient' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            Pusat Intelijen
                        </a>
                        <a href="{{ route('internal.users.index') }}"
                           class="px-3 py-1.5 rounded font-telemetry text-xs font-bold uppercase tracking-wider transition {{ $internalMenu === 'users' ? 'nav-active-gradient' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            Direktori Akun
                        </a>
                        <a href="{{ route('internal.audit-logs.index') }}"
                           class="px-3 py-1.5 rounded font-telemetry text-xs font-bold uppercase tracking-wider transition {{ $internalMenu === 'audit-logs' ? 'nav-active-gradient' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            Audit Log
                        </a>
                    @endif
                    @if($canOperate)
                        <a href="{{ route('internal.incidents.create') }}"
                           class="ml-1 px-2.5 py-1.5 rounded border border-emerald-500/70 bg-emerald-950/60 hover:bg-emerald-900 text-emerald-200 font-telemetry text-[11px] font-bold uppercase tracking-wider transition whitespace-nowrap">
                            + Buat Kejadian
                        </a>
                    @endif
                </nav>
            </div>

            <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                @auth
                    <a href="{{ route('internal.profile.show') }}"
                       class="hidden lg:flex flex-col items-end px-2.5 py-1 rounded hover:bg-slate-800 transition {{ $internalMenu === 'profile' ? 'ring-1 ring-amber-400 bg-slate-800' : '' }}"
                       title="Kelola Profil Akun">
                        <span class="text-xs font-bold text-white leading-none flex items-center gap-1">
                            <span>👤</span> {{ auth()->user()->name }}
                        </span>
                        <span class="font-telemetry text-[10px] text-amber-400 uppercase tracking-wider mt-0.5">
                            Role: {{ $roleLabel }}
                        </span>
                    </a>
                @endauth

                <a href="{{ route('public.monitor') }}"
                   class="px-2.5 sm:px-3 py-1.5 rounded border border-slate-600 text-slate-200 hover:bg-slate-800 font-telemetry text-[11px] sm:text-xs font-bold uppercase tracking-wider transition whitespace-nowrap">
                    <span class="hidden sm:inline">Halaman Publik</span>
                    <span class="sm:hidden">Publik</span>
                </a>

                @auth
                    <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                        @csrf
                        <button type="submit" class="px-2.5 sm:px-3 py-1.5 rounded bg-[#DC2626] hover:bg-red-700 text-white font-telemetry text-[11px] sm:text-xs font-bold uppercase tracking-wider transition cursor-pointer whitespace-nowrap">
                            Keluar
                        </button>
                    </form>
                @endauth
            </div>
        </div>

        <!-- Mobile & Tablet Sub-nav + Account Info Bar for Internal (< 1024px) -->
        <div class="lg:hidden border-t border-slate-800 px-3 sm:px-6 py-2 flex flex-wrap items-center justify-between gap-2">
            <nav class="flex flex-wrap items-center gap-1.5" aria-label="Navigasi Internal Mobile">
                <a href="{{ route('internal.dashboard') }}" class="px-2.5 py-1 rounded font-telemetry text-[11px] font-bold uppercase whitespace-nowrap {{ $internalMenu === 'dashboard' ? 'nav-active-gradient' : 'text-slate-300 hover:bg-slate-800' }}">Dashboard</a>
                <a href="{{ route('internal.reports.index') }}" class="px-2.5 py-1 rounded font-telemetry text-[11px] font-bold uppercase whitespace-nowrap {{ $internalMenu === 'reports' ? 'nav-active-gradient' : 'text-slate-300 hover:bg-slate-800' }}">{{ $reportsMenuLabel }}</a>
                <a href="{{ route('internal.incidents.index') }}" class="px-2.5 py-1 rounded font-telemetry text-[11px] font-bold uppercase whitespace-nowrap {{ $internalMenu === 'incidents' ? 'nav-active-gradient' : 'text-slate-300 hover:bg-slate-800' }}">{{ $incidentsMenuLabel }}</a>
                @if($currentUser?->isAdmin())
                    <a href="{{ route('internal.intelligence.index') }}" class="px-2.5 py-1 rounded font-telemetry text-[11px] font-bold uppercase whitespace-nowrap {{ $internalMenu === 'intelligence' ? 'nav-active-gradient' : 'text-slate-300 hover:bg-slate-800' }}">Intelijen</a>
                    <a href="{{ route('internal.users.index') }}" class="px-2.5 py-1 rounded font-telemetry text-[11px] font-bold uppercase whitespace-nowrap {{ $internalMenu === 'users' ? 'nav-active-gradient' : 'text-slate-300 hover:bg-slate-800' }}">Akun</a>
                    <a href="{{ route('internal.audit-logs.index') }}" class="px-2.5 py-1 rounded font-telemetry text-[11px] font-bold uppercase whitespace-nowrap {{ $internalMenu === 'audit-logs' ? 'nav-active-gradient' : 'text-slate-300 hover:bg-slate-800' }}">Audit Log</a>
                @endif
                <a href="{{ route('internal.profile.show') }}" class="px-2.5 py-1 rounded font-telemetry text-[11px] font-bold uppercase whitespace-nowrap {{ $internalMenu === 'profile' ? 'nav-active-gradient' : 'text-slate-300 hover:bg-slate-800' }}">Profil</a>
                @if($canOperate)
                    <a href="{{ route('internal.incidents.create') }}" class="px-2.5 py-1 rounded border border-emerald-500/70 bg-emerald-950/60 text-emerald-200 font-telemetry text-[11px] font-bold uppercase whitespace-nowrap">+ Buat Kejadian</a>
                @endif
            </nav>
            @auth
                <div class="flex items-center gap-1.5 font-telemetry text-[10px] sm:text-[11px] text-slate-300">
                    <span class="font-bold text-white">{{ auth()->user()->name }}</span>
                    <span>•</span>
                    <span class="text-amber-400 uppercase font-bold">Role: {{ $roleLabel }}</span>
                </div>
            @endauth
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-5">
        <x-simulation-banner :compact="true" />

        @if(session('success'))
            <div class="bg-[#ECFDF5] border-l-4 border-[#059669] border-y border-r border-[#6EE7B7] rounded-lg p-4 text-[#065F46] text-sm font-medium" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-[#FEF2F2] border-l-4 border-[#DC2626] border-y border-r border-[#FCA5A5] rounded-lg p-4 text-[#991B1B] text-sm font-medium" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-[#FEF2F2] border-l-4 border-[#DC2626] border-y border-r border-[#FCA5A5] rounded-lg p-4 text-[#991B1B] text-sm" role="alert">
                <p class="font-bold mb-1">Periksa kembali input Anda:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Internal Footer -->
    <x-internal-footer />

    @stack('scripts')
</body>
</html>
