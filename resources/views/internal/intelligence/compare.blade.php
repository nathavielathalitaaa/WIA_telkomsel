@extends('layouts.internal', [
    'title' => 'Perbandingan Sumber Data — Pusat Intelijen TanggapTerang',
    'internalMenu' => 'intelligence',
])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 sm:p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('internal.intelligence.index') }}" class="link-text text-xs font-telemetry">
                        Kembali ke Pusat Intelijen
                    </a>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold font-telemetry text-[#202938] tracking-tight">
                    Perbandingan Sumber Data & Analisis Disparitas
                </h1>
                <p class="text-sm text-slate-600 mt-1 max-w-3xl">
                    Bandingkan parameter hidrometeorologi dan laporan kebencanaan antara instrumen Dalam Negeri dan Internasional untuk mendeteksi deviasi model dan disparitas data lapangan.
                </p>
            </div>
            <a href="#analisis-admin"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-[#173B70] hover:bg-[#0F2A52] text-white font-telemetry text-xs font-bold uppercase tracking-wider transition shrink-0">
                + Tulis Catatan Analisis Admin
            </a>
        </div>
    </div>

    <!-- Methodology Warning Note -->
    <div class="bg-blue-50/70 border-l-4 border-blue-600 p-4 rounded-r-xl border-y border-r border-blue-200 text-xs text-blue-900 space-y-1">
        <div class="font-bold flex items-center gap-1.5">
            <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Panduan Integritas Metodologi Analisis Intelijen:
        </div>
        <p>
            Perbedaan hasil observasi atau prakiraan antarsumber tidak serta-merta menandakan kesalahan salah satu pihak. Disparitas dapat timbul dari perbedaan resolusi spasial model numerik (mis. WRF regional BMKG vs GFS/ECMWF global Open-Meteo), waktu siklus asimilasi data, sensor permukaan, atau batas administrasi. Gunakan tabel komparasi ini sebagai rujukan telaah, bukan keputusan otomatis.
        </p>
    </div>

    <!-- Section 1: Perbandingan Prakiraan Cuaca (BMKG vs Open-Meteo) -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-100/90 overflow-hidden">
        <div class="p-4 sm:p-5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase tracking-wider bg-cyan-100 text-cyan-800 border border-cyan-200">
                    Kasus Komparasi 1
                </span>
                <h2 class="text-base font-bold font-telemetry text-[#202938] mt-1">
                    Prakiraan Cuaca: BMKG (Nasional) vs Open-Meteo (Global)
                </h2>
                <p class="text-xs text-slate-500">
                    Titik acuan: Gambir, DKI Jakarta Pusat (-6.1764, 106.8267) •
                    @if($weatherComparison['has_match'] ?? false)
                        Slot Waktu Setara: <span class="font-bold text-slate-700">{{ $weatherComparison['valid_time_wib'] }}</span> ({{ $weatherComparison['valid_time_utc'] }})
                    @else
                        {{ $weatherComparison['location'] ?? 'DKI Jakarta' }}
                    @endif
                </p>
            </div>
            @if(! ($weatherComparison['has_data'] ?? false))
                <div class="text-xs text-amber-700 bg-amber-50 px-2.5 py-1 rounded border border-amber-200">
                    Data belum tersedia. Silakan tarik data BMKG Cuaca & Open-Meteo terlebih dahulu.
                </div>
            @elseif(! ($weatherComparison['has_match'] ?? false))
                <div class="text-xs text-amber-700 bg-amber-50 px-2.5 py-1 rounded border border-amber-200">
                    {{ $weatherComparison['status_message'] ?? 'Tidak ditemukan slot waktu yang setara.' }}
                </div>
            @endif
        </div>

        @if(!empty($weatherComparison['nature']))
            <div class="bg-amber-50/50 border-b border-amber-200/60 px-4 py-2 text-[11px] text-amber-900 flex items-center gap-1.5 font-medium">
                <span class="font-bold uppercase tracking-wider text-[10px] bg-amber-200/70 text-amber-900 px-1.5 py-0.5 rounded">Klasifikasi</span>
                {{ $weatherComparison['nature'] }}
            </div>
        @endif

        @if(($weatherComparison['has_data'] ?? false) && ($weatherComparison['has_match'] ?? false))
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-100/70 text-slate-500 font-telemetry uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 w-1/5">Parameter</th>
                            <th class="px-4 py-3 w-1/4 text-blue-900 font-bold bg-blue-50/40">BMKG (Prakiraan Wilayah)</th>
                            <th class="px-4 py-3 w-1/4 text-purple-900 font-bold bg-purple-50/40">Open-Meteo (Model Numerik)</th>
                            <th class="px-4 py-3">Disparitas & Catatan Metodologi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($weatherComparison['differences'] as $diff)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-4 py-3.5 font-bold font-telemetry text-[#202938]">
                                    {{ $diff['parameter'] }}
                                </td>
                                <td class="px-4 py-3.5 bg-blue-50/20 font-telemetry font-bold text-blue-950">
                                    {{ $diff['domestic'] }}
                                </td>
                                <td class="px-4 py-3.5 bg-purple-50/20 font-telemetry font-bold text-purple-950">
                                    {{ $diff['international'] }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-slate-800 font-telemetry text-[11px] mb-0.5">
                                        @if($diff['comparable'] && isset($diff['difference']))
                                            <span class="{{ abs($diff['difference']) > 3 ? 'text-amber-700' : 'text-slate-700' }}">
                                                Selisih {{ $diff['difference'] > 0 ? '+'.$diff['difference'] : $diff['difference'] }} {{ $diff['unit'] ?? '' }}
                                            </span>
                                        @else
                                            <span class="text-slate-500 font-normal italic">Selisih numerik tidak dihitung</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-500">
                                        {{ $diff['note'] }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(!empty($weatherComparison['matched_slots']))
                <div class="p-4 bg-slate-50/80 border-t border-slate-200">
                    <h4 class="font-bold font-telemetry text-xs uppercase tracking-wider text-slate-700 mb-2">
                        Slot Waktu Identik Lainnya yang Tersedia (8 Jam Terakhir / Mendatang)
                    </h4>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-[11px]">
                        @foreach($weatherComparison['matched_slots'] as $matchedSlot)
                            <div class="bg-white p-2 rounded border border-slate-200">
                                <span class="font-bold font-telemetry text-slate-800 block text-[10px]">{{ $matchedSlot['time_wib'] }} WIB</span>
                                <div class="text-slate-600 mt-0.5">
                                    <span class="text-blue-700 font-medium">BMKG:</span> {{ $matchedSlot['bmkg_temp'] }}
                                    <span class="text-slate-400">|</span>
                                    <span class="text-purple-700 font-medium">OM:</span> {{ $matchedSlot['om_temp'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <div class="p-8 text-center text-slate-500 text-xs">
                {{ $weatherComparison['status_message'] ?? 'Belum ada data cuaca yang tersimpan untuk dianalisis secara komparatif.' }}
            </div>
        @endif
    </div>

    <!-- Section 2: Perbandingan Gempa Bumi & Peringatan Dini -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- BMKG TEWS -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-100/90 overflow-hidden">
            <div class="p-4 bg-blue-50/60 border-b border-blue-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold font-telemetry text-sm text-blue-950">BMKG TEWS Gempa Terkini (Domestik)</h3>
                    <p class="text-[11px] text-blue-700">Jaringan sensor seismik akselerometer & broadband BMKG</p>
                </div>
                <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase bg-blue-100 text-blue-800 border border-blue-200">
                    Dalam Negeri
                </span>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($bmkgQuakes as $eq)
                    <div class="p-3.5 hover:bg-slate-50 transition">
                        <div class="flex justify-between items-start gap-2">
                            <span class="font-bold text-xs text-[#202938] leading-snug">{{ $eq->title }}</span>
                            <span class="text-[10px] font-telemetry text-slate-400 whitespace-nowrap">{{ $eq->occurred_at?->format('d/m H:i') }}</span>
                        </div>
                        <div class="text-[11px] text-slate-500 mt-1">{{ $eq->summary }}</div>
                        <div class="text-[10px] font-mono text-slate-400 mt-1">Koordinat: {{ $eq->latitude }}, {{ $eq->longitude }}</div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">Belum ada rekaman gempa BMKG tersimpan.</div>
                @endforelse
            </div>
        </div>

        <!-- GDACS Alerts -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-100/90 overflow-hidden">
            <div class="p-4 bg-purple-50/60 border-b border-purple-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold font-telemetry text-sm text-purple-950">GDACS Global Disaster Alerts (Internasional)</h3>
                    <p class="text-[11px] text-purple-700">Koordinasi peringatan bencana multi-bahaya PBB & Uni Eropa</p>
                </div>
                <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase bg-purple-100 text-purple-800 border border-purple-200">
                    Internasional
                </span>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($gdacsQuakes as $al)
                    <div class="p-3.5 hover:bg-slate-50 transition">
                        <div class="flex justify-between items-start gap-2">
                            <span class="font-bold text-xs text-[#202938] leading-snug">{{ $al->title }}</span>
                            <span class="text-[10px] font-telemetry text-slate-400 whitespace-nowrap">{{ $al->occurred_at?->format('d/m H:i') }}</span>
                        </div>
                        <div class="text-[11px] text-slate-500 mt-1 line-clamp-2">{{ $al->summary }}</div>
                        <div class="text-[10px] font-mono text-slate-400 mt-1">Koordinat: {{ $al->latitude ?? '-' }}, {{ $al->longitude ?? '-' }}</div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">Belum ada rekaman peringatan GDACS tersimpan.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Section 3: Laporan Lapangan vs Indikator Debit Sungai -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- PetaBencana -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-100/90 overflow-hidden">
            <div class="p-4 bg-amber-50/60 border-b border-amber-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold font-telemetry text-sm text-amber-950">PetaBencana Laporan Warga Lapangan</h3>
                    <p class="text-[11px] text-amber-800">Laporan terkonfirmasi masyarakat berbasis titik koordinat</p>
                </div>
                <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase bg-amber-100 text-amber-800 border border-amber-200">
                    Laporan Lapangan
                </span>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($petaBencanaReports as $pb)
                    <div class="p-3.5 hover:bg-slate-50 transition">
                        <div class="flex justify-between items-start gap-2">
                            <span class="font-bold text-xs text-[#202938]">{{ $pb->title }}</span>
                            <span class="text-[10px] font-telemetry text-slate-400 whitespace-nowrap">{{ $pb->occurred_at?->format('d/m H:i') }}</span>
                        </div>
                        <div class="text-[11px] text-slate-500 mt-0.5">{{ $pb->summary }}</div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">Belum ada rekaman laporan PetaBencana tersimpan.</div>
                @endforelse
            </div>
        </div>

        <!-- Open-Meteo Flood -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-100/90 overflow-hidden">
            <div class="p-4 bg-teal-50/60 border-b border-teal-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold font-telemetry text-sm text-teal-950">Open-Meteo Global River Discharge (Hidrologi)</h3>
                    <p class="text-[11px] text-teal-800">Model estimasi aliran debit sungai (m³/s) — Bukan bukti banjir</p>
                </div>
                <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase bg-teal-100 text-teal-800 border border-teal-200">
                    Indikator Aliran
                </span>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($floodDischarges as $fd)
                    <div class="p-3.5 hover:bg-slate-50 transition">
                        <div class="flex justify-between items-start gap-2">
                            <span class="font-bold text-xs text-[#202938]">{{ $fd->title }}</span>
                            <span class="text-[10px] font-telemetry text-slate-400 whitespace-nowrap">{{ $fd->occurred_at?->format('d M Y') }}</span>
                        </div>
                        <div class="text-[11px] text-slate-500 mt-0.5">{{ $fd->summary }}</div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">Belum ada rekaman hidrologi tersimpan.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Section 4: Form Catatan Analisis Admin -->
    <div id="analisis-admin" class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 sm:p-6">
        <h3 class="text-base font-bold font-telemetry text-[#202938] mb-1">
            Form Dokumentasi Analisis Perbandingan Intelijen
        </h3>
        <p class="text-xs text-slate-500 mb-5">
            Admin dapat membukukan kesimpulan telaah perbedaan data antara instrumen nasional dan global untuk dokumentasi operasional tim verifikasi.
        </p>

        <form action="{{ route('internal.intelligence.comparisons.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase font-telemetry text-slate-700 mb-1">
                        Judul Sesi Analisis <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" required
                           placeholder="Contoh: Analisis Disparitas Prakiraan Hujan Lebat Jakarta Periode 08-10 Nov"
                           class="w-full text-xs rounded-lg border border-slate-300 py-2 px-3 bg-white text-[#202938]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase font-telemetry text-slate-700 mb-1">
                        Topik Komparasi <span class="text-red-500">*</span>
                    </label>
                    <select name="comparison_topic" required class="w-full text-xs rounded-lg border border-slate-300 py-2 px-3 bg-white text-[#202938]">
                        <option value="cuaca">Prakiraan Cuaca & Presipitasi Hujan</option>
                        <option value="gempa">Gempa Bumi & Potensi Tsunami</option>
                        <option value="hidrologi">Debit Aliran Sungai & Laporan Banjir</option>
                        <option value="umum">Umum / Berita Bencana</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase font-telemetry text-slate-700 mb-1">
                        Sumber Domestik / Nasional <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="domestic_source" required value="BMKG Prakiraan Cuaca Wilayah"
                           class="w-full text-xs rounded-lg border border-slate-300 py-2 px-3 bg-white text-[#202938]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase font-telemetry text-slate-700 mb-1">
                        Sumber Internasional / Global <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="international_source" required value="Open-Meteo Global Forecast"
                           class="w-full text-xs rounded-lg border border-slate-300 py-2 px-3 bg-white text-[#202938]">
                </div>

                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase font-telemetry text-slate-700 mb-1">
                            Ringkasan Klaim Sumber Domestik <span class="text-red-500">*</span>
                        </label>
                        <textarea name="domestic_summary" rows="3" required
                                  placeholder="Tuliskan indikator suhu, potensi hujan, atau status dari sumber domestik..."
                                  class="w-full text-xs rounded-lg border border-slate-300 py-2 px-3 bg-white text-[#202938]"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase font-telemetry text-slate-700 mb-1">
                            Ringkasan Klaim Sumber Internasional <span class="text-red-500">*</span>
                        </label>
                        <textarea name="international_summary" rows="3" required
                                  placeholder="Tuliskan indikator pembanding dari model internasional..."
                                  class="w-full text-xs rounded-lg border border-slate-300 py-2 px-3 bg-white text-[#202938]"></textarea>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase font-telemetry text-slate-700 mb-1">
                        Catatan & Kesimpulan Analisis Administrator <span class="text-red-500">*</span>
                    </label>
                    <textarea name="analysis_notes" rows="4" required
                              placeholder="Analisis mengapa terdapat perbedaan, model mana yang lebih relevan untuk cakupan lokal, dan pertimbangan operasional tim..."
                              class="w-full text-xs rounded-lg border border-slate-300 py-2 px-3 bg-white text-[#202938]"></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase font-telemetry text-slate-700 mb-1">
                        Rekomendasi Tindak Lanjut Verifikator (Opsional)
                    </label>
                    <input type="text" name="recommendation"
                           placeholder="Contoh: Jadikan BMKG sebagai acuan resmi publikasi, pantau perkembangan indikator debit sungai Open-Meteo."
                           class="w-full text-xs rounded-lg border border-slate-300 py-2 px-3 bg-white text-[#202938]">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                        class="px-5 py-2.5 rounded-lg bg-[#173B70] hover:bg-[#0F2A52] text-white font-telemetry text-xs font-bold uppercase tracking-wider shadow-xs transition cursor-pointer">
                    Simpan Catatan Analisis Intelijen
                </button>
            </div>
        </form>
    </div>

    <!-- Section 5: Riwayat Catatan Analisis Tersimpan -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-100/90 overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-200">
            <h3 class="text-base font-bold font-telemetry text-[#202938]">
                Riwayat Dokumen Analisis Perbandingan
            </h3>
            <p class="text-xs text-slate-500">
                Arsip catatan telaah yang telah disahkan oleh Administrator TanggapTerang.
            </p>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($savedComparisons as $comp)
                <div class="p-4 sm:p-5 hover:bg-slate-50 transition">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-800 border border-slate-200">
                                {{ $comp->comparison_topic }}
                            </span>
                            <h4 class="font-bold text-sm text-[#202938]">{{ $comp->title }}</h4>
                        </div>
                        <div class="text-[11px] font-telemetry text-slate-500">
                            Disusun oleh <span class="font-bold text-slate-700">{{ $comp->admin?->name ?? 'Admin' }}</span> • {{ $comp->created_at->format('d M Y H:i') }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-slate-50 p-3 rounded-lg border border-slate-200 text-xs text-slate-700 mt-2">
                        <div>
                            <span class="font-bold font-telemetry text-blue-900">Sumber Domestik ({{ $comp->domestic_source }}):</span>
                            <p class="mt-0.5 text-slate-600">{{ $comp->domestic_summary }}</p>
                        </div>
                        <div>
                            <span class="font-bold font-telemetry text-purple-900">Sumber Internasional ({{ $comp->international_source }}):</span>
                            <p class="mt-0.5 text-slate-600">{{ $comp->international_summary }}</p>
                        </div>
                    </div>

                    <div class="mt-3 text-xs text-slate-800">
                        <span class="font-bold font-telemetry text-[#202938]">Kesimpulan Analisis:</span>
                        <p class="mt-0.5 text-slate-700 leading-relaxed">{{ $comp->analysis_notes }}</p>
                    </div>

                    @if($comp->recommendation)
                        <div class="mt-2 text-xs bg-emerald-50 text-emerald-900 p-2.5 rounded border border-emerald-200">
                            <span class="font-bold font-telemetry uppercase text-[10px] text-emerald-800 block mb-0.5">Rekomendasi Operasional:</span>
                            {{ $comp->recommendation }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-slate-500 text-xs">
                    Belum ada arsip catatan analisis perbandingan yang disimpan.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

