@extends('layouts.internal', [
    'title' => 'Manajemen Laporan Masyarakat — TanggapTerang Internal',
    'internalMenu' => 'reports',
])

@php
    $user = auth()->user();
@endphp

@section('content')
    <!-- Page Header -->
    <section class="bg-white border border-[#E2E8F0] rounded-lg p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded bg-[#0F172A] text-white font-telemetry text-[10px] font-bold uppercase tracking-wider">
                    Manajemen Laporan Warga
                </span>
                <span class="font-telemetry text-xs text-slate-500">
                    Total: {{ $reports->total() }} Laporan
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#0F172A]">
                Antrean &amp; Pengelolaan Laporan Masyarakat
            </h1>
            <p class="text-sm text-slate-600">
                Tinjau laporan masuk, lakukan verifikasi atau penolakan dengan alasan, tandai duplikat, dan hubungkan laporan ke kejadian yang relevan.
            </p>
        </div>

        <a href="{{ route('internal.dashboard') }}"
           class="px-3.5 py-2 rounded-lg border border-[#CBD5E1] bg-white hover:bg-slate-50 text-[#0F172A] font-telemetry text-xs font-bold uppercase tracking-wider shrink-0">
            &larr; Kembali ke Dashboard
        </a>
    </section>

    <!-- Search & Filter Bar -->
    <section class="bg-white border border-[#E2E8F0] rounded-lg p-4">
        <form method="GET" action="{{ route('internal.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-5">
                <label for="q" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Cari Nomor Tiket / Judul / Lokasi
                </label>
                <input type="text"
                       id="q"
                       name="q"
                       value="{{ $filters['q'] }}"
                       placeholder="Contoh: RPT-SIM-8K4M9P2X atau judul laporan..."
                       class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A]">
            </div>

            <div class="sm:col-span-3">
                <label for="verification_status" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Status Verifikasi
                </label>
                <select id="verification_status"
                        name="verification_status"
                        class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A]">
                    <option value="">Semua Status Verifikasi</option>
                    @foreach(\App\Enums\VerificationStatus::cases() as $vCase)
                        <option value="{{ $vCase->value }}" @selected($filters['verification_status'] === $vCase->value)>
                            {{ $vCase->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <label for="link_state" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Keterhubungan
                </label>
                <select id="link_state"
                        name="link_state"
                        class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A]">
                    <option value="">Semua</option>
                    <option value="unlinked" @selected($filters['link_state'] === 'unlinked')>Belum Terhubung</option>
                    <option value="linked" @selected($filters['link_state'] === 'linked')>Sudah Terhubung</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit"
                        class="w-full px-4 py-2.5 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white font-telemetry text-xs font-bold uppercase tracking-wider min-h-[42px] cursor-pointer">
                    Filter
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('internal.reports.index') }}"
                       class="px-3 py-2.5 rounded-lg border border-[#CBD5E1] text-slate-700 font-telemetry text-xs font-bold uppercase min-h-[42px] inline-flex items-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </section>

    <!-- Scannable Reports Table -->
    <section class="bg-white border border-[#E2E8F0] rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600">
                        <th class="py-3.5 px-4">Tiket &amp; Waktu</th>
                        <th class="py-3.5 px-4">Judul &amp; Lokasi Laporan</th>
                        <th class="py-3.5 px-4">Pelapor (Internal)</th>
                        <th class="py-3.5 px-4">Status Verifikasi</th>
                        <th class="py-3.5 px-4">Kejadian Terhubung</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($reports as $report)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 align-top whitespace-nowrap">
                                <a href="{{ route('internal.reports.show', $report) }}"
                                   class="px-2 py-0.5 rounded bg-[#0F172A] text-white font-telemetry text-xs font-bold inline-block">
                                    {{ $report->ticket_number }}
                                </a>
                                @if($report->is_simulation)
                                    <span class="block mt-1 font-telemetry text-[10px] font-bold uppercase text-amber-700">
                                        [Simulasi]
                                    </span>
                                @endif
                                <div class="font-telemetry text-[11px] text-slate-500 mt-1">
                                    {{ $report->created_at?->format('d M Y, H:i') }}
                                </div>
                            </td>

                            <td class="py-3.5 px-4 align-top max-w-md">
                                <div class="font-bold text-[#0F172A]">
                                    <a href="{{ route('internal.reports.show', $report) }}" class="hover:text-[#2563EB]">
                                        {{ $report->title }}
                                    </a>
                                </div>
                                <div class="text-xs text-slate-600 mt-0.5">
                                    {{ $report->location_name }}
                                </div>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-telemetry text-[10px] font-bold uppercase">
                                    {{ $categories[$report->category] ?? $report->category }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 align-top text-xs">
                                <div class="font-semibold text-[#0F172A]">
                                    {{ $report->reporter_name ?: 'Anonim (Tanpa Nama)' }}
                                </div>
                                <div class="font-telemetry text-slate-500">
                                    {{ $report->reporter_contact ?: 'Tanpa Kontak' }}
                                </div>
                            </td>

                            <td class="py-3.5 px-4 align-top whitespace-nowrap">
                                <x-verification-badge :status="$report->verification_status" />
                                @if($report->verifier)
                                    <div class="font-telemetry text-[10px] text-slate-500 mt-1">
                                        Oleh: {{ $report->verifier->name }}
                                    </div>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 align-top text-xs">
                                @if($report->incident)
                                    <a href="{{ route('internal.incidents.edit', $report->incident) }}"
                                       class="font-bold text-[#2563EB] hover:underline block">
                                        {{ $report->incident->title }}
                                    </a>
                                    <div class="mt-1">
                                        <x-followup-badge :status="$report->incident->follow_up_status" />
                                    </div>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200 font-telemetry text-[10px] font-bold uppercase">
                                        Belum Terhubung
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 align-top text-right whitespace-nowrap">
                                <a href="{{ route('internal.reports.show', $report) }}"
                                   class="px-3 py-1.5 rounded bg-[#0F172A] hover:bg-slate-800 text-white font-telemetry text-xs font-bold uppercase tracking-wider inline-flex items-center gap-1">
                                    <span>Kelola</span>
                                    <span>&rarr;</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 px-4 text-center text-sm text-slate-500">
                                Tidak ada laporan masyarakat yang sesuai dengan kriteria pencarian/filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="p-4 border-t border-[#E2E8F0]">
                {{ $reports->links() }}
            </div>
        @endif
    </section>
@endsection
