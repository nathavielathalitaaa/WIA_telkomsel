@extends('layouts.app', [
    'title' => 'Formulir Pelaporan Kejadian Masyarakat — TanggapTerang',
    'activeTab' => 'lapor-form',
])

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" class="flex items-center justify-between gap-3">
        <a href="{{ route('public.monitor') }}" class="font-telemetry text-xs font-bold text-slate-600 hover:text-[#0F172A] inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span>Kembali ke Halaman Pantau</span>
        </a>
        <a href="{{ route('public.track') }}" class="font-telemetry text-xs font-bold text-[#2563EB] hover:underline">
            Sudah Punya Nomor Tiket? Lacak di Sini &rarr;
        </a>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Main Form Column (8 cols) -->
        <section class="lg:col-span-8 bg-white border border-[#E2E8F0] rounded-lg p-5 sm:p-6 space-y-6">
            <div class="border-b border-slate-100 pb-4 space-y-1.5">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-[#DC2626] text-white font-telemetry text-[10px] font-bold uppercase tracking-wider">
                        Pelaporan Publik Tanpa Login
                    </span>
                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-telemetry text-[10px] font-bold uppercase tracking-wider">
                        Status Awal: Menunggu Verifikasi
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-[#0F172A]">
                    Formulir Pelaporan Kejadian Masyarakat
                </h1>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Laporkan kejadian yang Anda amati di lapangan. Laporan yang masuk akan mendapatkan <strong>nomor tiket unik</strong> dan diperiksa terlebih dahulu oleh tim verifikator TanggapTerang sebelum dihubungkan ke data kejadian publik.
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
                        <span class="w-6 h-6 rounded bg-[#0F172A] text-white font-telemetry text-xs font-bold flex items-center justify-center">1</span>
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                            Rincian Kejadian yang Dilaporkan
                        </h2>
                    </div>

                    <!-- Judul Laporan -->
                    <div>
                        <label for="title" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] mb-1">
                            Judul Laporan Kejadian <span class="text-[#DC2626]">*</span>
                        </label>
                        <input type="text"
                               id="title"
                               name="title"
                               value="{{ old('title') }}"
                               required
                               maxlength="255"
                               placeholder="Contoh: Genangan banjir setinggi 60 cm di Jalan Raya Dayeuhkolot"
                               class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('title') ? 'border-[#DC2626]' : 'border-[#CBD5E1]' }} rounded-lg text-sm text-[#0F172A] focus:outline-none focus:border-[#0F172A]">
                        @error('title')
                            <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Jenis Kejadian -->
                        <div>
                            <label for="category" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] mb-1">
                                Jenis Kejadian <span class="text-[#DC2626]">*</span>
                            </label>
                            <select id="category"
                                    name="category"
                                    required
                                    class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('category') ? 'border-[#DC2626]' : 'border-[#CBD5E1]' }} rounded-lg text-sm text-[#0F172A] focus:outline-none focus:border-[#0F172A]">
                                <option value="">-- Pilih Jenis Kejadian --</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" @selected(old('category') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Waktu Kejadian -->
                        <div>
                            <label for="occurred_at" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] mb-1">
                                Waktu Kejadian Diamati <span class="text-[#DC2626]">*</span>
                            </label>
                            <input type="datetime-local"
                                   id="occurred_at"
                                   name="occurred_at"
                                   value="{{ old('occurred_at', now()->format('Y-m-d\TH:i')) }}"
                                   required
                                   class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('occurred_at') ? 'border-[#DC2626]' : 'border-[#CBD5E1]' }} rounded-lg text-sm text-[#0F172A] focus:outline-none focus:border-[#0F172A]">
                            @error('occurred_at')
                                <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Deskripsi Kejadian -->
                    <div>
                        <label for="description" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] mb-1">
                            Deskripsi Kondisi Lapangan <span class="text-[#DC2626]">*</span>
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="4"
                                  required
                                  placeholder="Jelaskan kondisi yang terlihat di lokasi, dampak terhadap akses jalan/pemukiman, dan patokan lokasi sekitar..."
                                  class="w-full px-3.5 py-2.5 bg-white border {{ $errors->has('description') ? 'border-[#DC2626]' : 'border-[#CBD5E1]' }} rounded-lg text-sm text-[#0F172A] focus:outline-none focus:border-[#0F172A]">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Bagian 2: Lokasi & Wilayah Administratif -->
                <div class="space-y-4 pt-2">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                        <span class="w-6 h-6 rounded bg-[#0F172A] text-white font-telemetry text-xs font-bold flex items-center justify-center">2</span>
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
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
                        <label for="location_name" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] mb-1">
                            Alamat atau Keterangan Patokan Lokasi <span class="text-[#DC2626]">*</span>
                        </label>
                        <input type="text"
                               id="location_name"
                               name="location_name"
                               value="{{ old('location_name') }}"
                               required
                               maxlength="255"
                               placeholder="Contoh: RW 04 Desa Citeureup, dekat Jembatan Sungai Citarum"
                               class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('location_name') ? 'border-[#DC2626]' : 'border-[#CBD5E1]' }} rounded-lg text-sm text-[#0F172A] focus:outline-none focus:border-[#0F172A]">
                        @error('location_name')
                            <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Interactive Map Coordinate Picker (Optional) -->
                    <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-4 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                                    Titik Koordinat Lokasi (Opsional)
                                </span>
                                <p class="text-xs text-slate-500">
                                    Klik pada peta di bawah untuk menandai lokasi atau isi koordinat lintang/bujur secara manual. Kosongkan jika tidak diketahui.
                                </p>
                            </div>
                            <button type="button"
                                    id="clear-coordinates-btn"
                                    class="px-2.5 py-1 rounded border border-slate-300 bg-white hover:bg-slate-100 font-telemetry text-[11px] font-bold text-slate-700 uppercase">
                                Kosongkan Titik
                            </button>
                        </div>

                        <div id="report-picker-map" class="w-full h-[250px] rounded-lg border border-[#CBD5E1] bg-slate-100 z-10"></div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="latitude" class="block font-telemetry text-[11px] font-bold uppercase text-slate-600 mb-1">
                                    Latitude (Lintang: -90 s/d 90)
                                </label>
                                <input type="text"
                                       id="latitude"
                                       name="latitude"
                                       value="{{ old('latitude') }}"
                                       placeholder="Contoh: -6.9892000"
                                       class="w-full min-h-[42px] px-3 py-2 bg-white border {{ $errors->has('latitude') ? 'border-[#DC2626]' : 'border-[#CBD5E1]' }} rounded-lg font-telemetry text-xs text-[#0F172A]">
                                @error('latitude')
                                    <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="longitude" class="block font-telemetry text-[11px] font-bold uppercase text-slate-600 mb-1">
                                    Longitude (Bujur: -180 s/d 180)
                                </label>
                                <input type="text"
                                       id="longitude"
                                       name="longitude"
                                       value="{{ old('longitude') }}"
                                       placeholder="Contoh: 107.6254000"
                                       class="w-full min-h-[42px] px-3 py-2 bg-white border {{ $errors->has('longitude') ? 'border-[#DC2626]' : 'border-[#CBD5E1]' }} rounded-lg font-telemetry text-xs text-[#0F172A]">
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
                        <span class="w-6 h-6 rounded bg-[#0F172A] text-white font-telemetry text-xs font-bold flex items-center justify-center">3</span>
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                            Tautan Pendukung &amp; Data Pelapor (Opsional)
                        </h2>
                    </div>

                    <!-- URL Bukti / Sumber Pendukung -->
                    <div>
                        <label for="evidence_url" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] mb-1">
                            URL Bukti Dokumentasi / Sumber Pendukung (Opsional)
                        </label>
                        <input type="url"
                               id="evidence_url"
                               name="evidence_url"
                               value="{{ old('evidence_url') }}"
                               placeholder="https://contoh-sumber.id/dokumentasi-kejadian"
                               class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('evidence_url') ? 'border-[#DC2626]' : 'border-[#CBD5E1]' }} rounded-lg text-sm text-[#0F172A] focus:outline-none focus:border-[#0F172A]">
                        <p class="mt-1 text-xs text-slate-500">
                            Masukkan tautan foto/berita/unggah dokumentasi jika tersedia (harus diawali <code>http://</code> atau <code>https://</code>).
                        </p>
                        @error('evidence_url')
                            <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nama Pelapor (Opsional) -->
                        <div>
                            <label for="reporter_name" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] mb-1">
                                Nama Pelapor (Opsional)
                            </label>
                            <input type="text"
                                   id="reporter_name"
                                   name="reporter_name"
                                   value="{{ old('reporter_name') }}"
                                   maxlength="150"
                                   placeholder="Boleh dikosongkan (Anonim)"
                                   class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('reporter_name') ? 'border-[#DC2626]' : 'border-[#CBD5E1]' }} rounded-lg text-sm text-[#0F172A]">
                            @error('reporter_name')
                                <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kontak Pelapor (Opsional) -->
                        <div>
                            <label for="reporter_contact" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] mb-1">
                                Kontak Pelapor (Opsional)
                            </label>
                            <input type="text"
                                   id="reporter_contact"
                                   name="reporter_contact"
                                   value="{{ old('reporter_contact') }}"
                                   maxlength="100"
                                   placeholder="Email atau nomor telepon konfirmasi internal"
                                   class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('reporter_contact') ? 'border-[#DC2626]' : 'border-[#CBD5E1]' }} rounded-lg text-sm text-[#0F172A]">
                            @error('reporter_contact')
                                <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-3 text-xs text-slate-600">
                        <strong class="text-[#0F172A] font-telemetry uppercase">Jaminan Privasi Pelapor:</strong>
                        Nama dan kontak pelapor bersifat opsional, hanya dapat dilihat oleh petugas verifikator internal, dan <strong>tidak pernah ditampilkan pada halaman publik maupun halaman pelacakan tiket</strong>.
                    </div>
                </div>

                <!-- Submit Actions -->
                <div class="pt-4 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                    <a href="{{ route('public.monitor') }}"
                       class="px-4 py-2.5 rounded-lg border border-[#CBD5E1] bg-white hover:bg-slate-50 text-slate-700 font-telemetry text-xs font-bold uppercase tracking-wider min-h-[44px] inline-flex items-center">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-3 rounded-lg btn-brand-gradient font-telemetry text-xs font-bold uppercase tracking-wider min-h-[44px] inline-flex items-center gap-2 shadow-xs cursor-pointer">
                        <span class="font-extrabold text-sm leading-none">+</span>
                        <span>Kirim Laporan Masyarakat</span>
                        <span>&rarr;</span>
                    </button>
                </div>
            </form>
        </section>

        <!-- Right Sidebar: Reporting Guidelines & Scope Notice (4 cols) -->
        <aside class="lg:col-span-4 space-y-4">
            <div class="bg-white border border-[#E2E8F0] rounded-lg p-5 space-y-3">
                <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                    Alur Pemrosesan Laporan Warga
                </h2>
                <ol class="space-y-3 text-xs text-slate-600">
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 font-telemetry font-bold flex items-center justify-center shrink-0 mt-0.5">1</span>
                        <div>
                            <strong class="text-[#0F172A] block">Penerbitan Nomor Tiket Unik</strong>
                            Laporan Anda langsung memperoleh kode tiket acak (contoh: <code>RPT-SIM-8K4M9P2X</code>) dengan status awal <strong>Menunggu Verifikasi</strong>.
                        </div>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-800 font-telemetry font-bold flex items-center justify-center shrink-0 mt-0.5">2</span>
                        <div>
                            <strong class="text-[#0F172A] block">Pemeriksaan Tim Verifikator</strong>
                            Laporan baru tidak otomatis tampil sebagai kejadian terverifikasi sebelum diperiksa kesesuaiannya dengan laporan lain dan sumber terbuka.
                        </div>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 font-telemetry font-bold flex items-center justify-center shrink-0 mt-0.5">3</span>
                        <div>
                            <strong class="text-[#0F172A] block">Penautan ke Kejadian &amp; Catatan Tindak Lanjut</strong>
                            Laporan yang valid akan dihubungkan dengan kejadian terkait dan dapat dipantau riwayatnya melalui menu <strong>Lacak Tiket</strong>.
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const mapEl = document.getElementById('report-picker-map');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const clearBtn = document.getElementById('clear-coordinates-btn');

        if (!mapEl || typeof window.L === 'undefined') return;

        const initialLat = parseFloat(latInput.value);
        const initialLng = parseFloat(lngInput.value);
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
            latInput.value = Number(lat).toFixed(7);
            lngInput.value = Number(lng).toFixed(7);
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
            latInput.value = '';
            lngInput.value = '';
        });
    });
</script>
@endpush
