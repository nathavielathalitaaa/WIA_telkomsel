@extends('layouts.app')

@section('title', 'Transparansi Sumber Data & Integrasi API — TanggapTerang')
@section('activeTab', 'sumber_data')

@section('content')
<div class="space-y-8 sm:space-y-10 lg:space-y-12">
    <!-- Header Transparansi (Clean, Editorial, Master Design Alignment) -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-200/80 pb-4">
        <div class="space-y-1 max-w-3xl">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#173B70]/10 text-[#173B70] font-telemetry">
                    Transparansi Sumber Rujukan Terbuka
                </span>
                <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>8 Provider Terpantau</span>
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#202938]">
                Transparansi Sumber Data &amp; Integrasi API
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                Pemantauan publik status integrasi, waktu pembaruan, dan rekaman data dari layanan kebencanaan resmi dan terbuka.
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('public.monitor') }}"
               class="px-3.5 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-telemetry text-xs font-bold uppercase tracking-wider transition inline-flex items-center gap-1.5 shadow-2xs min-h-[38px]">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <span>Buka Peta Pantau</span>
            </a>
        </div>
    </div>

    <!-- Ringkasan Telemetri Cepat (Single Compact Row) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 divide-x divide-y sm:divide-y-0 divide-slate-100 bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden text-xs">
        <div class="p-3">
            <span class="font-telemetry text-[10px] font-bold uppercase text-slate-400 block">Total Provider</span>
            <span class="font-telemetry text-lg font-bold text-[#202938] mt-0.5 block">{{ $totalProviders }} Layanan</span>
            <span class="text-[10px] text-slate-400">4 DN • 4 LN</span>
        </div>
        <div class="p-3">
            <span class="font-telemetry text-[10px] font-bold uppercase text-emerald-700 block">Data Berhasil</span>
            <span class="font-telemetry text-lg font-bold text-emerald-700 mt-0.5 block">{{ $successfulProvidersCount }} Aktif</span>
            <span class="text-[10px] text-emerald-600">Tersimpan di DB</span>
        </div>
        <div class="p-3">
            <span class="font-telemetry text-[10px] font-bold uppercase text-amber-700 block">Terkendala</span>
            <span class="font-telemetry text-lg font-bold text-amber-700 mt-0.5 block">{{ $issueCount }} Provider</span>
            <span class="text-[10px] text-amber-600">Akses Terbatas</span>
        </div>
        <div class="p-3">
            <span class="font-telemetry text-[10px] font-bold uppercase text-slate-500 block">Belum Terintegrasi</span>
            <span class="font-telemetry text-lg font-bold text-slate-700 mt-0.5 block">{{ $notIntegratedCount }} Provider</span>
            <span class="text-[10px] text-slate-400">Endpoint Belum Ada</span>
        </div>
        <div class="p-3">
            <span class="font-telemetry text-[10px] font-bold uppercase text-blue-700 block">Total Rekaman</span>
            <span class="font-telemetry text-lg font-bold text-blue-900 mt-0.5 block">{{ number_format($totalRecords) }} Data</span>
            <span class="text-[10px] text-blue-600">{{ $domesticRecordsCount }} DN • {{ $intlRecordsCount }} LN</span>
        </div>
        <div class="p-3">
            <span class="font-telemetry text-[10px] font-bold uppercase text-slate-400 block">Sinkronisasi</span>
            <span class="font-telemetry text-xs font-bold text-slate-700 mt-1 block truncate">
                {{ $lastSyncTime ? $lastSyncTime->diffForHumans() : 'Belum pernah' }}
            </span>
            <span class="text-[10px] text-slate-400 font-mono">
                {{ $lastSyncTime ? $lastSyncTime->format('d M, H:i') . ' WIB' : '-' }}
            </span>
        </div>
    </div>

    <!-- Prinsip Transparansi Ringkas (1 Notice Block) -->
    <div class="p-3.5 rounded-xl bg-blue-50/50 border border-blue-200/60 text-xs text-slate-600 leading-relaxed space-y-1">
        <h2 class="font-telemetry font-bold text-xs uppercase text-[#173B70] tracking-wider">
            Prinsip Transparansi &amp; Integritas Data Eksternal
        </h2>
        <p>
            Data eksternal disajikan sebagai rujukan terbuka dan pembanding situasi, bukan status otomatis kejadian resmi. TanggapTerang bersikap jujur: layanan <strong>BNPB</strong> berstatus <em>Belum Terintegrasi</em> karena endpoint data terbuka belum tersedia secara publik, dan kami tidak membuat data karangan atas nama BNPB.
        </p>
    </div>

    <!-- TABEL RINGKAS PROVIDER (Section 7 Redesign: Minimalist & Transparent) -->
    <section aria-label="Tabel Provider Eksternal" class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
                <h2 class="font-telemetry text-sm sm:text-base font-bold uppercase tracking-wider text-[#202938] flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                    <span>Status Integrasi 8 Provider Eksternal</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Ringkasan status pemeriksaan aktual sistem tanpa paragraf berulang.
                </p>
            </div>
            <div class="flex items-center gap-2 text-xs font-telemetry">
                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold">Aktif: {{ $successfulProvidersCount }}</span>
                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200 font-bold">Stub: {{ $notIntegratedCount }}</span>
            </div>
        </div>

        <!-- Tabel Ringkas Provider -->
        <div class="overflow-x-auto border border-slate-200/80 rounded-xl">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FAFC] border-b border-slate-200/80 font-telemetry text-[11px] font-bold uppercase text-slate-500">
                        <th class="py-2.5 px-3">Provider</th>
                        <th class="py-2.5 px-3">Informasi</th>
                        <th class="py-2.5 px-3">Status Aktual</th>
                        <th class="py-2.5 px-3 text-right">Rincian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @foreach($providers as $provider)
                        @php
                            $statusBadge = \App\Models\ExternalProvider::statusBadgeClass($provider->last_status, $provider->is_active);
                            $statusText = \App\Models\ExternalProvider::statusLabel($provider->last_status, $provider->is_active);
                            $providerRecordCount = \App\Models\ExternalSourceRecord::where('provider_code', $provider->code)->count();
                        @endphp
                        <tr class="hover:bg-slate-50/70 align-top group">
                            <!-- Kolom 1: Provider -->
                            <td class="py-3 px-3">
                                <div class="font-bold text-[#202938] text-sm">
                                    {{ $provider->name }}
                                </div>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="px-1.5 py-0.2 rounded font-telemetry text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 uppercase">
                                        {{ $provider->category === 'dalam_negeri' ? 'Dalam Negeri' : 'Internasional' }}
                                    </span>
                                    <a href="{{ $provider->officialPortalUrl() }}" target="_blank" rel="noopener noreferrer"
                                       class="text-blue-700 hover:underline font-telemetry text-[10px] inline-flex items-center gap-0.5">
                                        <span>Portal Resmi</span>
                                        <span aria-hidden="true">&nearr;</span>
                                    </a>
                                </div>
                            </td>

                            <!-- Kolom 2: Informasi -->
                            <td class="py-3 px-3 space-y-1">
                                <span class="inline-block px-1.5 py-0.5 rounded font-telemetry text-[10px] font-bold border {{ $provider->dataNatureBadgeClass() }}">
                                    {{ $provider->dataNatureLabel() }}
                                </span>
                                <div class="text-[11px] text-slate-600 font-telemetry">
                                    <span>Terakhir: <strong>{{ $provider->last_record_count }} record</strong></span>
                                    <span class="text-slate-300 mx-1">•</span>
                                    <span>Total DB: <strong class="text-blue-900">{{ $providerRecordCount }} record</strong></span>
                                    @if($provider->last_duration_ms)
                                        <span class="text-slate-300 mx-1">•</span>
                                        <span class="font-mono text-slate-500">{{ $provider->last_duration_ms }} ms</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Kolom 3: Status -->
                            <td class="py-3 px-3 space-y-1">
                                <div>
                                    <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase tracking-wider {{ $statusBadge }} border inline-block">
                                        {{ $statusText }}
                                    </span>
                                </div>
                                <div class="text-[10px] text-slate-400 font-telemetry">
                                    Pembaruan: {{ $provider->last_fetched_at ? $provider->last_fetched_at->diffForHumans() : 'Belum pernah' }}
                                </div>
                            </td>

                            <!-- Kolom 4: Aksi Detail -->
                            <td class="py-3 px-3 text-right">
                                <details class="relative inline-block text-left">
                                    <summary class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-telemetry text-[11px] font-bold uppercase tracking-wider cursor-pointer transition select-none inline-flex items-center gap-1 list-none shadow-2xs">
                                        <span>Detail</span>
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </summary>

                                    <!-- Dropdown Drawer Panel -->
                                    <div class="fixed inset-x-4 top-24 sm:absolute sm:inset-auto sm:right-0 sm:top-full sm:mt-1 w-auto sm:w-96 bg-white rounded-xl shadow-lg border border-slate-200 p-4 z-50 space-y-3 text-left">
                                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                            <strong class="font-telemetry text-xs font-bold text-[#202938] uppercase">
                                                {{ $provider->name }}
                                            </strong>
                                            <span class="font-telemetry text-[10px] text-slate-400 uppercase font-mono">{{ $provider->code }}</span>
                                        </div>

                                        <!-- Keterbatasan / Pesan Error -->
                                        @if($provider->last_error_message && $provider->last_status !== 'success')
                                            <div class="p-2 rounded bg-amber-50 border border-amber-200 text-[11px] text-amber-900 leading-snug">
                                                <strong>Catatan Pengambilan:</strong> {{ $provider->last_error_message }}
                                            </div>
                                        @endif

                                        <!-- 3 Contoh Rekaman -->
                                        <div class="space-y-1.5">
                                            <span class="font-telemetry text-[10px] font-bold text-slate-500 uppercase block">
                                                Contoh Rekaman Terbaru:
                                            </span>
                                            @if($provider->sourceRecords->isNotEmpty())
                                                <ul class="space-y-1.5">
                                                    @foreach($provider->sourceRecords as $sample)
                                                        <li class="p-2 rounded bg-slate-50 border border-slate-200 text-[11px] text-slate-800 hover:bg-blue-50/50 transition">
                                                            <div class="font-bold line-clamp-1 text-[#202938]" title="{{ $sample->title }}">
                                                                {{ $sample->title }}
                                                            </div>
                                                            <div class="flex items-center justify-between text-[10px] text-slate-500 mt-1">
                                                                <span class="truncate max-w-[160px]">{{ $sample->location_name ?: 'Indonesia' }}</span>
                                                                <a href="{{ $sample->source_url ?: route('public.sources.show', $sample->id) }}"
                                                                   target="_blank" rel="noopener noreferrer"
                                                                   class="text-blue-700 hover:underline font-bold font-telemetry">
                                                                    {{ $sample->source_url ?: 'Lihat Detail &rarr;' }}
                                                                </a>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <div class="p-2.5 rounded bg-slate-100 text-[11px] text-slate-600 leading-snug">
                                                    @if(! $provider->is_active)
                                                        <em>Belum ada rekaman tersimpan — endpoint dataset kejadian BNPB belum terintegrasi. Tidak ada data yang dikarang.</em>
                                                    @else
                                                        <em>Belum ada rekaman tersimpan dari provider ini.</em>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                                            <a href="{{ route('public.sources.index', ['provider' => $provider->code]) }}"
                                               class="text-blue-700 hover:underline font-telemetry text-xs font-bold uppercase">
                                                Saring Data {{ $provider->code }} &rarr;
                                            </a>
                                        </div>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <!-- SECTION 2: EKSPLORASI REKAMAN DATA PUBLIK DENGAN FILTER -->
    <section aria-label="Eksplorasi Rekaman Data" class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
                <h2 class="font-telemetry text-sm sm:text-base font-bold uppercase tracking-wider text-[#202938]">
                    Eksplorasi Rekaman Data Eksternal
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Filter berdasarkan kategori, cakupan wilayah, atau kata kunci pencarian.
                </p>
            </div>

            @if($selectedInfoType || $selectedCategory || $selectedProvider || $searchKeyword)
                <a href="{{ route('public.sources.index') }}"
                   class="inline-flex items-center gap-1 text-xs font-telemetry font-bold text-red-600 hover:text-red-800">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Reset Filter
                </a>
            @endif
        </div>

        <!-- Category Pills Bar -->
        <div class="flex flex-wrap items-center gap-1.5">
            <span class="text-xs font-telemetry font-bold text-slate-400 uppercase mr-1">Kategori:</span>
            @foreach($availableCategories as $typeKey => $typeLabel)
                @php
                    $isActive = ($selectedInfoType === $typeKey);
                    $url = route('public.sources.index', array_merge(request()->query(), ['info_type' => $typeKey, 'page' => 1]));
                @endphp
                <a href="{{ $url }}"
                   class="px-2.5 py-1 rounded-lg font-telemetry text-xs font-bold transition {{ $isActive ? 'nav-active-gradient' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200' }}">
                    {{ $typeLabel }}
                </a>
            @endforeach
        </div>

        <!-- Filter Form -->
        <form action="{{ route('public.sources.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 pt-1">
            @if($selectedInfoType)
                <input type="hidden" name="info_type" value="{{ $selectedInfoType }}">
            @endif

            <div class="sm:col-span-3">
                <label for="filter-category" class="sr-only">Cakupan Wilayah</label>
                <select id="filter-category" name="category" onchange="this.form.submit()"
                        class="w-full text-xs rounded-lg border-slate-200 bg-slate-50/60 focus:bg-white focus:border-[#173B70] py-2 px-3">
                    <option value="">Semua Cakupan</option>
                    <option value="dalam_negeri" {{ $selectedCategory === 'dalam_negeri' ? 'selected' : '' }}>Dalam Negeri (Indonesia)</option>
                    <option value="internasional" {{ $selectedCategory === 'internasional' ? 'selected' : '' }}>Internasional / Global</option>
                </select>
            </div>

            <div class="sm:col-span-3">
                <label for="filter-provider" class="sr-only">Provider Spesifik</label>
                <select id="filter-provider" name="provider" onchange="this.form.submit()"
                        class="w-full text-xs rounded-lg border-slate-200 bg-slate-50/60 focus:bg-white focus:border-[#173B70] py-2 px-3">
                    <option value="">Semua Provider</option>
                    @foreach($allProviders as $p)
                        <option value="{{ $p->code }}" {{ $selectedProvider === $p->code ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-4">
                <label for="filter-q" class="sr-only">Cari Kata Kunci</label>
                <input type="text" id="filter-q" name="q" value="{{ $searchKeyword }}"
                       placeholder="Cari judul, lokasi, atau ringkasan..."
                       class="w-full text-xs rounded-lg border-slate-200 bg-slate-50/60 focus:bg-white focus:border-[#173B70] py-2 px-3">
            </div>

            <div class="sm:col-span-2">
                <button type="submit"
                        class="w-full py-2 px-3 rounded-lg btn-brand-gradient font-telemetry text-xs font-bold uppercase tracking-wider text-center cursor-pointer min-h-[38px]">
                    Terapkan
                </button>
            </div>
        </form>

        <!-- Daftar Rekaman -->
        <div class="pt-3 border-t border-slate-100">
            <div class="flex items-center justify-between mb-3">
                <span class="font-telemetry text-xs font-bold text-slate-700 uppercase">
                    Hasil: {{ $records->total() }} Rekaman Ditemukan
                </span>
                <span class="text-[11px] text-slate-400 font-telemetry">
                    Halaman {{ $records->currentPage() }} dari {{ $records->lastPage() ?: 1 }}
                </span>
            </div>

            @if($records->isEmpty())
                <div class="bg-slate-50/60 border border-slate-200/80 rounded-xl p-8 text-center space-y-2">
                    <p class="text-xs font-medium text-slate-600">Tidak ada rekaman data yang sesuai dengan filter.</p>
                    <a href="{{ route('public.sources.index') }}"
                       class="inline-block px-3 py-1 rounded bg-slate-200 hover:bg-slate-300 text-slate-700 font-telemetry text-xs font-bold uppercase">
                        Reset Filter
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($records as $rec)
                        <article class="bg-white rounded-xl border border-slate-200/80 shadow-2xs hover:border-blue-400 hover:shadow-xs transition p-3.5 flex flex-col justify-between space-y-2.5">
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between gap-1 text-[10px] font-telemetry">
                                    <span class="font-bold uppercase px-1.5 py-0.2 rounded bg-blue-50 text-blue-800 border border-blue-200">
                                        {{ \App\Models\ExternalProvider::infoTypeLabel($rec->info_type) }}
                                    </span>
                                    <span class="text-slate-400">
                                        {{ $rec->occurred_at ? $rec->occurred_at->format('d M, H:i') : ($rec->published_at ? $rec->published_at->format('d M, H:i') : '-') }}
                                    </span>
                                </div>

                                <h3 class="font-bold text-xs sm:text-sm text-[#202938] leading-snug">
                                    <a href="{{ route('public.sources.show', $rec->id) }}" class="hover:text-blue-700 transition">
                                        {{ $rec->title }}
                                    </a>
                                </h3>

                                @if($rec->summary)
                                    <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">
                                        {{ $rec->summary }}
                                    </p>
                                @endif

                                <div class="text-[10px] text-slate-400 pt-1">
                                    <span>Lokasi: {{ $rec->location_name ?: 'Indonesia' }}</span>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px]">
                                <span class="font-telemetry text-[10px] text-slate-400 uppercase truncate max-w-[130px]">
                                    {{ $rec->provider?->name ? Str::limit($rec->provider->name, 16) : $rec->provider_code }}
                                </span>
                                <a href="{{ route('public.sources.show', $rec->id) }}"
                                   class="text-[#173B70] hover:underline font-telemetry font-bold inline-flex items-center gap-0.5">
                                    <span>Detail</span>
                                    <span aria-hidden="true">&rarr;</span>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if($records->hasPages())
                    <div class="pt-3">
                        {{ $records->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>
</div>
@endsection
