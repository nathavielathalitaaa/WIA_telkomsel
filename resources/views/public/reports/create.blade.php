@extends('layouts.app', [
    'title' => 'Formulir Pelaporan Kejadian Masyarakat — TanggapTerang',
    'activeTab' => 'lapor-form',
])

@section('content')
<div class="space-y-8 sm:space-y-10 lg:space-y-12">
    <!-- Master Institutional Hero Header (Aligned with /welcome) -->
    <section class="bg-[#173B70] text-white border border-[#315D91] rounded-2xl p-5 sm:p-7 space-y-4 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-2 max-w-2xl">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-1 rounded bg-[#D94350] text-white font-telemetry text-xs font-bold uppercase tracking-wider">
                        Pelaporan Terbuka Tanpa Akun
                    </span>
                    <span class="px-2.5 py-1 rounded bg-[#315D91] text-white border border-blue-300/30 font-telemetry text-xs font-bold uppercase tracking-wider">
                        Verifikasi Silang &amp; Privasi Terjaga
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold tracking-tight text-white leading-tight">
                    Formulir Pelaporan Kejadian Masyarakat
                </h1>
                <p class="text-xs sm:text-sm text-blue-100 leading-relaxed">
                    Sampaikan kejadian dan kondisi terkini di sekitar Anda tanpa perlu login. Setiap laporan akan memperoleh nomor tiket unik untuk memantau pemeriksaan dan penanganan tim di lapangan.
                </p>
            </div>
            <div class="flex items-center gap-2.5 shrink-0 self-start sm:self-center">
                <a href="{{ route('public.track') }}"
                   class="px-4 py-2.5 rounded-lg border border-blue-300/40 hover:bg-[#315D91] text-white font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[44px] inline-flex items-center shadow-xs">
                    <span>Lacak Tiket Sebelumnya</span>
                </a>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Main Form Column (8 cols) -->
        <section class="lg:col-span-8 bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 sm:p-6 space-y-6">
            <div class="border-b border-slate-100 pb-4 space-y-1">
                <h2 class="text-lg font-bold text-[#202938]">
                    Data Kejadian &amp; Kondisi Lapangan
                </h2>
                <p class="text-xs sm:text-sm text-[#788397] leading-relaxed">
                    Isi informasi selengkap mungkin. Tim verifikator akan memeriksa kesesuaian laporan Anda dengan data terbuka.
                </p>
            </div>

            @if($errors->any())
                <div class="bg-[#FEF2F2] border-l-4 border-[#DC2626] border-y border-r border-[#FCA5A5] rounded-lg p-4 space-y-1.5" role="alert">
                    <div class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#991B1B]">
                        Terdapat {{ $errors->count() }} Kesalahan Pengisian Formulir
                    </div>
                    <ul class="list-disc list-inside text-xs text-[#991B1B] space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('public.reports.store') }}" class="space-y-6" novalidate>
                @csrf

                <!-- Bagian 1: Informasi Kejadian -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                        <span class="w-6 h-6 rounded bg-[#173B70] text-white font-telemetry text-xs font-bold flex items-center justify-center">1</span>
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                            Rincian Kejadian yang Dilaporkan
                        </h2>
                    </div>

                    <!-- Judul Laporan -->
                    <div>
                        <label for="title" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1">
                            Judul Laporan Kejadian <span class="text-[#D94350]">*</span>
                        </label>
                        <input type="text"
                                id="title"
                                name="title"
                                value="{{ old('title') }}"
                                required
                                maxlength="255"
                                placeholder="Contoh: Genangan banjir setinggi 60 cm di Jalan Raya Dayeuhkolot"
                                class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('title') ? 'border-[#DC2626]' : 'border-slate-200/90' }} rounded-lg text-sm text-[#202938] focus:outline-none focus:border-[#173B70]">
                        @error('title')
                            <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Jenis Kejadian -->
                        <div>
                            <label for="category" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1">
                                Jenis Kejadian <span class="text-[#D94350]">*</span>
                            </label>
                            <select id="category"
                                    name="category"
                                    required
                                    class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('category') ? 'border-[#DC2626]' : 'border-slate-200/90' }} rounded-lg text-sm text-[#202938] focus:outline-none focus:border-[#173B70]">
                                <option value="">-- Pilih Jenis Kejadian --</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" @selected(old('category') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                <span class="text-[10px] font-telemetry uppercase text-slate-400 font-bold">Pilihan Cepat:</span>
                                @foreach(array_slice($categories, 0, 4, true) as $key => $label)
                                    <button type="button"
                                            class="btn-report-cat-pill px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[#202938] font-telemetry text-[11px] transition cursor-pointer"
                                            data-category="{{ $key }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                            @error('category')
                                <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Waktu Kejadian -->
                        <div>
                            <label for="occurred_at" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1">
                                Waktu Kejadian Diamati <span class="text-[#D94350]">*</span>
                            </label>
                            <input type="datetime-local"
                                   id="occurred_at"
                                   name="occurred_at"
                                   value="{{ old('occurred_at', now()->format('Y-m-d\TH:i')) }}"
                                   required
                                   class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('occurred_at') ? 'border-[#DC2626]' : 'border-slate-200/90' }} rounded-lg text-sm text-[#202938] focus:outline-none focus:border-[#173B70]">
                            @error('occurred_at')
                                <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Deskripsi Kejadian -->
                    <div>
                        <label for="description" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1">
                            Deskripsi Kondisi Lapangan <span class="text-[#D94350]">*</span>
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="4"
                                  required
                                  placeholder="Jelaskan kondisi yang terlihat di lokasi, dampak terhadap akses jalan/pemukiman, dan patokan lokasi sekitar..."
                                  class="w-full px-3.5 py-2.5 bg-white border {{ $errors->has('description') ? 'border-[#DC2626]' : 'border-slate-200/90' }} rounded-lg text-sm text-[#202938] focus:outline-none focus:border-[#173B70]">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Bagian 2: Lokasi & Wilayah Administratif -->
                <div class="space-y-4 pt-2">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                        <span class="w-6 h-6 rounded bg-[#173B70] text-white font-telemetry text-xs font-bold flex items-center justify-center">2</span>
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                            Lokasi Kejadian &amp; Koordinat Peta
                        </h2>
                    </div>

                    <!-- Wilayah Administratif Selector Component -->
                    <div>
                        <x-wilayah-selector name="wilayah_administratif_id" :selectedId="old('wilayah_administratif_id')" />
                        @error('wilayah_administratif_id')
                            <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Alamat / Keterangan Lokasi -->
                    <div>
                        <label for="location_name" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1">
                            Alamat atau Keterangan Patokan Lokasi <span class="text-[#D94350]">*</span>
                        </label>
                        <input type="text"
                               id="location_name"
                               name="location_name"
                               value="{{ old('location_name') }}"
                               required
                               maxlength="255"
                               placeholder="Contoh: RW 04 Desa Citeureup, dekat Jembatan Sungai Citarum"
                               class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('location_name') ? 'border-[#DC2626]' : 'border-slate-200/90' }} rounded-lg text-sm text-[#202938] focus:outline-none focus:border-[#173B70]">
                        @error('location_name')
                            <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Interactive Map Coordinate Picker (Optional) -->
                    <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-4 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                                    Titik Koordinat Lokasi (Opsional)
                                </span>
                                <p class="text-xs text-[#788397]">
                                    Klik pada peta di bawah untuk menandai lokasi atau isi koordinat lintang/bujur secara manual. Kosongkan jika tidak diketahui.
                                </p>
                            </div>
                            <button type="button"
                                    id="clear-coordinates-btn"
                                    class="px-2.5 py-1 rounded border border-slate-200/90 bg-white hover:bg-slate-100 font-telemetry text-[11px] font-bold text-[#202938] uppercase">
                                Kosongkan Titik
                            </button>
                        </div>

                        <div id="report-picker-map" class="w-full h-[250px] rounded-lg border border-slate-200/90 bg-slate-100 z-10"></div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="latitude" class="block font-telemetry text-[11px] font-bold uppercase text-[#788397] mb-1">
                                    Latitude (Lintang: -90 s/d 90)
                                </label>
                                <input type="text"
                                       id="latitude"
                                       name="latitude"
                                       value="{{ old('latitude') }}"
                                       placeholder="Contoh: -6.9892000"
                                       class="w-full min-h-[42px] px-3 py-2 bg-white border {{ $errors->has('latitude') ? 'border-[#DC2626]' : 'border-slate-200/90' }} rounded-lg font-telemetry text-xs text-[#202938]">
                                @error('latitude')
                                    <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="longitude" class="block font-telemetry text-[11px] font-bold uppercase text-[#788397] mb-1">
                                    Longitude (Bujur: -180 s/d 180)
                                </label>
                                <input type="text"
                                       id="longitude"
                                       name="longitude"
                                       value="{{ old('longitude') }}"
                                       placeholder="Contoh: 107.6254000"
                                       class="w-full min-h-[42px] px-3 py-2 bg-white border {{ $errors->has('longitude') ? 'border-[#DC2626]' : 'border-slate-200/90' }} rounded-lg font-telemetry text-xs text-[#202938]">
                                @error('longitude')
                                    <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bagian 3: Tautan Bukti & Identitas Pelapor (Opsional & Terlindungi) -->
                <div class="space-y-4 pt-2">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                        <span class="w-6 h-6 rounded bg-[#173B70] text-white font-telemetry text-xs font-bold flex items-center justify-center">3</span>
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                            Tautan Pendukung &amp; Data Pelapor (Opsional)
                        </h2>
                    </div>

                    <!-- URL Bukti / Sumber Pendukung -->
                    <div>
                        <label for="evidence_url" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1">
                            URL Bukti Dokumentasi / Sumber Pendukung (Opsional)
                        </label>
                        <input type="url"
                               id="evidence_url"
                               name="evidence_url"
                               value="{{ old('evidence_url') }}"
                               placeholder="https://contoh-sumber.id/dokumentasi-kejadian"
                               class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('evidence_url') ? 'border-[#DC2626]' : 'border-slate-200/90' }} rounded-lg text-sm text-[#202938] focus:outline-none focus:border-[#173B70]">
                        <p class="mt-1 text-xs text-[#788397]">
                            Masukkan tautan foto/berita/unggah dokumentasi jika tersedia (harus diawali <code>http://</code> atau <code>https://</code>).
                        </p>
                        @error('evidence_url')
                            <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nama Pelapor (Opsional) -->
                        <div>
                            <label for="reporter_name" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1">
                                Nama Pelapor (Opsional)
                            </label>
                            <input type="text"
                                   id="reporter_name"
                                   name="reporter_name"
                                   value="{{ old('reporter_name') }}"
                                   maxlength="150"
                                   placeholder="Boleh dikosongkan (Anonim)"
                                   class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('reporter_name') ? 'border-[#DC2626]' : 'border-slate-200/90' }} rounded-lg text-sm text-[#202938]">
                            @error('reporter_name')
                                <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kontak Pelapor (Opsional) -->
                        <div>
                            <label for="reporter_contact" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1">
                                Kontak Pelapor (Opsional)
                            </label>
                            <input type="text"
                                   id="reporter_contact"
                                   name="reporter_contact"
                                   value="{{ old('reporter_contact') }}"
                                   maxlength="100"
                                   placeholder="Email atau nomor telepon konfirmasi internal"
                                   class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('reporter_contact') ? 'border-[#DC2626]' : 'border-slate-200/90' }} rounded-lg text-sm text-[#202938]">
                            @error('reporter_contact')
                                <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-3 text-xs text-slate-600">
                        <strong class="text-[#202938] font-telemetry uppercase">Jaminan Privasi Pelapor:</strong>
                        Nama dan kontak pelapor bersifat opsional dan hanya dapat dilihat oleh petugas verifikator. Data ini <strong>tidak pernah ditampilkan ke publik maupun halaman pelacakan tiket</strong>.
                    </div>
                </div>

                <!-- Submit Actions -->
                <div class="pt-4 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                    <a href="{{ route('public.monitor') }}"
                       class="px-4 py-2.5 rounded-lg border border-slate-200/90 bg-white hover:bg-slate-50 text-[#202938] font-telemetry text-xs font-bold uppercase tracking-wider min-h-[44px] inline-flex items-center">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-3 rounded-lg btn-brand-gradient font-telemetry text-xs font-bold uppercase tracking-wider min-h-[44px] inline-flex items-center gap-2 shadow-xs cursor-pointer">
                        <span class="font-extrabold text-sm leading-none">+</span>
                        <span>Kirim Laporan Masyarakat</span>
                    </button>
                </div>
            </form>
        </section>

        <!-- Right Sidebar: Reporting Guidelines & Scope Notice (4 cols) -->
        <aside class="lg:col-span-4 space-y-4">
            <div class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                        Mekanisme Alur Laporan Warga
                    </h2>
                    <span class="font-telemetry text-[10px] text-slate-400 font-bold uppercase">5 Tahap</span>
                </div>
                <ol class="space-y-3 text-xs text-slate-600">
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 font-telemetry font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">1</span>
                        <div>
                            <strong class="text-[#202938] block">1. Laporan Masuk &amp; Tiket Acak</strong>
                            Laporan langsung memperoleh kode tiket acak non-sekuensial (contoh: <code>RPT-SIM-8K4M9P2X</code>) berstatus <strong>Menunggu Verifikasi</strong>.
                        </div>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-blue-100 text-[#173B70] font-telemetry font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">2</span>
                        <div>
                            <strong class="text-[#202938] block">2. Pemeriksaan Berkas Awal</strong>
                            Tim Verifikator memeriksa kejelasan data, tanggal kejadian, deskripsi, dan kelengkapan lokasi administratif.
                        </div>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 font-telemetry font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">3</span>
                        <div>
                            <strong class="text-[#202938] block">3. Verifikasi Silang Sumber Terbuka</strong>
                            Data dicocokkan dengan rujukan resmi BMKG, PetaBencana, berita terpercaya, dan laporan warga sekitar sebelum disahkan.
                        </div>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-cyan-100 text-cyan-800 font-telemetry font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">4</span>
                        <div>
                            <strong class="text-[#202938] block">4. Penautan &amp; Kebutuhan Ditindaklanjuti</strong>
                            Laporan sah ditautkan ke kejadian dan kebutuhan diklasifikasikan (Kesehatan, Makan/Minum, Bansos, Evakuasi).
                        </div>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-slate-100 text-[#202938] font-telemetry font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">5</span>
                        <div>
                            <strong class="text-[#202938] block">5. Konfirmasi Berdasarkan Bukti</strong>
                            Penanganan atau penyerahan bantuan disahkan setelah ada bukti dokumentasi yang terverifikasi petugas.
                        </div>
                    </li>
                </ol>
            </div>

            <div class="bg-[#FFFBEB] border border-[#FCD34D] rounded-lg p-4 space-y-2 text-xs text-[#92400E]">
                <div class="font-telemetry font-bold uppercase tracking-wider">
                    Batasan Layanan TanggapTerang
                </div>
                <p class="leading-relaxed">
                    TanggapTerang adalah platform pengumpulan dan penyajian informasi kejadian berbasis laporan masyarakat dan sumber terbuka. Sistem ini <strong>bukan layanan distribusi bantuan logistik</strong> dan tidak menggantikan panggilan darurat resmi.
                </p>
            </div>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const mapEl = document.getElementById('report-picker-map');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const clearBtn = document.getElementById('clear-coordinates-btn');

        if (mapEl && typeof window.L !== 'undefined') {
            const initialLat = parseFloat(latInput?.value);
            const initialLng = parseFloat(lngInput?.value);
            const hasInitial = !Number.isNaN(initialLat) && !Number.isNaN(initialLng);

            const map = window.L.map(mapEl, {
                scrollWheelZoom: false,
            }).setView(hasInitial ? [initialLat, initialLng] : [-6.9344, 107.6248], hasInitial ? 13 : 10);

            window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            let currentMarker = null;

            const setMarker = (lat, lng) => {
                if (currentMarker) {
                    currentMarker.setLatLng([lat, lng]);
                } else {
                    currentMarker = window.L.circleMarker([lat, lng], {
                        radius: 9,
                        fillColor: '#DC2626',
                        color: '#FFFFFF',
                        weight: 2.5,
                        opacity: 1,
                        fillOpacity: 0.95,
                    }).addTo(map);
                }
                if (latInput && lngInput) {
                    latInput.value = Number(lat).toFixed(7);
                    lngInput.value = Number(lng).toFixed(7);
                    latInput.dispatchEvent(new Event('input', { bubbles: true }));
                    lngInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            };

            if (hasInitial) {
                setMarker(initialLat, initialLng);
            }

            map.on('click', (e) => {
                setMarker(e.latlng.lat, e.latlng.lng);
            });

            clearBtn?.addEventListener('click', () => {
                if (currentMarker) {
                    map.removeLayer(currentMarker);
                    currentMarker = null;
                }
                if (latInput && lngInput) {
                    latInput.value = '';
                    lngInput.value = '';
                    latInput.dispatchEvent(new Event('input', { bubbles: true }));
                    lngInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
        }

        document.querySelectorAll('.btn-report-cat-pill').forEach((btn) => {
            btn.addEventListener('click', () => {
                const catSelect = document.getElementById('category');
                if (catSelect) {
                    catSelect.value = btn.getAttribute('data-category');
                    catSelect.dispatchEvent(new Event('change', { bubbles: true }));
                    catSelect.focus();
                }
            });
        });

        // --- Inline Form Validation & Auto-Scroll Navigation ---
        const reportForm = document.querySelector('form[action="{{ route('public.reports.store') }}"]') || document.querySelector('form');
        if (reportForm) {
            const fields = [
                {
                    id: 'title',
                    validate: (el) => {
                        const val = (el.value || '').trim();
                        return val.length >= 5 && val.length <= 255;
                    }
                },
                {
                    id: 'category',
                    validate: (el) => (el.value || '').trim() !== ''
                },
                {
                    id: 'occurred_at',
                    validate: (el) => {
                        const val = (el.value || '').trim();
                        if (!val) return false;
                        const d = new Date(val);
                        if (isNaN(d.getTime())) return false;
                        return d.getTime() <= (Date.now() + 60000);
                    }
                },
                {
                    id: 'description',
                    validate: (el) => {
                        const val = (el.value || '').trim();
                        return val.length >= 10 && val.length <= 5000;
                    }
                },
                {
                    id: 'location_name',
                    validate: (el) => {
                        const val = (el.value || '').trim();
                        return val.length >= 5 && val.length <= 255;
                    }
                },
                {
                    id: 'latitude',
                    validate: (el) => {
                        const latVal = (el.value || '').trim();
                        const lngVal = (document.getElementById('longitude')?.value || '').trim();
                        if (!latVal && !lngVal) return true;
                        if (!latVal && lngVal) return false;
                        const n = Number(latVal);
                        return !isNaN(n) && n >= -90 && n <= 90;
                    }
                },
                {
                    id: 'longitude',
                    validate: (el) => {
                        const lngVal = (el.value || '').trim();
                        const latVal = (document.getElementById('latitude')?.value || '').trim();
                        if (!latVal && !lngVal) return true;
                        if (!lngVal && latVal) return false;
                        const n = Number(lngVal);
                        return !isNaN(n) && n >= -180 && n <= 180;
                    }
                },
                {
                    id: 'evidence_url',
                    validate: (el) => {
                        const val = (el.value || '').trim();
                        if (!val) return true;
                        if (val.length > 255) return false;
                        try {
                            const u = new URL(val);
                            return u.protocol === 'http:' || u.protocol === 'https:';
                        } catch (e) {
                            return false;
                        }
                    }
                },
                {
                    id: 'reporter_name',
                    validate: (el) => ((el.value || '').trim().length <= 150)
                },
                {
                    id: 'reporter_contact',
                    validate: (el) => ((el.value || '').trim().length <= 100)
                }
            ];

            const markInvalid = (el) => {
                el.classList.remove('border-slate-200/90');
                el.classList.add('border-[#DC2626]');
                el.style.borderColor = '#DC2626';
                el.setAttribute('aria-invalid', 'true');
            };

            const markValid = (el) => {
                el.classList.remove('border-[#DC2626]');
                el.classList.add('border-slate-200/90');
                el.style.borderColor = '';
                el.removeAttribute('aria-invalid');

                const parent = el.parentElement;
                if (parent) {
                    const errText = parent.querySelector('p.text-\\[\\#DC2626\\]');
                    if (errText) {
                        errText.style.display = 'none';
                    }
                }
            };

            const scrollToField = (el) => {
                if (!el) return;
                const header = document.querySelector('header');
                const headerHeight = header ? header.getBoundingClientRect().height : 64;
                const offset = headerHeight + 28;

                const label = document.querySelector(`label[for="${el.id}"]`);
                const target = label || el;
                const elementPosition = target.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - offset;

                window.scrollTo({
                    top: Math.max(0, offsetPosition),
                    behavior: 'smooth'
                });

                setTimeout(() => {
                    try {
                        el.focus({ preventScroll: true });
                    } catch (err) {
                        el.focus();
                    }
                }, 180);
            };

            // Attach real-time input listeners to clear indicator on correction
            fields.forEach(({ id, validate }) => {
                const el = document.getElementById(id);
                if (!el) return;

                const handleCorrection = () => {
                    if (validate(el)) {
                        markValid(el);
                        if (id === 'latitude') {
                            const lng = document.getElementById('longitude');
                            const lngDef = fields.find(f => f.id === 'longitude');
                            if (lng && lngDef && lngDef.validate(lng)) markValid(lng);
                        } else if (id === 'longitude') {
                            const lat = document.getElementById('latitude');
                            const latDef = fields.find(f => f.id === 'latitude');
                            if (lat && latDef && latDef.validate(lat)) markValid(lat);
                        }
                    }
                };

                el.addEventListener('input', handleCorrection);
                el.addEventListener('change', handleCorrection);
            });

            // On initial page load (e.g. backend validation redirect), scroll to first error
            const backendErrorField = reportForm.querySelector('.border-\\[\\#DC2626\\]')
                || reportForm.querySelector('p.text-\\[\\#DC2626\\]')?.parentElement?.querySelector('input, select, textarea');
            if (backendErrorField) {
                setTimeout(() => {
                    scrollToField(backendErrorField);
                }, 150);
            }

            // Form submit validation & sequential focus navigation
            reportForm.addEventListener('submit', (e) => {
                let firstInvalidField = null;
                let hasError = false;

                fields.forEach(({ id, validate }) => {
                    const el = document.getElementById(id);
                    if (!el) return;

                    const isValid = validate(el);
                    if (!isValid) {
                        markInvalid(el);
                        hasError = true;
                        if (!firstInvalidField) {
                            firstInvalidField = el;
                        }
                    } else {
                        markValid(el);
                    }
                });

                if (hasError && firstInvalidField) {
                    e.preventDefault();
                    e.stopPropagation();
                    scrollToField(firstInvalidField);
                }
            });
        }
    });
</script>
@endpush
