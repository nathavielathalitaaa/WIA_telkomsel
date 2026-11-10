@extends('layouts.internal', [
    'title' => 'Buat Kejadian Baru — TanggapTerang Internal',
    'internalMenu' => 'incidents',
])

@section('content')
    <nav class="flex items-center justify-between gap-3">
        <a href="{{ route('internal.incidents.index') }}"
           class="font-telemetry text-xs font-bold text-slate-600 hover:text-[#0F172A]">
            &larr; Kembali ke Daftar Kejadian
        </a>
    </nav>

    <section class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 sm:p-6 space-y-6 max-w-4xl">
        <div class="border-b border-slate-100 pb-4 space-y-1">
            <span class="px-2 py-0.5 rounded bg-[#0F172A] text-white font-telemetry text-[10px] font-bold uppercase tracking-wider">
                Pencatatan Kejadian Baru (Wewenang Operator &amp; Admin)
            </span>
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#0F172A]">
                Buat Data Kejadian Baru
            </h1>
            <p class="text-sm text-slate-600">
                Kejadian baru akan dibuat dengan status awal <strong>Menunggu Verifikasi</strong> dan <strong>Belum Ditindaklanjuti</strong>, serta dapat langsung dihubungkan dengan laporan masyarakat yang relevan.
            </p>
        </div>

        @if(!empty($sourceReport))
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-xs text-blue-950 space-y-1">
                <div class="font-telemetry font-bold uppercase">
                    Diisi Otomatis dari Laporan Masyarakat [{{ $sourceReport->ticket_number }}]:
                </div>
                <p>
                    Formulir ini telah diisi awal berdasarkan laporan warga <strong>{{ $sourceReport->title }}</strong> ({{ $sourceReport->location_name }}). Silakan tinjau, sesuaikan tingkat dampak, dan simpan untuk langsung menautkan laporan ini ke kejadian baru.
                </p>
            </div>
        @endif

        <form method="POST" action="{{ route('internal.incidents.store') }}" class="space-y-5" novalidate>
            @csrf

            <div>
                <label for="title" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] mb-1">
                    Nama / Judul Kejadian <span class="text-[#DC2626]">*</span>
                </label>
                <input type="text"
                       id="title"
                       name="title"
                       value="{{ old('title', $sourceReport?->title) }}"
                       required
                       placeholder="Contoh: [SIMULASI] Luapan Drainase Jalan Raya Cibinong"
                       class="w-full min-h-[44px] px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A]">
                @error('title')
                    <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="incident_type" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] mb-1">
                        Jenis Kejadian <span class="text-[#DC2626]">*</span>
                    </label>
                    <select id="incident_type"
                            name="incident_type"
                            required
                            class="w-full min-h-[44px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A]">
                        <option value="">-- Pilih Jenis --</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" @selected(old('incident_type', $sourceReport?->category) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="severity" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] mb-1">
                        Tingkat Dampak <span class="text-[#DC2626]">*</span>
                    </label>
                    <select id="severity"
                            name="severity"
                            required
                            class="w-full min-h-[44px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A]">
                        @foreach(\App\Enums\IncidentSeverity::cases() as $sCase)
                            <option value="{{ $sCase->value }}" @selected(old('severity', 'sedang') === $sCase->value)>
                                {{ $sCase->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="occurred_at" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] mb-1">
                        Waktu Kejadian <span class="text-[#DC2626]">*</span>
                    </label>
                    <input type="datetime-local"
                           id="occurred_at"
                           name="occurred_at"
                           value="{{ old('occurred_at', ($sourceReport?->occurred_at ?? now())->format('Y-m-d\TH:i')) }}"
                           required
                           class="w-full min-h-[44px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A]">
                </div>
            </div>

            <div>
                <label for="description" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] mb-1">
                    Deskripsi Kejadian <span class="text-[#DC2626]">*</span>
                </label>
                <textarea id="description"
                          name="description"
                          rows="4"
                          required
                          placeholder="Uraikan ringkasan kejadian, dampak terhadap warga/infrastruktur, dan informasi yang telah dihimpun..."
                          class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A]">{{ old('description', $sourceReport?->description) }}</textarea>
            </div>

            <x-wilayah-selector name="wilayah_administratif_id" :selectedId="old('wilayah_administratif_id', $sourceReport?->wilayah_administratif_id)" />

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-1">
                    <label for="location_name" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] mb-1">
                        Nama Lokasi / Kawasan <span class="text-[#DC2626]">*</span>
                    </label>
                    <input type="text"
                           id="location_name"
                           name="location_name"
                           value="{{ old('location_name', $sourceReport?->location_name) }}"
                           required
                           placeholder="Contoh: Desa Bojonggede, Kab. Bogor"
                           class="w-full min-h-[44px] px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A]">
                </div>

                <div>
                    <label for="latitude" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] mb-1">
                        Latitude (Opsional)
                    </label>
                    <input type="text"
                           id="latitude"
                           name="latitude"
                           value="{{ old('latitude', $sourceReport?->latitude) }}"
                           placeholder="-6.4912000"
                           class="w-full min-h-[44px] px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-lg font-telemetry text-xs text-[#0F172A]">
                </div>

                <div>
                    <label for="longitude" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] mb-1">
                        Longitude (Opsional)
                    </label>
                    <input type="text"
                           id="longitude"
                           name="longitude"
                           value="{{ old('longitude', $sourceReport?->longitude) }}"
                           placeholder="106.7975000"
                           class="w-full min-h-[44px] px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-lg font-telemetry text-xs text-[#0F172A]">
                </div>
            </div>

            <!-- Hubungkan Laporan Masyarakat yang Belum Terhubung -->
            @if($unlinkedReports->isNotEmpty())
                <div class="bg-[#F8FAFC] border border-slate-100 rounded-xl p-4 space-y-2.5">
                    <div class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                        Hubungkan Laporan Masyarakat yang Relevan (Opsional)
                    </div>
                    <div class="max-h-48 overflow-y-auto space-y-2 pr-1">
                        @php
                            $defaultCheckedReports = !empty($sourceReport) ? [$sourceReport->id] : [];
                        @endphp
                        @foreach($unlinkedReports as $rep)
                            <label class="flex items-start gap-2.5 p-2.5 bg-white border border-slate-200 rounded cursor-pointer hover:bg-slate-50">
                                <input type="checkbox"
                                       name="report_ids[]"
                                       value="{{ $rep->id }}"
                                       {{ in_array($rep->id, (array) old('report_ids', $defaultCheckedReports)) ? 'checked' : '' }}
                                       class="mt-1">
                                <span class="text-xs">
                                    <strong class="font-telemetry text-[#0F172A]">{{ $rep->ticket_number }}</strong> —
                                    <span class="font-semibold text-[#0F172A]">{{ $rep->title }}</span>
                                    <span class="text-slate-500 block">{{ $rep->location_name }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                <a href="{{ route('internal.incidents.index') }}"
                   class="px-4 py-2.5 rounded-lg border border-[#CBD5E1] text-slate-700 font-telemetry text-xs font-bold uppercase">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white font-telemetry text-xs font-bold uppercase tracking-wider cursor-pointer">
                    Simpan Kejadian Baru
                </button>
            </div>
        </form>
    </section>
@endsection
