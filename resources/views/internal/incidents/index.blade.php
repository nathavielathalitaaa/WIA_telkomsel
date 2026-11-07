@extends('layouts.internal', [
    'title' => 'Manajemen Data Kejadian — TanggapTerang Internal',
    'internalMenu' => 'incidents',
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
                    Manajemen Kejadian
                </span>
                <span class="font-telemetry text-xs text-slate-500">
                    Total: {{ $incidents->total() }} Kejadian
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#0F172A]">
                Daftar &amp; Pengelolaan Informasi Kejadian
            </h1>
            <p class="text-sm text-slate-600">
                Kelola informasi kejadian, pisahkan status verifikasi informasi dengan status tindak lanjut tim, tambahkan sumber terbuka, dan hubungkan laporan warga.
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            @if($user->canManageOperations())
                <a href="{{ route('internal.incidents.create') }}"
                   class="px-4 py-2.5 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[42px] inline-flex items-center gap-1.5">
                    <span>+ Buat Kejadian Baru</span>
                </a>
            @endif
        </div>
    </section>

    <!-- Search & Filter Bar -->
    <section class="bg-white border border-[#E2E8F0] rounded-lg p-4">
        <form method="GET" action="{{ route('internal.incidents.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-4">
                <label for="q" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Cari Judul / Lokasi Kejadian
                </label>
                <input type="text"
                       id="q"
                       name="q"
                       value="{{ $filters['q'] }}"
                       placeholder="Nama kejadian atau lokasi..."
                       class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A]">
            </div>

            <div class="sm:col-span-2">
                <label for="verification_status" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Status Verifikasi
                </label>
                <select id="verification_status"
                        name="verification_status"
                        class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A]">
                    <option value="">Semua Verifikasi</option>
                    @foreach(\App\Enums\VerificationStatus::cases() as $vCase)
                        <option value="{{ $vCase->value }}" @selected($filters['verification_status'] === $vCase->value)>
                            {{ $vCase->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <label for="follow_up_status" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Status Tindak Lanjut
                </label>
                <select id="follow_up_status"
                        name="follow_up_status"
                        class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A]">
                    <option value="">Semua Tindak Lanjut</option>
                    @foreach(\App\Enums\FollowUpStatus::cases() as $fCase)
                        <option value="{{ $fCase->value }}" @selected($filters['follow_up_status'] === $fCase->value)>
                            {{ $fCase->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <label for="severity" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Tingkat Dampak
                </label>
                <select id="severity"
                        name="severity"
                        class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A]">
                    <option value="">Semua Dampak</option>
                    @foreach(\App\Enums\IncidentSeverity::cases() as $sCase)
                        <option value="{{ $sCase->value }}" @selected($filters['severity'] === $sCase->value)>
                            {{ $sCase->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit"
                        class="w-full px-4 py-2.5 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white font-telemetry text-xs font-bold uppercase tracking-wider min-h-[42px] cursor-pointer">
                    Filter
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('internal.incidents.index') }}"
                       class="px-3 py-2.5 rounded-lg border border-[#CBD5E1] text-slate-700 font-telemetry text-xs font-bold uppercase min-h-[42px] inline-flex items-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </section>

    <!-- Scannable Incidents Table -->
    <section class="bg-white border border-[#E2E8F0] rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600">
                        <th class="py-3.5 px-4">Kejadian &amp; Lokasi</th>
                        <th class="py-3.5 px-4">Dampak &amp; Waktu</th>
                        <th class="py-3.5 px-4">1. Status Verifikasi</th>
                        <th class="py-3.5 px-4">2. Status Tindak Lanjut</th>
                        <th class="py-3.5 px-4">Laporan &amp; Sumber</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($incidents as $incident)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 align-top max-w-md">
                                <div class="flex items-center gap-1.5 mb-1">
                                    @if($incident->is_simulation)
                                        <span class="px-1.5 py-0.5 rounded bg-slate-900 text-white font-telemetry text-[10px] font-bold uppercase">
                                            Simulasi
                                        </span>
                                    @endif
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-telemetry text-[10px] font-bold uppercase">
                                        {{ $categories[$incident->incident_type] ?? $incident->incident_type }}
                                    </span>
                                </div>
                                <div class="font-bold text-[#0F172A]">
                                    <a href="{{ route('internal.incidents.edit', $incident) }}" class="hover:text-[#2563EB]">
                                        {{ $incident->title }}
                                    </a>
                                </div>
                                <div class="text-xs text-slate-600 mt-0.5">
                                    {{ $incident->location_name }}
                                    @if($incident->wilayahAdministratif)
                                        • {{ $incident->wilayahAdministratif->nama }}
                                    @endif
                                </div>
                            </td>

                            <td class="py-3.5 px-4 align-top whitespace-nowrap">
                                <x-severity-badge :severity="$incident->severity" />
                                <div class="font-telemetry text-[11px] text-slate-500 mt-1">
                                    {{ $incident->occurred_at?->format('d M Y, H:i') }} WIB
                                </div>
                            </td>

                            <td class="py-3.5 px-4 align-top whitespace-nowrap">
                                <x-verification-badge :status="$incident->verification_status" />
                            </td>

                            <td class="py-3.5 px-4 align-top whitespace-nowrap">
                                <x-followup-badge :status="$incident->follow_up_status" />
                            </td>

                            <td class="py-3.5 px-4 align-top font-telemetry text-xs text-slate-600 whitespace-nowrap">
                                <div>{{ $incident->reports_count }} Laporan Warga</div>
                                <div>{{ $incident->sources_count }} Sumber Terbuka</div>
                                <div>{{ $incident->follow_up_records_count }} Catatan Tindak Lanjut</div>
                            </td>

                            <td class="py-3.5 px-4 align-top text-right whitespace-nowrap space-x-1.5">
                                <a href="{{ route('public.incidents.show', $incident->slug) }}"
                                   target="_blank"
                                   class="px-2.5 py-1.5 rounded border border-slate-300 text-slate-700 hover:bg-slate-100 font-telemetry text-xs font-bold uppercase inline-flex items-center">
                                    Publik &nearr;
                                </a>
                                <a href="{{ route('internal.incidents.edit', $incident) }}"
                                   class="px-3 py-1.5 rounded bg-[#0F172A] hover:bg-slate-800 text-white font-telemetry text-xs font-bold uppercase inline-flex items-center gap-1">
                                    <span>Kelola</span>
                                    <span>&rarr;</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 px-4 text-center text-sm text-slate-500">
                                Tidak ada data kejadian yang sesuai dengan kriteria pencarian/filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($incidents->hasPages())
            <div class="p-4 border-t border-[#E2E8F0]">
                {{ $incidents->links() }}
            </div>
        @endif
    </section>
@endsection
