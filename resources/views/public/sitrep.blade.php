@extends('layouts.app', [
    'title' => 'Laporan Ringkasan Situasi (SitRep) — TanggapTerang',
    'activeTab' => 'laporan',
])

@section('content')
    <!-- Interactive Filter & Print Toolbar (Hidden on Print) -->
    <section class="bg-white border border-[#E2E8F0] rounded-lg p-5 space-y-4 no-print">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-100 pb-4">
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-[#0F172A] text-white font-telemetry text-[10px] font-bold uppercase tracking-wider">
                        Dokumen Ringkasan Informasi (SitRep)
                    </span>
                    @if($selectedIncident)
                        @if(!$selectedIncident->is_simulation)
                            <span class="px-2 py-0.5 rounded bg-blue-900 text-white font-telemetry text-[10px] font-bold uppercase tracking-wider">
                                Data Kejadian Historis BNPB (DIBI)
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 border border-amber-300 font-telemetry text-[10px] font-bold uppercase tracking-wider">
                                Data Simulasi Prototype
                            </span>
                        @endif
                    @else
                        <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-900 border border-blue-300 font-telemetry text-[10px] font-bold uppercase tracking-wider">
                            Data Gabungan: {{ $stats['historical_incidents'] ?? 0 }} Historis BNPB • {{ $stats['simulation_incidents'] ?? 0 }} Simulasi
                        </span>
                    @endif
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-[#0F172A]">
                    Laporan Ringkasan Situasi Kejadian &amp; Tindak Lanjut
                </h1>
                <p class="text-sm text-slate-600">
                    Saring berdasarkan kejadian, wilayah, atau rentang waktu kejadian, lalu gunakan tombol <strong>Cetak / Simpan PDF</strong> untuk mencetak dokumen siap baca.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <button type="button"
                        onclick="if(window.TanggapToast) window.TanggapToast.info('Mempersiapkan dokumen cetak / dialog PDF...', 'Cetak SitRep'); window.print();"
                        data-tooltip="Cetak atau simpan dokumen SitRep sebagai PDF"
                        class="px-5 py-2.5 rounded-lg bg-[#DC2626] hover:bg-red-700 text-white font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[44px] inline-flex items-center gap-2 cursor-pointer shadow-xs active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak / Simpan PDF</span>
                </button>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('public.sitrep') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-3">
                <label for="sitrep-incident" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Cakupan Kejadian
                </label>
                <select id="sitrep-incident"
                        name="incident"
                        class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#0F172A]">
                    <option value="">Semua Kejadian Tercatat (Historis &amp; Simulasi)</option>
                    <optgroup label="Data Historis Resmi BNPB (DIBI)">
                        @foreach($allIncidentOptions->where('is_simulation', false) as $opt)
                            <option value="{{ $opt->slug }}" @selected($filters['incident'] === $opt->slug)>
                                [HISTORIS] {{ $opt->title }}
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Skenario Simulasi Prototipe">
                        @foreach($allIncidentOptions->where('is_simulation', true) as $opt)
                            <option value="{{ $opt->slug }}" @selected($filters['incident'] === $opt->slug)>
                                [SIMULASI] {{ $opt->title }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
            </div>

            <div class="sm:col-span-3">
                <label for="sitrep-wilayah" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Wilayah / Lokasi
                </label>
                <select id="sitrep-wilayah"
                        name="wilayah"
                        class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#0F172A]">
                    <option value="">Semua Wilayah</option>
                    @foreach($availableLocations as $loc)
                        <option value="{{ $loc }}" @selected($filters['wilayah'] === $loc)>{{ $loc }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <label for="sitrep-date-from" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Dari Tanggal
                </label>
                <input type="date"
                       id="sitrep-date-from"
                       name="date_from"
                       value="{{ $filters['date_from'] }}"
                       class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#0F172A]">
            </div>

            <div class="sm:col-span-2">
                <label for="sitrep-date-to" class="block font-telemetry text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Sampai Tanggal
                </label>
                <input type="date"
                       id="sitrep-date-to"
                       name="date_to"
                       value="{{ $filters['date_to'] }}"
                       class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#0F172A]">
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit"
                        class="w-full px-4 py-2.5 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white font-telemetry text-xs font-bold uppercase tracking-wider min-h-[42px] cursor-pointer">
                    Terapkan
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('public.sitrep') }}"
                       class="px-3 py-2.5 rounded-lg border border-[#CBD5E1] text-slate-700 font-telemetry text-xs font-bold uppercase min-h-[42px] inline-flex items-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </section>

    <!-- Printable SitRep Document Sheet -->
    <article class="bg-white border border-[#CBD5E1] rounded-lg p-6 sm:p-8 space-y-6 print-sheet">
        <!-- Document Institutional Header (Strictly TanggapTerang — NO fake BNPB/BPBD authorship) -->
        <header class="border-b-2 border-[#0F172A] pb-5 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="flex items-start gap-3.5 sm:gap-4">
                <img src="{{ asset('images/logo-tanggap-terang.png') }}"
                     alt="Logo TanggapTerang"
                     class="h-12 sm:h-14 w-auto object-contain shrink-0 mt-0.5">
                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2 py-0.5 rounded bg-[#0F172A] text-white font-telemetry text-[10px] font-bold uppercase tracking-wider">
                            TanggapTerang — Laporan Ringkasan Situasi (SitRep)
                        </span>
                        @if($selectedIncident)
                            @if(!$selectedIncident->is_simulation)
                                <span class="px-2 py-0.5 rounded bg-blue-900 text-white font-telemetry text-[10px] font-bold uppercase tracking-wider">
                                    DATA HISTORIS (BNPB / DIBI)
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-900 border border-amber-400 font-telemetry text-[10px] font-bold uppercase tracking-wider">
                                    DATA SIMULASI PROTOTYPE
                                </span>
                            @endif
                        @else
                            <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-900 border border-blue-300 font-telemetry text-[10px] font-bold uppercase tracking-wider">
                                DATA HISTORIS BNPB ({{ $stats['historical_incidents'] ?? 0 }}) &amp; SIMULASI ({{ $stats['simulation_incidents'] ?? 0 }})
                            </span>
                        @endif
                    </div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-[#0F172A] tracking-tight">
                        Ringkasan Informasi Kejadian &amp; Status Tindak Lanjut Tim TanggapTerang
                    </h2>
                    <p class="text-xs text-slate-600">
                        Dokumen ini merupakan ringkasan otomatis dari informasi kejadian, laporan masyarakat, dan sumber informasi terbuka yang tercatat di sistem prototype <strong>TanggapTerang</strong> (bukan dokumen resmi instansi pemerintah/BNPB/BPBD).
                    </p>
                </div>
            </div>

            <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-3 font-telemetry text-xs text-slate-700 shrink-0 space-y-1">
                <div>
                    <span class="text-[10px] uppercase text-slate-500 block">Waktu Pembuatan Laporan:</span>
                    <strong>{{ $generatedAt->format('d M Y, H:i') }} WIB</strong>
                </div>
                <div>
                    <span class="text-[10px] uppercase text-slate-500 block">Pembaruan Data Terakhir:</span>
                    <strong>{{ $lastUpdatedAt->format('d M Y, H:i') }} WIB</strong>
                </div>
                @if($filters['wilayah'] !== '')
                    <div>
                        <span class="text-[10px] uppercase text-slate-500 block">Filter Wilayah:</span>
                        <strong>{{ $filters['wilayah'] }}</strong>
                    </div>
                @endif
            </div>
        </header>

        <!-- Dual Summary Section: Verification Status Counts vs Follow-Up Status Counts -->
        <section class="grid grid-cols-1 md:grid-cols-2 gap-4 print-avoid-break">
            <!-- Box 1: Ringkasan Berdasarkan Status Verifikasi Informasi -->
            <div class="bg-[#F8FAFC] border border-[#CBD5E1] rounded-lg p-4 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                    <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                        1. Ringkasan Berdasarkan Status Verifikasi Informasi
                    </h3>
                    <span class="font-telemetry text-xs font-bold text-[#0F172A]">
                        Total: {{ $stats['total_incidents'] }} Kejadian ({{ $stats['historical_incidents'] ?? 0 }} Historis, {{ $stats['simulation_incidents'] ?? 0 }} Sim)
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-2.5 text-center">
                    <div class="bg-white border border-[#93C5FD] rounded p-2.5 card-interactive"
                         data-tooltip="Jumlah kejadian dengan validasi terverifikasi">
                        <span class="font-telemetry text-[10px] font-bold uppercase text-[#1E40AF] block">Terverifikasi</span>
                        <span class="font-telemetry text-2xl font-bold text-[#2563EB]">{{ $stats['terverifikasi'] }}</span>
                    </div>
                    <div class="bg-white border border-[#FCD34D] rounded p-2.5 card-interactive"
                         data-tooltip="Kejadian sedang dalam pemeriksaan tim verifikator">
                        <span class="font-telemetry text-[10px] font-bold uppercase text-[#92400E] block">Menunggu Verifikasi</span>
                        <span class="font-telemetry text-2xl font-bold text-[#D97706]">{{ $stats['menunggu_verifikasi'] }}</span>
                    </div>
                    <div class="bg-white border border-[#CBD5E1] rounded p-2.5 card-interactive"
                         data-tooltip="Total laporan masyarakat yang telah ditautkan ke kejadian">
                        <span class="font-telemetry text-[10px] font-bold uppercase text-slate-600 block">Laporan Terhubung</span>
                        <span class="font-telemetry text-2xl font-bold text-[#0F172A]">{{ $stats['connected_reports'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Box 2: Ringkasan Berdasarkan Status Tindak Lanjut Internal TanggapTerang -->
            <div class="bg-[#F8FAFC] border border-[#CBD5E1] rounded-lg p-4 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                    <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                        2. Ringkasan Status Tindak Lanjut Tim TanggapTerang
                    </h3>
                    <span class="font-telemetry text-xs font-bold text-[#0F172A]">
                        {{ $stats['connected_sources'] }} Sumber Terbuka
                    </span>
                </div>

                <div class="grid grid-cols-4 gap-2 text-center">
                    <div class="bg-white border border-slate-300 rounded p-2 card-interactive"
                         data-tooltip="Kejadian belum ada tindakan pemantauan lanjutan">
                        <span class="font-telemetry text-[9px] font-bold uppercase text-slate-600 block">Belum Ditindaklanjuti</span>
                        <span class="font-telemetry text-xl font-bold text-slate-800">{{ $stats['belum_ditindaklanjuti'] }}</span>
                    </div>
                    <div class="bg-white border border-[#FCD34D] rounded p-2 card-interactive"
                         data-tooltip="Kejadian sedang dalam koordinasi &amp; pemantauan aktif">
                        <span class="font-telemetry text-[9px] font-bold uppercase text-[#92400E] block">Sedang Ditindaklanjuti</span>
                        <span class="font-telemetry text-xl font-bold text-[#D97706]">{{ $stats['sedang_ditindaklanjuti'] }}</span>
                    </div>
                    <div class="bg-white border border-[#93C5FD] rounded p-2 card-interactive"
                         data-tooltip="Perkembangan terus dipantau secara berkala">
                        <span class="font-telemetry text-[9px] font-bold uppercase text-[#1E40AF] block">Dipantau</span>
                        <span class="font-telemetry text-xl font-bold text-[#2563EB]">{{ $stats['dipantau'] }}</span>
                    </div>
                    <div class="bg-white border border-[#6EE7B7] rounded p-2 card-interactive"
                         data-tooltip="Penanganan informasi kejadian dinyatakan selesai">
                        <span class="font-telemetry text-[9px] font-bold uppercase text-[#065F46] block">Selesai</span>
                        <span class="font-telemetry text-xl font-bold text-[#059669]">{{ $stats['selesai'] }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Detailed Incidents Table -->
        <section class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                    Daftar Rincian Kejadian yang Tercatat ({{ $incidents->count() }} Kejadian)
                </h3>
                <span class="font-telemetry text-[11px] text-slate-500">
                    Membedakan kejadian terverifikasi dan belum terverifikasi
                </span>
            </div>

            @if($incidents->isEmpty())
                <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-8 text-center space-y-2">
                    <h4 class="text-base font-bold text-[#0F172A]">Tidak Ada Data Kejadian pada Kriteria Ini</h4>
                    <p class="text-xs text-slate-600">
                        Belum terdapat kejadian yang sesuai dengan filter wilayah, kejadian, atau rentang tanggal yang dipilih.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto border border-[#CBD5E1] rounded-lg">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-[#0F172A] text-white font-telemetry text-[10px] font-bold uppercase tracking-wider">
                                <th class="py-3 px-3">No</th>
                                <th class="py-3 px-3">Kejadian &amp; Deskripsi Ringkas</th>
                                <th class="py-3 px-3">Lokasi Administratif</th>
                                <th class="py-3 px-3">Tingkat Dampak</th>
                                <th class="py-3 px-3">1. Status Verifikasi</th>
                                <th class="py-3 px-3">2. Status Tindak Lanjut Tim</th>
                                <th class="py-3 px-3">Sumber Informasi &amp; Pembaruan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($incidents as $idx => $incident)
                                <tr class="align-top print-avoid-break hover:bg-blue-50/40 transition-colors {{ $idx % 2 === 1 ? 'bg-slate-50/60' : 'bg-white' }}">
                                    <td class="py-3 px-3 font-telemetry font-bold text-[#0F172A]">
                                        {{ $idx + 1 }}
                                    </td>

                                    <td class="py-3 px-3 max-w-xs space-y-1">
                                        <div class="font-bold text-[#0F172A] text-sm">
                                            {{ $incident->title }}
                                        </div>
                                        <div class="font-telemetry text-[10px] uppercase text-slate-500">
                                            Jenis: {{ $categories[$incident->incident_type] ?? $incident->incident_type }}
                                            @if($incident->is_simulation)
                                                • <span class="text-slate-800 font-bold">[DATA SIMULASI]</span>
                                            @else
                                                • <span class="text-blue-800 font-bold">[DATA HISTORIS BNPB]</span>
                                            @endif
                                        </div>
                                        <p class="text-slate-600 leading-relaxed">
                                            {{ $incident->description }}
                                        </p>
                                    </td>

                                    <td class="py-3 px-3 space-y-1">
                                        <div class="font-semibold text-[#0F172A]">{{ $incident->location_name }}</div>
                                        @if($incident->wilayahAdministratif)
                                            <div class="font-telemetry text-[11px] text-slate-600">
                                                Wilayah: {{ $incident->wilayahAdministratif->nama }}
                                            </div>
                                        @endif
                                        @if($incident->latitude !== null && $incident->longitude !== null)
                                            <div class="font-telemetry text-[10px] text-slate-500">
                                                Koordinat: {{ number_format((float) $incident->latitude, 4) }}, {{ number_format((float) $incident->longitude, 4) }}
                                            </div>
                                        @else
                                            <div class="font-telemetry text-[10px] text-slate-400">
                                                Koordinat spesifik belum tersedia
                                            </div>
                                        @endif
                                    </td>

                                    <td class="py-3 px-3 whitespace-nowrap">
                                        <x-severity-badge :severity="$incident->severity" />
                                    </td>

                                    <td class="py-3 px-3 whitespace-nowrap">
                                        <x-verification-badge :status="$incident->verification_status" />
                                    </td>

                                    <td class="py-3 px-3 space-y-1.5">
                                        <div>
                                            <x-followup-badge :status="$incident->follow_up_status" />
                                        </div>
                                        @if($incident->followUpRecords->isNotEmpty())
                                            <div class="text-[11px] text-slate-600 bg-slate-100 rounded p-1.5">
                                                <strong class="block text-[#0F172A]">{{ $incident->followUpRecords->first()->action_title }}</strong>
                                                <span>{{ \Illuminate\Support\Str::limit($incident->followUpRecords->first()->notes, 90) }}</span>
                                            </div>
                                        @endif
                                    </td>

                                    <td class="py-3 px-3 space-y-1 font-telemetry text-[11px] text-slate-600">
                                        <div><strong>{{ $incident->reports_count }}</strong> Laporan Masyarakat</div>
                                        <div><strong>{{ $incident->aid_responses_count ?? 0 }}</strong> Respons Bantuan Tercatat</div>
                                        <div><strong>{{ $incident->incident_sources_count }}</strong> Sumber Terbuka:</div>
                                        @if($incident->incidentSources->isNotEmpty())
                                            <ul class="list-disc list-inside text-[10px] text-slate-600 space-y-0.5">
                                                @foreach($incident->incidentSources as $incSrc)
                                                    <li>{{ $incSrc->source?->name ?? $incSrc->headline }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        <div class="pt-1 text-[10px] text-slate-500 border-t border-slate-100">
                                            Waktu Kejadian: {{ $incident->occurred_at?->format('d M Y, H:i') }} WIB<br>
                                            Diperbarui: {{ $incident->updated_at?->format('d M Y, H:i') }} WIB
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <!-- Mandatory Data Limitations & Institutional Scope Footnote -->
        <footer class="bg-[#F8FAFC] border border-[#CBD5E1] rounded-lg p-4 space-y-2 text-xs text-slate-700 print-avoid-break">
            <div class="font-telemetry font-bold uppercase tracking-wider text-[#0F172A]">
                Catatan Keterbatasan Data &amp; Ruang Lingkup TanggapTerang
            </div>
            <ul class="list-disc list-inside space-y-1 leading-relaxed">
                <li>
                    <strong>Sumber Data:</strong> Laporan ini merupakan ringkasan dari informasi kejadian yang tercatat pada sistem prototype <strong>TanggapTerang</strong> melalui laporan masyarakat dan sumber informasi terbuka.
                </li>
                <li>
                    <strong>Pemisahan Verifikasi dan Tindak Lanjut:</strong> Kejadian dengan status <em>Menunggu Verifikasi</em> masih dalam tahap pemeriksaan silang dan belum dinyatakan sebagai informasi terverifikasi.
                </li>
                <li>
                    <strong>Makna Status Tindak Lanjut:</strong> Status tindak lanjut pada dokumen ini mencatat aktivitas verifikasi, pemantauan informasi, dan dokumentasi oleh tim internal TanggapTerang. <strong>Ketiadaan catatan tindak lanjut pada TanggapTerang tidak berarti instansi berwenang eksternal tidak sedang menangani kejadian di lapangan.</strong>
                </li>
                <li>
                    <strong>Penanda Simulasi:</strong> Seluruh data yang tercantum dalam laporan prototype ini merupakan <strong>data simulasi</strong> untuk demonstrasi kompetisi <em>Multimedia In Action 2026</em>. Sistem tidak mengarang angka korban, pengungsi, maupun stok logistik di luar data laporan yang tercatat.
                </li>
            </ul>
        </footer>
    </article>
@endsection
