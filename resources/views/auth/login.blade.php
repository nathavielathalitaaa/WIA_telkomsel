@extends('layouts.app', [
    'title' => 'Login Tim Internal — TanggapTerang',
    'activeTab' => '',
])

@section('content')
    <div class="max-w-4xl mx-auto py-4 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left Column: Login Card (7 cols) -->
        <section class="lg:col-span-7 bg-white rounded-2xl shadow-xs border border-slate-100/90 p-6 sm:p-8 space-y-6">
            <div class="border-b border-slate-100 pb-4 space-y-3">
                <div class="flex items-center gap-3.5">
                    <img src="{{ asset('images/logo-tanggap-terang.png') }}"
                         alt="Logo TanggapTerang"
                         class="h-14 sm:h-16 w-auto object-contain shrink-0">
                    <div class="space-y-1.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2 py-0.5 rounded bg-[#0F172A] text-white font-telemetry text-[10px] font-bold uppercase tracking-wider">
                                Akses Terbatas Tim Internal
                            </span>
                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-telemetry text-[10px] font-bold uppercase tracking-wider">
                                Admin • Verifikator • Operator
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-[#0F172A]">
                            Masuk ke Panel Pengelolaan TanggapTerang
                        </h1>
                    </div>
                </div>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Halaman ini khusus untuk tim internal TanggapTerang yang bertugas memverifikasi laporan masyarakat, menautkan sumber informasi terbuka, dan mencatat perkembangan tindak lanjut.
                </p>
            </div>

            @if($errors->any())
                <div class="bg-[#FEF2F2] border-l-4 border-[#DC2626] border-y border-r border-[#FCA5A5] rounded-lg p-4 text-xs text-[#991B1B] space-y-1" role="alert">
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
                    <label for="email" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] mb-1">
                        Email Petugas Internal
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="username"
                           placeholder="nama.petugas@tanggap-terang.id"
                           class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('email') ? 'border-[#DC2626]' : 'border-[#CBD5E1]' }} rounded-lg text-sm text-[#0F172A] focus:outline-none focus:border-[#0F172A]">
                    @error('email')
                        <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] mb-1">
                        Kata Sandi
                    </label>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           autocomplete="current-password"
                           placeholder="Masukkan kata sandi akun internal"
                           class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('password') ? 'border-[#DC2626]' : 'border-[#CBD5E1]' }} rounded-lg text-sm text-[#0F172A] focus:outline-none focus:border-[#0F172A]">
                    @error('password')
                        <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <a href="{{ route('public.monitor') }}"
                       class="font-telemetry text-xs font-bold text-slate-600 hover:text-[#0F172A] inline-flex items-center gap-1">
                        <span>&larr; Kembali ke Halaman Publik</span>
                    </a>

                    <button type="submit"
                            class="px-6 py-2.5 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[44px] inline-flex items-center justify-center gap-2 cursor-pointer">
                        <span>Masuk ke Panel Internal</span>
                        <span>&rarr;</span>
                    </button>
                </div>
            </form>
        </section>

        <!-- Right Column: Role Information & Public Access Notice (5 cols) -->
        <aside class="lg:col-span-5 space-y-4">
            <div class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 space-y-3">
                <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                    Pembagian Peran Tim Internal
                </h2>
                <div class="space-y-2.5 text-xs text-slate-600">
                    <div class="p-3 rounded-xl bg-[#F8FAFC] border border-slate-100">
                        <strong class="font-telemetry uppercase text-[#2563EB] block">1. Verifikator</strong>
                        Memeriksa laporan masyarakat yang masuk, memverifikasi, menolak dengan alasan, atau menandai laporan duplikat, serta memperbarui status verifikasi kejadian.
                    </div>
                    <div class="p-3 rounded-xl bg-[#F8FAFC] border border-slate-100">
                        <strong class="font-telemetry uppercase text-[#059669] block">2. Operator</strong>
                        Membuat dan memperbarui data kejadian, menghubungkan laporan ke kejadian, menambahkan sumber informasi terbuka, serta mencatat tindak lanjut.
                    </div>
                    <div class="p-3 rounded-xl bg-[#F8FAFC] border border-slate-100">
                        <strong class="font-telemetry uppercase text-[#0F172A] block">3. Administrator</strong>
                        Memiliki kewenangan penuh atas pengelolaan verifikasi laporan maupun pengelolaan kejadian dan tindak lanjut.
                    </div>
                </div>
            </div>

            <div class="bg-[#EFF6FF] border border-[#93C5FD] rounded-lg p-4 space-y-2 text-xs text-[#1E40AF]">
                <div class="font-telemetry font-bold uppercase tracking-wider">
                    Masyarakat Umum Tidak Perlu Login
                </div>
                <p class="leading-relaxed">
                    Untuk melihat peta kejadian, mengirimkan laporan baru, atau melacak status nomor tiket laporan, masyarakat dapat langsung mengakses halaman publik tanpa akun.
                </p>
                <div class="pt-1 flex flex-wrap gap-2">
                    <a href="{{ route('public.reports.create') }}"
                       class="px-3 py-1.5 rounded btn-brand-gradient font-telemetry text-[11px] font-bold uppercase">
                        + Lapor Kejadian Tanpa Login
                    </a>
                    <a href="{{ route('public.track') }}"
                       class="px-3 py-1.5 rounded bg-white border border-[#93C5FD] text-[#1E40AF] font-telemetry text-[11px] font-bold uppercase">
                        Lacak Tiket
                    </a>
                </div>
            </div>
        </aside>
    </div>
@endsection
