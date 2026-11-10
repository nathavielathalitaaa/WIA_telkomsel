@extends('layouts.app', [
    'title' => 'Catat Respons Bantuan Masyarakat / Komunitas — TanggapTerang',
    'activeTab' => 'respons-form',
])

@section('content')
    <!-- Master Institutional Hero Header (Aligned with /welcome) -->
    <section class="bg-[#173B70] text-white border border-[#315D91] rounded-2xl p-6 sm:p-8 space-y-4 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-2 max-w-2xl">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-1 rounded bg-amber-400 text-[#173B70] font-telemetry text-xs font-bold uppercase tracking-wider">
                        Partisipasi Terbuka Tanpa Login
                    </span>
                    <span class="px-2.5 py-1 rounded bg-[#315D91] text-white border border-blue-300/30 font-telemetry text-xs font-bold uppercase tracking-wider">
                        Tercatat &amp; Terkoordinasi
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight leading-tight text-white">
                    Catat Komitmen &amp; Respons Bantuan
                </h1>
                <p class="text-sm sm:text-base text-blue-100 leading-relaxed">
                    Catat tawaran bantuan logistik, medis, evakuasi, atau bantuan yang telah disalurkan. Setiap catatan diverifikasi agar koordinasi bantuan terarah dan tidak tumpang tindih.
                </p>
            </div>
            <div class="flex items-center gap-2.5 shrink-0 self-start sm:self-center">
                <a href="{{ route('public.monitor') }}"
                   class="px-4 py-2.5 rounded-lg border border-blue-300/40 hover:bg-[#315D91] text-white font-telemetry text-xs font-bold uppercase tracking-wider transition min-h-[44px] inline-flex items-center gap-2 shadow-xs">
                    <span>&larr; Pantau Kebutuhan</span>
                </a>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        <!-- Main Form Column (8 cols) -->
        <section class="lg:col-span-8 bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 sm:p-6 space-y-6">
            @php
                $activeSubmissionType = old('submission_type', $selectedSubmissionType ?? \App\Models\AidResponse::SUBMISSION_TAWARAN_RENCANA);
                $activeNeedCategory = old('need_category', $selectedNeedCategory ?? '');
            @endphp

            <div class="border-b border-slate-200 pb-4 space-y-1">
                <h2 class="text-lg font-bold text-[#202938]">
                    Data Tawaran / Realisasi Respons Bantuan
                </h2>
                <p class="text-xs sm:text-sm text-[#788397] leading-relaxed">
                    Pilih tipe respons dan rincian kebutuhan yang didukung untuk mempercepat koordinasi dengan posko lapangan.
                </p>
            </div>
                    Catat tawaran bantuan yang direncanakan atau laporkan bantuan yang sudah diserahkan di lokasi kejadian untuk diperiksa oleh petugas.
                </p>
            </div>

            @if($errors->any())
                <div class="bg-[#FEF2F2] border-l-4 border-[#DC2626] border-y border-r border-[#FCA5A5] rounded-lg p-4 text-xs sm:text-sm text-[#991B1B]" role="alert">
                    <div class="font-telemetry font-bold uppercase tracking-wider mb-1">
                        Mohon periksa kembali isian formulir ({{ $errors->count() }} kesalahan):
                    </div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('public.aid-responses.store') }}" class="space-y-6" novalidate>
                @csrf

                <!-- SECTION 0: Pilihan Jenis Pencatatan (Tawaran Rencana vs Laporan Bantuan Sudah Diberikan) -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded bg-[#173B70] text-white font-telemetry text-xs font-bold flex items-center justify-center">A/B</span>
                            <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                                Pilih Jenis Pencatatan Respons Bantuan <span class="text-[#D94350]">*</span>
                            </h2>
                        </div>
                        <span class="font-telemetry text-[11px] text-[#788397]">Dibedakan secara eksplisit</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="relative flex items-start gap-3 p-3.5 rounded-lg border cursor-pointer transition {{ $activeSubmissionType === 'tawaran_rencana' ? 'border-[#173B70] bg-blue-50/50 ring-1 ring-[#173B70]' : 'border-slate-200/90 bg-white hover:bg-slate-50' }}"
                               data-submission-card="tawaran_rencana">
                            <input type="radio"
                                   name="submission_type"
                                   value="tawaran_rencana"
                                   {{ $activeSubmissionType === 'tawaran_rencana' ? 'checked' : '' }}
                                   class="mt-1 w-4 h-4 text-[#173B70] border-slate-300 focus:ring-[#173B70]">
                            <span class="space-y-1">
                                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] block">
                                    A. Tawaran / Rencana Bantuan
                                </span>
                                <span class="text-xs text-slate-600 block leading-relaxed">
                                    Mencatat komitmen, tawaran, atau jadwal rencana penyaluran bantuan terhadap kebutuhan kejadian.
                                </span>
                            </span>
                        </label>

                        <label class="relative flex items-start gap-3 p-3.5 rounded-lg border cursor-pointer transition {{ $activeSubmissionType === 'laporan_penyerahan' ? 'border-emerald-600 bg-emerald-50/50 ring-1 ring-emerald-600' : 'border-slate-200/90 bg-white hover:bg-slate-50' }}"
                               data-submission-card="laporan_penyerahan">
                            <input type="radio"
                                   name="submission_type"
                                   value="laporan_penyerahan"
                                   {{ $activeSubmissionType === 'laporan_penyerahan' ? 'checked' : '' }}
                                   class="mt-1 w-4 h-4 text-emerald-600 border-slate-300 focus:ring-emerald-500">
                            <span class="space-y-1">
                                <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] block">
                                    B. Laporan Bantuan Sudah Diberikan
                                </span>
                                <span class="text-xs text-slate-600 block leading-relaxed">
                                    Melaporkan bahwa bantuan telah diserahkan di lokasi (mencatat waktu &amp; lokasi penyerahan). Status tetap <strong>belum terkonfirmasi</strong> hingga diperiksa petugas.
                                </span>
                            </span>
                        </label>
                    </div>
                    @error('submission_type')
                        <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SECTION 1: Keterkaitan Kejadian & Kebutuhan -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                        <span class="w-5 h-5 rounded bg-[#173B70] text-white font-telemetry text-xs font-bold flex items-center justify-center">1</span>
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                            Kejadian &amp; Kategori Kebutuhan yang Direspons
                        </h2>
                    </div>

                    <div>
                        <label for="incident_id" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1">
                            Pilih Kejadian Terkait <span class="text-[#DC2626]">*</span>
                        </label>
                        <select id="incident_id"
                                name="incident_id"
                                required
                                class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('incident_id') ? 'border-[#DC2626]' : 'border-slate-200/90' }} rounded-lg text-sm text-[#202938]">
                            @foreach($incidents as $inc)
                                <option value="{{ $inc->id }}" {{ (int) old('incident_id', $selectedIncident?->id) === $inc->id ? 'selected' : '' }}>
                                    {{ $inc->title }} ({{ $inc->location_name }})
                                </option>
                            @endforeach
                        </select>
                        @error('incident_id')
                            <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="need_category" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1">
                                Kategori Kebutuhan / Bantuan <span class="text-[#DC2626]">*</span>
                            </label>
                            <select id="need_category"
                                     name="need_category"
                                     required
                                     class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('need_category') ? 'border-[#DC2626]' : 'border-slate-200/90' }} rounded-lg text-sm text-[#202938]">
                                <option value="">-- Pilih Kategori Kebutuhan --</option>
                                @foreach($needCategories as $key => $label)
                                    <option value="{{ $key }}" {{ $activeNeedCategory === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('need_category')
                                <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="report_id" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1">
                                Rujukan Tiket Laporan Warga (Opsional)
                            </label>
                            <select id="report_id"
                                    name="report_id"
                                    class="w-full min-h-[44px] px-3.5 py-2 bg-white border border-slate-200/90 rounded-lg text-sm text-[#202938]">
                                <option value="">-- Umum / Seluruh Wilayah Kejadian --</option>
                                @foreach($incidents as $inc)
                                    @foreach($inc->reports as $rep)
                                        <option value="{{ $rep->id }}"
                                                data-incident-id="{{ $inc->id }}"
                                                {{ (int) old('report_id', $selectedReportId) === $rep->id ? 'selected' : '' }}>
                                            [{{ $rep->ticket_number }}] {{ \Illuminate\Support\Str::limit($rep->title, 55) }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                            <p class="mt-1 text-[11px] text-slate-500">
                                Pilih jika respons ini ditujukan khusus pada laporan warga tertentu.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: Rincian Bantuan, Waktu Penyerahan, & Lokasi Tujuan -->
                <div class="space-y-4 pt-2">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                        <span class="w-5 h-5 rounded bg-[#173B70] text-white font-telemetry text-xs font-bold flex items-center justify-center">2</span>
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                            Rincian Bantuan, Kapasitas, Waktu, &amp; Lokasi Penyaluran
                        </h2>
                    </div>

                    <div id="delivery-report-notice"
                         class="{{ $activeSubmissionType === 'laporan_penyerahan' ? '' : 'hidden' }} bg-amber-50 border border-amber-300 rounded-lg p-3 text-xs text-amber-900 space-y-1">
                        <div class="font-telemetry font-bold uppercase">
                            Catatan Penting Laporan Bantuan yang Sudah Diberikan:
                        </div>
                        <p class="leading-relaxed">
                            Meskipun bantuan dilaporkan telah diserahkan di lokasi, entri ini <strong>tidak langsung berstatus Penyerahan Dikonfirmasi</strong>. Sistem akan mencatatnya dengan status awal <strong>Direncanakan (Menunggu Pemeriksaan Petugas)</strong> hingga tim berwenang memeriksa bukti pendukung dan memvalidasi penyerahan.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="aid_item_title" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1">
                                <span id="label-aid-item-title">
                                    {{ $activeSubmissionType === 'laporan_penyerahan' ? 'Jenis Bantuan yang Sudah Diserahkan' : 'Jenis Bantuan yang Direncanakan / Ditawarkan' }}
                                </span>
                                <span class="text-[#DC2626]">*</span>
                            </label>
                            <input type="text"
                                   id="aid_item_title"
                                   name="aid_item_title"
                                   value="{{ old('aid_item_title') }}"
                                   required
                                   maxlength="255"
                                   placeholder="Contoh: Air Bersih, Nasi Bungkus, Obat-obatan, Sembako"
                                   class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('aid_item_title') ? 'border-[#DC2626]' : 'border-slate-200/90' }} rounded-lg text-sm text-[#202938]">
                            <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                <span class="text-[10px] font-telemetry uppercase text-slate-400 font-bold">Pilihan Cepat:</span>
                                <button type="button" class="btn-example-aid btn-aid-quick-pill px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-telemetry text-[11px] transition cursor-pointer" data-fill="Air Bersih">Air Bersih</button>
                                <button type="button" class="btn-example-aid btn-aid-quick-pill px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-telemetry text-[11px] transition cursor-pointer" data-fill="Nasi Bungkus">Nasi Bungkus</button>
                                <button type="button" class="btn-example-aid btn-aid-quick-pill px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-telemetry text-[11px] transition cursor-pointer" data-fill="Obat-obatan">Obat-obatan</button>
                                <button type="button" class="btn-example-aid btn-aid-quick-pill px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-telemetry text-[11px] transition cursor-pointer" data-fill="Paket Sembako">Paket Sembako</button>
                                <button type="button" class="btn-example-aid btn-aid-quick-pill px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-telemetry text-[11px] transition cursor-pointer" data-fill="Tenda Darurat">Tenda Darurat</button>
                            </div>
                            @error('aid_item_title')
                                <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="quantity_label" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1">
                                Jumlah &amp; Satuan Bantuan <span class="text-[#DC2626]">*</span>
                            </label>
                            <input type="text"
                                   id="quantity_label"
                                   name="quantity_label"
                                   value="{{ old('quantity_label') }}"
                                   required
                                   maxlength="150"
                                   placeholder="Contoh: 100 Paket / 25 Galon Air / 1 Tim Medis"
                                   class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('quantity_label') ? 'border-[#DC2626]' : 'border-slate-200/90' }} rounded-lg text-sm text-[#202938]">
                            @error('quantity_label')
                                <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="target_location" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1">
                                <span id="label-target-location">
                                    {{ $activeSubmissionType === 'laporan_penyerahan' ? 'Lokasi Penyerahan Bantuan di Lapangan' : 'Lokasi atau Wilayah Tujuan Penyaluran' }}
                                </span>
                                <span class="text-[#DC2626]">*</span>
                            </label>
                            <input type="text"
                                   id="target_location"
                                   name="target_location"
                                   value="{{ old('target_location', $selectedIncident?->location_name) }}"
                                   required
                                   maxlength="255"
                                   placeholder="Contoh: Pos Singgah Warga RT 02 / RW 04 Dusun 2, Bojonggede"
                                   class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('target_location') ? 'border-[#DC2626]' : 'border-slate-200/90' }} rounded-lg text-sm text-[#202938]">
                            @error('target_location')
                                <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="delivered_at" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1">
                                <span id="label-delivered-at">
                                    {{ $activeSubmissionType === 'laporan_penyerahan' ? 'Waktu Penyerahan Bantuan di Lokasi' : 'Estimasi / Jadwal Penyaluran (Opsional)' }}
                                </span>
                                <span id="required-delivered-at" class="text-[#DC2626] {{ $activeSubmissionType === 'laporan_penyerahan' ? '' : 'hidden' }}">*</span>
                            </label>
                            <input type="datetime-local"
                                   id="delivered_at"
                                   name="delivered_at"
                                   value="{{ old('delivered_at') }}"
                                   {{ $activeSubmissionType === 'laporan_penyerahan' ? 'required' : '' }}
                                   class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('delivered_at') ? 'border-[#DC2626]' : 'border-slate-200/90' }} rounded-lg text-sm font-telemetry text-[#202938]">
                            <p id="hint-delivered-at" class="mt-1 text-[11px] text-slate-500">
                                {{ $activeSubmissionType === 'laporan_penyerahan' ? 'Wajib diisi sesuai waktu bantuan diserahkan di lokasi kejadian.' : 'Opsional jika baru berupa tawaran atau rencana penyaluran.' }}
                            </p>
                            @error('delivered_at')
                                <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="aid_description" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1">
                            <span id="label-aid-description">
                                {{ $activeSubmissionType === 'laporan_penyerahan' ? 'Keterangan Penyerahan & Penerima di Lokasi (Opsional)' : 'Keterangan Rencana Penyaluran (Opsional)' }}
                            </span>
                        </label>
                        <textarea id="aid_description"
                                  name="aid_description"
                                  rows="3"
                                  placeholder="Jelaskan rincian bantuan, perwakilan warga/posko penerima di lokasi, atau jadwal rencana pengiriman..."
                                  class="w-full px-3.5 py-2.5 bg-white border border-slate-200/90 rounded-lg text-sm text-[#202938]">{{ old('aid_description') }}</textarea>
                    </div>
                </div>

                <!-- SECTION 3: Identitas Pemberi Respons & Persetujuan Publikasi -->
                <div class="space-y-4 pt-2">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                        <span class="w-5 h-5 rounded bg-[#173B70] text-white font-telemetry text-xs font-bold flex items-center justify-center">3</span>
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                            Identitas Pemberi Respons &amp; Perlindungan Privasi
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="organization_name" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1">
                                Nama Organisasi / Komunitas / Inisiatif <span class="text-[#DC2626]">*</span>
                            </label>
                            <input type="text"
                                   id="organization_name"
                                   name="organization_name"
                                   value="{{ old('organization_name') }}"
                                   required
                                   maxlength="150"
                                   placeholder="Contoh: Komunitas Dapur Warga Bojonggede"
                                   class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('organization_name') ? 'border-[#DC2626]' : 'border-slate-200/90' }} rounded-lg text-sm text-[#202938]">
                            @error('organization_name')
                                <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="responder_type" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1">
                                Jenis Pemberi Respons <span class="text-[#DC2626]">*</span>
                            </label>
                            <select id="responder_type"
                                    name="responder_type"
                                    required
                                    class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('responder_type') ? 'border-[#DC2626]' : 'border-slate-200/90' }} rounded-lg text-sm text-[#202938]">
                                @foreach($responderTypes as $key => $label)
                                    <option value="{{ $key }}" {{ old('responder_type', 'komunitas_warga') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('responder_type')
                                <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Checkbox Persetujuan Tampil Nama di Publik -->
                    <div class="bg-[#F8F9FC] border border-slate-200/90 rounded-lg p-3.5">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox"
                                   name="allow_public_name"
                                   value="1"
                                   {{ old('allow_public_name', '1') ? 'checked' : '' }}
                                   class="mt-1 w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-xs text-slate-700 leading-relaxed">
                                <strong class="text-[#202938] block font-telemetry uppercase">Tampilkan Nama Organisasi/Komunitas pada Data Publik</strong>
                                Jika dicentang, nama organisasi/komunitas di atas akan ditampilkan pada tabel transparansi publik. Jika tidak dicentang, halaman publik akan menampilkan <em>&ldquo;Inisiatif Masyarakat / Pemberi Respons Anonim&rdquo;</em>.
                            </span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="contact_name" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1">
                                Nama Narahubung Internal (Opsional — Rahasia)
                            </label>
                            <input type="text"
                                   id="contact_name"
                                   name="contact_name"
                                   value="{{ old('contact_name') }}"
                                   maxlength="150"
                                   placeholder="Hanya untuk konfirmasi tim internal"
                                   class="w-full min-h-[44px] px-3.5 py-2 bg-white border border-slate-200/90 rounded-lg text-sm text-[#202938]">
                        </div>

                        <div>
                            <label for="contact_info" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1">
                                Nomor/Email Narahubung (Opsional — Rahasia)
                            </label>
                            <input type="text"
                                   id="contact_info"
                                   name="contact_info"
                                   value="{{ old('contact_info') }}"
                                   maxlength="150"
                                   placeholder="Tidak ditampilkan di halaman publik"
                                   class="w-full min-h-[44px] px-3.5 py-2 bg-white border border-slate-200/90 rounded-lg text-sm text-[#202938]">
                        </div>
                    </div>

                    <div>
                        <label for="evidence_url" class="block font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938] mb-1">
                            <span id="label-evidence-url">
                                {{ $activeSubmissionType === 'laporan_penyerahan' ? 'Tautan Bukti Dokumentasi Penyerahan Bantuan (Disarankan)' : 'Tautan Publikasi / Dokumentasi Rencana Bantuan (Opsional)' }}
                            </span>
                        </label>
                        <input type="url"
                               id="evidence_url"
                               name="evidence_url"
                               value="{{ old('evidence_url') }}"
                               placeholder="https://contoh-komunitas.org/dokumentasi-serah-terima"
                               class="w-full min-h-[44px] px-3.5 py-2 bg-white border {{ $errors->has('evidence_url') ? 'border-[#DC2626]' : 'border-slate-200/90' }} rounded-lg text-sm text-[#202938]">
                        <p class="mt-1 text-[11px] text-slate-500">
                            Tautan dokumentasi digunakan petugas berwenang untuk memeriksa sebelum mengubah status menjadi <strong>Penyerahan Dikonfirmasi</strong>.
                        </p>
                        @error('evidence_url')
                            <p class="mt-1 text-xs font-semibold text-[#DC2626]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Actions -->
                <div class="pt-4 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                    <a href="{{ $selectedIncident ? route('public.incidents.show', $selectedIncident->slug) : route('public.monitor') }}"
                       class="px-4 py-2.5 rounded-lg border border-slate-200/90 bg-white hover:bg-slate-50 text-slate-700 font-telemetry text-xs font-bold uppercase tracking-wider min-h-[44px] inline-flex items-center">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-3 rounded-lg btn-brand-gradient font-telemetry text-xs font-bold uppercase tracking-wider min-h-[44px] inline-flex items-center gap-2 shadow-xs cursor-pointer">
                        <span class="font-extrabold text-sm leading-none">+</span>
                        <span id="submit-btn-label">
                            {{ $activeSubmissionType === 'laporan_penyerahan' ? 'Kirim Laporan Bantuan Sudah Diberikan (Status Awal: Direncanakan)' : 'Catat Tawaran / Rencana Bantuan (Status Awal: Direncanakan)' }}
                        </span>
                        <span>&rarr;</span>
                    </button>
                </div>
            </form>
        </section>

        <!-- Right Sidebar: Status Rules & Non-Duplication Notice (4 cols) -->
        <aside class="lg:col-span-4 space-y-4">
            <div class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 space-y-3">
                <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                    2 Jenis Aktivitas Pencatatan Publik
                </h2>
                <div class="space-y-2.5 text-xs text-slate-600">
                    <div class="p-3 rounded bg-blue-50/70 border border-blue-200">
                        <strong class="font-telemetry text-[11px] uppercase text-blue-900 block mb-0.5">
                            A. Tawaran / Rencana Bantuan
                        </strong>
                        <p class="leading-relaxed text-slate-700">
                            Digunakan saat komunitas, relawan, atau warga ingin mencatat tawaran atau komitmen rencana penyaluran bantuan terhadap kebutuhan kejadian.
                        </p>
                    </div>
                    <div class="p-3 rounded bg-emerald-50/70 border border-emerald-200">
                        <strong class="font-telemetry text-[11px] uppercase text-emerald-900 block mb-0.5">
                            B. Laporan Bantuan Sudah Diberikan
                        </strong>
                        <p class="leading-relaxed text-slate-700">
                            Digunakan saat bantuan telah diserahkan di lokasi kejadian (mencatat waktu penyerahan &amp; bukti). <strong>Tidak langsung dianggap terkonfirmasi</strong> sebelum diperiksa petugas internal.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xs border border-slate-100/90 p-5 space-y-3">
                <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
                    3 Tahap Status Respons Bantuan
                </h2>
                <div class="space-y-3 text-xs text-slate-600">
                    <div class="p-3 rounded bg-amber-50 border border-amber-200">
                        <div class="mb-1">
                            <x-aid-status-badge status="direncanakan" />
                        </div>
                        <p class="text-slate-700 leading-relaxed">
                            Status awal setiap kiriman publik (baik tawaran rencana maupun laporan bantuan yang sudah diberikan) sebelum diperiksa petugas.
                        </p>
                    </div>

                    <div class="p-3 rounded bg-blue-50 border border-blue-200">
                        <div class="mb-1">
                            <x-aid-status-badge status="dalam_penyaluran" />
                        </div>
                        <p class="text-slate-700 leading-relaxed">
                            Pemberi respons atau tim pemantau menyatakan bantuan sedang dalam perjalanan atau proses distribusi di lokasi sasaran.
                        </p>
                    </div>

                    <div class="p-3 rounded bg-emerald-50 border border-emerald-200">
                        <div class="mb-1">
                            <x-aid-status-badge status="terkonfirmasi" />
                        </div>
                        <p class="text-slate-700 leading-relaxed">
                            Petugas berwenang (Operator / Admin) telah memeriksa informasi serta bukti pendukung dan mengonfirmasi penyerahan bantuan.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-4 space-y-2 text-xs text-slate-600">
                <div class="font-telemetry font-bold uppercase tracking-wider text-[#202938]">
                    Perbedaan dengan Tindak Lanjut Internal
                </div>
                <p class="leading-relaxed">
                    <strong>Tindak Lanjut Internal</strong> adalah aktivitas tim TanggapTerang dalam memverifikasi laporan dan memutakhirkan informasi kejadian. Sedangkan <strong>Respons Bantuan</strong> adalah catatan terbuka mengenai dukungan logistik/bantuan dari masyarakat, komunitas, atau organisasi.
                </p>
                <p class="leading-relaxed text-slate-500">
                    Data ini hanya mencakup respons bantuan yang dilaporkan ke dalam TanggapTerang dan tidak merepresentasikan seluruh bantuan di lapangan.
                </p>
            </div>
        </aside>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const radios = document.querySelectorAll('input[name="submission_type"]');
        const cardTawaran = document.querySelector('[data-submission-card="tawaran_rencana"]');
        const cardLaporan = document.querySelector('[data-submission-card="laporan_penyerahan"]');
        const noticeBox = document.getElementById('delivery-report-notice');
        const labelTitle = document.getElementById('label-aid-item-title');
        const labelLocation = document.getElementById('label-target-location');
        const labelDeliveredAt = document.getElementById('label-delivered-at');
        const reqDeliveredAt = document.getElementById('required-delivered-at');
        const inputDeliveredAt = document.getElementById('delivered_at');
        const hintDeliveredAt = document.getElementById('hint-delivered-at');
        const labelDesc = document.getElementById('label-aid-description');
        const labelEvidence = document.getElementById('label-evidence-url');
        const submitLabel = document.getElementById('submit-btn-label');

        const syncSubmissionMode = (mode) => {
            const isDelivered = mode === 'laporan_penyerahan';
            if (cardTawaran && cardLaporan) {
                cardTawaran.className = 'relative flex items-start gap-3 p-3.5 rounded-lg border cursor-pointer transition ' +
                    (!isDelivered ? 'border-[#173B70] bg-blue-50/50 ring-1 ring-[#173B70]' : 'border-slate-200/90 bg-white hover:bg-slate-50');
                cardLaporan.className = 'relative flex items-start gap-3 p-3.5 rounded-lg border cursor-pointer transition ' +
                    (isDelivered ? 'border-emerald-600 bg-emerald-50/50 ring-1 ring-emerald-600' : 'border-slate-200/90 bg-white hover:bg-slate-50');
            }
            if (noticeBox) noticeBox.classList.toggle('hidden', !isDelivered);
            if (labelTitle) labelTitle.textContent = isDelivered ? 'Jenis Bantuan yang Sudah Diserahkan' : 'Jenis Bantuan yang Direncanakan / Ditawarkan';
            if (labelLocation) labelLocation.textContent = isDelivered ? 'Lokasi Penyerahan Bantuan di Lapangan' : 'Lokasi atau Wilayah Tujuan Penyaluran';
            if (labelDeliveredAt) labelDeliveredAt.textContent = isDelivered ? 'Waktu Penyerahan Bantuan di Lokasi' : 'Estimasi / Jadwal Penyaluran (Opsional)';
            if (reqDeliveredAt) reqDeliveredAt.classList.toggle('hidden', !isDelivered);
            if (inputDeliveredAt) inputDeliveredAt.required = isDelivered;
            if (hintDeliveredAt) hintDeliveredAt.textContent = isDelivered
                ? 'Wajib diisi sesuai waktu bantuan diserahkan di lokasi kejadian.'
                : 'Opsional jika baru berupa tawaran atau rencana penyaluran.';
            if (labelDesc) labelDesc.textContent = isDelivered
                ? 'Keterangan Penyerahan & Penerima di Lokasi (Opsional)'
                : 'Keterangan Rencana Penyaluran (Opsional)';
            if (labelEvidence) labelEvidence.textContent = isDelivered
                ? 'Tautan Bukti Dokumentasi Penyerahan Bantuan (Disarankan)'
                : 'Tautan Publikasi / Dokumentasi Rencana Bantuan (Opsional)';
            if (submitLabel) submitLabel.textContent = isDelivered
                ? 'Kirim Laporan Bantuan Sudah Diberikan (Status Awal: Direncanakan)'
                : 'Catat Tawaran / Rencana Bantuan (Status Awal: Direncanakan)';
        };

        radios.forEach((radio) => {
            radio.addEventListener('change', (e) => {
                syncSubmissionMode(e.target.value);
            });
        });

        const exampleBtns = document.querySelectorAll('.btn-example-aid');
        const aidInput = document.getElementById('aid_item_title');
        exampleBtns.forEach((btn) => {
            btn.addEventListener('click', () => {
                const fillVal = btn.getAttribute('data-fill');
                if (aidInput && fillVal) {
                    aidInput.value = fillVal;
                    aidInput.focus();
                }
            });
        });
    });
</script>
@endpush
