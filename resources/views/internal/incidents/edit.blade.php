@extends('layouts.internal', [
    'title' => 'Kelola Kejadian: ' . $incident->title . ' — TanggapTerang',
    'internalMenu' => 'incidents',
])

@php
    $user = auth()->user();
@endphp

@section('content')
    <!-- Top Bar Navigation -->
    <nav class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('internal.incidents.index') }}"
           class="font-telemetry text-xs font-bold text-slate-600 hover:text-[#0F172A]">
            &larr; Kembali ke Daftar Kejadian
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('public.incidents.show', $incident->slug) }}"
               target="_blank"
               class="px-3 py-1.5 rounded border border-[#CBD5E1] bg-white hover:bg-slate-50 text-[#0F172A] font-telemetry text-xs font-bold uppercase">
                Lihat Halaman Publik &nearr;
            </a>
        </div>
    </nav>

    <!-- Header Summary with Dual Separated Statuses -->
    <section class="bg-white border border-[#E2E8F0] rounded-lg p-5 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex flex-wrap items-center gap-2">
                @if($incident->is_simulation)
                    <span class="px-2 py-0.5 rounded bg-[#0F172A] text-white font-telemetry text-[10px] font-bold uppercase">
                        Data Simulasi
                    </span>
                @endif
                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-800 font-telemetry text-xs font-bold uppercase">
                    Slug: {{ $incident->slug }}
                </span>
                <x-severity-badge :severity="$incident->severity" />
            </div>
            <span class="font-telemetry text-xs text-slate-500">
                Waktu Kejadian: <strong>{{ $incident->occurred_at?->format('d M Y, H:i') }} WIB</strong>
            </span>
        </div>

        <h1 class="text-xl sm:text-2xl font-extrabold text-[#0F172A]">
            {{ $incident->title }}
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-[#F8FAFC] border-l-4 border-[#2563EB] border-y border-r border-[#E2E8F0] rounded-lg p-3.5 flex items-center justify-between">
                <div>
                    <span class="font-telemetry text-[10px] font-bold uppercase text-slate-500 block">
                        1. Status Verifikasi Informasi
                    </span>
                    <span class="text-xs text-slate-600">Dikelola oleh Verifikator / Admin</span>
                </div>
                <x-verification-badge :status="$incident->verification_status" />
            </div>

            <div class="bg-[#F8FAFC] border-l-4 border-[#059669] border-y border-r border-[#E2E8F0] rounded-lg p-3.5 flex items-center justify-between">
                <div>
                    <span class="font-telemetry text-[10px] font-bold uppercase text-slate-500 block">
                        2. Status Tindak Lanjut Tim
                    </span>
                    <span class="text-xs text-slate-600">Dikelola oleh Operator / Admin</span>
                </div>
                <x-followup-badge :status="$incident->follow_up_status" />
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        <!-- Left Column (7 cols): Incident Info Form, Connected Reports, Sources, & Complete Audit Trail -->
        <div class="lg:col-span-7 space-y-5">
            <!-- Section 1: Edit Informasi Kejadian & Tautkan Laporan (Operator & Admin) -->
            <section class="bg-white border border-[#E2E8F0] rounded-lg p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                            Informasi Kejadian &amp; Penautan Laporan Warga
                        </h2>
                        <p class="text-xs text-slate-500">Wewenang perubahan data kejadian: Operator &amp; Admin</p>
                    </div>
                    <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase {{ $user->canManageOperations() ? 'bg-emerald-100 text-[#059669]' : 'bg-slate-100 text-slate-500' }}">
                        {{ $user->canManageOperations() ? 'Dapat Diedit' : 'Mode Baca' }}
                    </span>
                </div>

                @if($user->canManageOperations())
                    <form method="POST" action="{{ route('internal.incidents.update', $incident) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="title" class="block font-telemetry text-xs font-bold uppercase text-[#0F172A] mb-1">
                                Judul Kejadian <span class="text-[#DC2626]">*</span>
                            </label>
                            <input type="text"
                                   id="title"
                                   name="title"
                                   value="{{ old('title', $incident->title) }}"
                                   required
                                   class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A]">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label for="incident_type" class="block font-telemetry text-xs font-bold uppercase text-[#0F172A] mb-1">
                                    Jenis Kejadian
                                </label>
                                <select id="incident_type"
                                        name="incident_type"
                                        class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#0F172A]">
                                    @foreach($categories as $key => $label)
                                        <option value="{{ $key }}" @selected(old('incident_type', $incident->incident_type) === $key)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="severity" class="block font-telemetry text-xs font-bold uppercase text-[#0F172A] mb-1">
                                    Tingkat Dampak
                                </label>
                                <select id="severity"
                                        name="severity"
                                        class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#0F172A]">
                                    @foreach(\App\Enums\IncidentSeverity::cases() as $sCase)
                                        <option value="{{ $sCase->value }}" @selected(old('severity', $incident->severity?->value ?? $incident->severity) === $sCase->value)>
                                            {{ $sCase->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="occurred_at" class="block font-telemetry text-xs font-bold uppercase text-[#0F172A] mb-1">
                                    Waktu Kejadian
                                </label>
                                <input type="datetime-local"
                                       id="occurred_at"
                                       name="occurred_at"
                                       value="{{ old('occurred_at', $incident->occurred_at?->format('Y-m-d\TH:i')) }}"
                                       required
                                       class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#0F172A]">
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block font-telemetry text-xs font-bold uppercase text-[#0F172A] mb-1">
                                Deskripsi Kejadian <span class="text-[#DC2626]">*</span>
                            </label>
                            <textarea id="description"
                                      name="description"
                                      rows="4"
                                      required
                                      class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-sm text-[#0F172A]">{{ old('description', $incident->description) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label for="location_name" class="block font-telemetry text-xs font-bold uppercase text-[#0F172A] mb-1">
                                    Nama Lokasi <span class="text-[#DC2626]">*</span>
                                </label>
                                <input type="text"
                                       id="location_name"
                                       name="location_name"
                                       value="{{ old('location_name', $incident->location_name) }}"
                                       required
                                       class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#0F172A]">
                            </div>

                            <div>
                                <label for="latitude" class="block font-telemetry text-xs font-bold uppercase text-[#0F172A] mb-1">
                                    Latitude
                                </label>
                                <input type="text"
                                       id="latitude"
                                       name="latitude"
                                       value="{{ old('latitude', $incident->latitude) }}"
                                       class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg font-telemetry text-xs text-[#0F172A]">
                            </div>

                            <div>
                                <label for="longitude" class="block font-telemetry text-xs font-bold uppercase text-[#0F172A] mb-1">
                                    Longitude
                                </label>
                                <input type="text"
                                       id="longitude"
                                       name="longitude"
                                       value="{{ old('longitude', $incident->longitude) }}"
                                       class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg font-telemetry text-xs text-[#0F172A]">
                            </div>
                        </div>

                        @if($unlinkedReports->isNotEmpty())
                            <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-3.5 space-y-2">
                                <span class="font-telemetry text-[11px] font-bold uppercase text-[#0F172A] block">
                                    Tautkan Laporan Masyarakat Mandiri ke Kejadian Ini:
                                </span>
                                <div class="max-h-40 overflow-y-auto space-y-1.5">
                                    @foreach($unlinkedReports as $rep)
                                        <label class="flex items-start gap-2 p-2 bg-white border border-slate-200 rounded text-xs cursor-pointer">
                                            <input type="checkbox" name="report_ids[]" value="{{ $rep->id }}" class="mt-0.5">
                                            <span>
                                                <strong class="font-telemetry">{{ $rep->ticket_number }}</strong> — {{ $rep->title }} ({{ $rep->location_name }})
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="px-5 py-2.5 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white font-telemetry text-xs font-bold uppercase tracking-wider cursor-pointer">
                                Simpan Perubahan Informasi Kejadian
                            </button>
                        </div>
                    </form>
                @else
                    <div class="space-y-2 text-sm text-slate-700">
                        <p>{{ $incident->description }}</p>
                        <p class="font-telemetry text-xs text-slate-500">
                            Lokasi: {{ $incident->location_name }} | Koordinat: {{ $incident->latitude ?? '-' }}, {{ $incident->longitude ?? '-' }}
                        </p>
                    </div>
                @endif
            </section>

            <!-- Section 2: Connected Citizen Reports -->
            <section class="bg-white border border-[#E2E8F0] rounded-lg p-5 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                        Laporan Masyarakat Terhubung ({{ $incident->reports->count() }})
                    </h2>
                    <a href="{{ route('internal.reports.index') }}" class="font-telemetry text-xs font-bold text-[#2563EB] hover:underline">
                        Kelola Semua Laporan &rarr;
                    </a>
                </div>

                @forelse($incident->reports as $rep)
                    <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-3.5 flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('internal.reports.show', $rep) }}"
                                   class="px-2 py-0.5 rounded bg-[#0F172A] text-white font-telemetry text-xs font-bold">
                                    {{ $rep->ticket_number }}
                                </a>
                                <x-verification-badge :status="$rep->verification_status" />
                            </div>
                            <div class="text-sm font-bold text-[#0F172A] mt-1">{{ $rep->title }}</div>
                            <div class="text-xs text-slate-500">
                                Pelapor: {{ $rep->reporter_name ?: 'Anonim' }} ({{ $rep->reporter_contact ?: 'Tanpa Kontak' }})
                            </div>
                        </div>
                        <a href="{{ route('internal.reports.show', $rep) }}"
                           class="px-3 py-1.5 rounded border border-slate-300 bg-white hover:bg-slate-100 font-telemetry text-xs font-bold">
                            Detail Laporan &rarr;
                        </a>
                    </div>
                @empty
                    <p class="text-xs text-slate-500">Belum ada laporan masyarakat yang dihubungkan ke kejadian ini.</p>
                @endforelse
            </section>

            <!-- Section 3: Connected Open Sources -->
            <section class="bg-white border border-[#E2E8F0] rounded-lg p-5 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                        Sumber Informasi Terbuka Terhubung ({{ $incident->incidentSources->count() }})
                    </h2>
                </div>

                @forelse($incident->incidentSources as $incSource)
                    <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-3.5 space-y-1">
                        <div class="flex items-center justify-between">
                            @if($incSource->source)
                                <x-source-badge :type="$incSource->source->source_type" :isSimulation="(bool) $incSource->source->is_simulation" />
                            @endif
                            <span class="font-telemetry text-[11px] text-slate-500">
                                {{ $incSource->published_at?->format('d M Y, H:i') }}
                            </span>
                        </div>
                        <div class="text-sm font-bold text-[#0F172A]">{{ $incSource->headline }}</div>
                        <div class="text-xs text-slate-600">{{ $incSource->source?->name }} — {{ $incSource->notes }}</div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500">Belum ada sumber informasi terbuka yang ditautkan.</p>
                @endforelse
            </section>

            <!-- Section 3B: Kelola & Konfirmasi Respons Bantuan Masyarakat -->
            <section class="bg-white border border-[#E2E8F0] rounded-lg p-5 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                            Respons Bantuan Masyarakat Tercatat ({{ $incident->aidResponses->count() }})
                        </h2>
                        <p class="text-xs text-slate-500">
                            Pemisahan tegas antara <strong>Pemeriksaan &amp; Validasi Informasi</strong> (Verifikator &amp; Admin) dan <strong>Status Penyaluran &amp; Konfirmasi Lapangan</strong> (Operator &amp; Admin).
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-1.5 font-telemetry text-[10px] font-bold uppercase">
                        <span class="px-2 py-0.5 rounded bg-blue-50 text-[#2563EB] border border-blue-200">
                            Verifikasi: Verifikator &amp; Admin
                        </span>
                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-[#059669] border border-emerald-200">
                            Operasional: Operator &amp; Admin
                        </span>
                    </div>
                </div>

                @forelse($incident->aidResponses as $aid)
                    @php
                        $aidStatusVal = $aid->status instanceof \BackedEnum ? $aid->status->value : (string) $aid->status;
                        $aidVerifVal = $aid->verification_status instanceof \BackedEnum ? $aid->verification_status->value : (string) $aid->verification_status;
                        $isDeliveredSubmission = $aid->isReportedDelivered();
                        $isAidVerified = $aid->isVerified();
                    @endphp
                    <div class="bg-[#F8FAFC] border border-[#CBD5E1] rounded-lg p-4 space-y-3.5">
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 pb-2.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2 py-0.5 rounded bg-slate-900 text-white font-telemetry text-xs font-bold">
                                    {{ $aid->reference_code }}
                                </span>
                                <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase {{ $isDeliveredSubmission ? 'bg-indigo-50 text-indigo-900 border border-indigo-200' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                                    {{ $aid->submissionTypeLabel() }}
                                </span>
                                <span class="text-xs font-bold text-[#0F172A]">
                                    {{ $aid->aid_item_title }} ({{ $aid->quantity_label }})
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <div class="flex items-center gap-1">
                                    <span class="font-telemetry text-[9px] uppercase font-bold text-slate-500">Pemeriksaan:</span>
                                    <x-verification-badge :status="$aid->verification_status" />
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="font-telemetry text-[9px] uppercase font-bold text-slate-500">Operasional:</span>
                                    <x-aid-status-badge :status="$aid->status" />
                                </div>
                            </div>
                        </div>

                        <!-- Data Informasi Bantuan -->
                        <div class="text-xs text-slate-600 space-y-1">
                            <div>
                                <strong>Jenis Aktivitas:</strong> {{ $aid->submissionTypeLabel() }}
                                @if($aid->report)
                                    • <strong>Rujukan Tiket Laporan:</strong> <span class="font-telemetry text-blue-700 font-bold">{{ $aid->report->ticket_number }}</span>
                                @endif
                            </div>
                            <div>
                                <strong>Organisasi/Komunitas:</strong> {{ $aid->organization_name }}
                                <span class="text-slate-500">({{ $aid->allow_public_name ? 'Tampil Publik' : 'Anonim di Publik' }})</span>
                                • <strong>Kategori Kebutuhan:</strong> {{ \App\Models\AidResponse::needCategories()[$aid->need_category] ?? $aid->need_category }}
                            </div>
                            <div>
                                <strong>Kontak Internal (Rahasia):</strong> {{ $aid->contact_name ?: '-' }} / {{ $aid->contact_info ?: '-' }}
                            </div>
                            <div>
                                <strong>Lokasi Penyaluran / Tujuan:</strong> {{ $aid->target_location }}
                            </div>
                            @if($aid->aid_description)
                                <div>
                                    <strong>Keterangan Pemberi Respons:</strong> {{ $aid->aid_description }}
                                </div>
                            @endif
                            <div class="font-telemetry text-[11px] text-slate-500 pt-0.5">
                                @if($aid->delivered_at)
                                    <span class="text-indigo-800 font-semibold">Waktu Penyerahan (Dilaporkan): {{ $aid->delivered_at->format('d M Y, H:i') }} WIB</span> •
                                @endif
                                Dicatat: {{ $aid->recorded_at?->format('d M Y, H:i') }} WIB
                                @if($aid->verified_at)
                                    • Diperiksa: {{ $aid->verified_at->format('d M Y, H:i') }} WIB ({{ $aid->verifiedBy?->name ?? 'Verifikator' }})
                                @endif
                                @if($aid->confirmed_at)
                                    • Dikonfirmasi: {{ $aid->confirmed_at->format('d M Y, H:i') }} WIB ({{ $aid->confirmedBy?->name ?? 'Operator' }})
                                @endif
                            </div>
                        </div>

                        <!-- 2 Panel Terpisah: Panel Verifikasi dan Panel Operasional -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 pt-2">
                            <!-- PANEL 1: PEMERIKSAAN & VERIFIKASI INFORMASI (Verifikator & Admin) -->
                            <div class="bg-white border {{ $user->canVerify() ? 'border-blue-300' : 'border-slate-200' }} rounded-lg p-3.5 space-y-2.5">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <span class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-blue-900">
                                        1. Pemeriksaan &amp; Verifikasi Informasi
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded font-telemetry text-[9px] font-bold uppercase {{ $user->canVerify() ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $user->canVerify() ? 'Bisa Diubah' : 'Baca Saja' }}
                                    </span>
                                </div>

                                @if($user->canVerify())
                                    <form method="POST" action="{{ route('internal.incidents.aid-responses.verification.update', [$incident, $aid]) }}" class="space-y-2">
                                        @csrf
                                        @method('PUT')

                                        <div>
                                            <label class="block font-telemetry text-[10px] font-bold uppercase text-slate-600 mb-1">
                                                Status Pemeriksaan <span class="text-[#DC2626]">*</span>
                                            </label>
                                            <select name="verification_status" class="w-full min-h-[36px] px-2.5 py-1 bg-white border border-[#CBD5E1] rounded text-xs text-[#0F172A] focus:outline-none focus:border-blue-500">
                                                @foreach(\App\Enums\VerificationStatus::cases() as $vCase)
                                                    <option value="{{ $vCase->value }}" @selected($aidVerifVal === $vCase->value)>
                                                        {{ $vCase->label() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block font-telemetry text-[10px] font-bold uppercase text-slate-600 mb-1">
                                                Catatan Verifikator <span class="text-[#DC2626]">*</span>
                                            </label>
                                            <textarea name="verification_notes"
                                                      rows="2"
                                                      required
                                                      placeholder="Catatan hasil verifikasi keabsahan informasi pemberi bantuan..."
                                                      class="w-full px-2.5 py-1.5 bg-white border border-[#CBD5E1] rounded text-xs text-[#0F172A] focus:outline-none focus:border-blue-500">{{ old('verification_notes', $aid->verification_notes) }}</textarea>
                                        </div>

                                        <button type="submit" class="w-full min-h-[34px] px-3 py-1 rounded bg-[#2563EB] hover:bg-blue-700 text-white font-telemetry text-[10px] font-bold uppercase cursor-pointer transition">
                                            Simpan Verifikasi Informasi
                                        </button>
                                    </form>
                                @else
                                    <div class="text-xs text-slate-600 space-y-1 pt-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-slate-500">Status Pemeriksaan:</span>
                                            <x-verification-badge :status="$aid->verification_status" />
                                        </div>
                                        <div>
                                            <strong>Catatan Verifikator:</strong>
                                            <p class="text-slate-700 italic mt-0.5">{{ $aid->verification_notes ?: 'Belum ada catatan verifikasi khusus.' }}</p>
                                        </div>
                                        @if($aid->verifiedBy)
                                            <div class="text-[11px] text-slate-500 font-telemetry">
                                                Diverifikasi oleh: {{ $aid->verifiedBy->name }} ({{ $aid->verified_at?->format('d M Y, H:i') }} WIB)
                                            </div>
                                        @endif
                                        <p class="text-[10px] text-slate-400 font-telemetry italic pt-1">
                                            Wewenang penetapan status pemeriksaan berada pada Verifikator &amp; Admin.
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <!-- PANEL 2: PENGELOLAAN STATUS OPERASIONAL BANTUAN (Operator & Admin) -->
                            <div class="bg-white border {{ $user->canManageOperations() ? 'border-emerald-300' : 'border-slate-200' }} rounded-lg p-3.5 space-y-2.5">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <span class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-emerald-900">
                                        2. Status Penyaluran &amp; Konfirmasi Operasional
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded font-telemetry text-[9px] font-bold uppercase {{ $user->canManageOperations() ? 'bg-emerald-100 text-[#059669]' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $user->canManageOperations() ? 'Bisa Diubah' : 'Baca Saja' }}
                                    </span>
                                </div>

                                @if($user->canManageOperations())
                                    @if(!$isAidVerified)
                                        <div class="p-2 bg-amber-50 border border-amber-200 rounded text-[11px] text-amber-900">
                                            <strong>Pemberitahuan:</strong> Status <em>Penyerahan Dikonfirmasi</em> hanya dapat ditetapkan setelah status pemeriksaan dinyatakan <strong>Terverifikasi</strong> oleh Tim Verifikator.
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('internal.incidents.aid-responses.update', [$incident, $aid]) }}" class="space-y-2">
                                        @csrf
                                        @method('PUT')

                                        <div>
                                            <label class="block font-telemetry text-[10px] font-bold uppercase text-slate-600 mb-1">
                                                Status Operasional <span class="text-[#DC2626]">*</span>
                                            </label>
                                            <select name="status" class="w-full min-h-[36px] px-2.5 py-1 bg-white border border-[#CBD5E1] rounded text-xs text-[#0F172A] focus:outline-none focus:border-emerald-500">
                                                @foreach(\App\Enums\AidResponseStatus::cases() as $aCase)
                                                    <option value="{{ $aCase->value }}"
                                                            @selected($aidStatusVal === $aCase->value)
                                                            @disabled($aCase === \App\Enums\AidResponseStatus::Terkonfirmasi && !$isAidVerified)>
                                                        {{ $aCase->label() }}
                                                        @if($aCase === \App\Enums\AidResponseStatus::Terkonfirmasi && !$isAidVerified)
                                                            (Perlu Verifikasi Verifikator)
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block font-telemetry text-[10px] font-bold uppercase text-slate-600 mb-1">
                                                Catatan Konfirmasi / Lapangan
                                            </label>
                                            <input type="text"
                                                   name="confirmation_notes"
                                                   value="{{ old('confirmation_notes', $aid->confirmation_notes) }}"
                                                   placeholder="Catatan serah terima / perkembangan lapangan..."
                                                   class="w-full min-h-[36px] px-2.5 py-1 bg-white border border-[#CBD5E1] rounded text-xs text-[#0F172A] focus:outline-none focus:border-emerald-500">
                                        </div>

                                        <div>
                                            <label class="block font-telemetry text-[10px] font-bold uppercase text-slate-600 mb-1">
                                                Tautan Bukti Dokumentasi (URL Valid)
                                            </label>
                                            <input type="url"
                                                   name="evidence_url"
                                                   value="{{ old('evidence_url', $aid->evidence_url) }}"
                                                   placeholder="https://contoh-sumber.id/dokumentasi-penyerahan"
                                                   class="w-full min-h-[36px] px-2.5 py-1 bg-white border border-[#CBD5E1] rounded text-xs text-[#0F172A] focus:outline-none focus:border-emerald-500">
                                        </div>

                                        <button type="submit" class="w-full min-h-[34px] px-3 py-1 rounded bg-[#0F172A] hover:bg-slate-800 text-white font-telemetry text-[10px] font-bold uppercase cursor-pointer transition">
                                            Simpan Status Operasional
                                        </button>
                                    </form>
                                @else
                                    <div class="text-xs text-slate-600 space-y-1.5 pt-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-slate-500">Status Operasional:</span>
                                            <x-aid-status-badge :status="$aid->status" />
                                        </div>
                                        @if($aid->confirmation_notes)
                                            <div>
                                                <strong>Catatan Lapangan:</strong>
                                                <p class="text-slate-700 italic mt-0.5">{{ $aid->confirmation_notes }}</p>
                                            </div>
                                        @endif
                                        @if($aid->evidence_url)
                                            <div>
                                                <strong>Tautan Dokumentasi:</strong>
                                                <a href="{{ $aid->evidence_url }}" target="_blank" rel="noopener noreferrer" class="text-[#2563EB] underline break-all block">{{ $aid->evidence_url }}</a>
                                            </div>
                                        @endif
                                        @if($aid->confirmedBy)
                                            <div class="text-[11px] text-slate-500 font-telemetry">
                                                Dikonfirmasi oleh: {{ $aid->confirmedBy->name }} ({{ $aid->confirmed_at?->format('d M Y, H:i') }} WIB)
                                            </div>
                                        @endif
                                        <p class="text-[10px] text-slate-400 font-telemetry italic pt-1">
                                            Wewenang pengelolaan status operasional berada pada Operator &amp; Admin.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500">Belum ada respons bantuan masyarakat yang tercatat pada kejadian ini.</p>
                @endforelse

                <div class="bg-[#FFFBEB] border border-[#FCD34D] rounded-lg p-3 text-[11px] text-[#92400E]">
                    <strong class="font-telemetry uppercase">Catatan Akuntabilitas:</strong>
                    Tautan dokumentasi yang diinput tidak otomatis membuktikan bantuan telah diterima; status yang disimpan tetap merupakan catatan konfirmasi administratif oleh petugas TanggapTerang.
                </div>
            </section>

            <!-- Section 4: Complete Internal Audit Trail -->
            <section class="bg-white border border-[#E2E8F0] rounded-lg p-5 space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                        Riwayat Verifikasi, Tindak Lanjut, &amp; Respons Bantuan (Mode Internal)
                    </h2>
                </div>

                <x-audit-timeline
                    :verificationHistories="$incident->verificationHistories"
                    :followUpRecords="$incident->followUpRecords"
                    :aidResponses="$incident->aidResponses"
                    :isInternal="true"
                />
            </section>
        </div>

        <!-- Right Column (5 cols): Role-Gated Action Panels -->
        <div class="lg:col-span-5 space-y-5">
            <!-- Panel A: Status Verifikasi Kejadian (Verifikator & Admin) -->
            <section class="bg-white border border-[#E2E8F0] rounded-lg p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#2563EB]">
                            1. Kelola Status Verifikasi Kejadian
                        </h2>
                        <p class="text-xs text-slate-500">Wewenang: Verifikator &amp; Admin</p>
                    </div>
                    <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase {{ $user->canVerify() ? 'bg-blue-100 text-[#2563EB]' : 'bg-slate-100 text-slate-500' }}">
                        {{ $user->canVerify() ? 'Diizinkan' : 'Khusus Verifikator/Admin' }}
                    </span>
                </div>

                @if($user->canVerify())
                    <form method="POST"
                          action="{{ route('internal.incidents.verification.update', $incident) }}"
                          onsubmit="return confirm('Konfirmasi: Perbarui status verifikasi kejadian ini?');"
                          class="space-y-3">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="verification_status_select" class="block font-telemetry text-[11px] font-bold uppercase text-slate-700 mb-1">
                                Status Verifikasi Kejadian <span class="text-[#DC2626]">*</span>
                            </label>
                            <select id="verification_status_select"
                                    name="verification_status"
                                    required
                                    class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#0F172A]">
                                @foreach(\App\Enums\VerificationStatus::cases() as $vCase)
                                    <option value="{{ $vCase->value }}" @selected(($incident->verification_status?->value ?? $incident->verification_status) === $vCase->value)>
                                        {{ $vCase->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="verification_reason" class="block font-telemetry text-[11px] font-bold uppercase text-slate-700 mb-1">
                                Alasan / Catatan Verifikasi Internal <span class="text-[#DC2626]">*</span>
                            </label>
                            <textarea id="verification_reason"
                                      name="reason"
                                      rows="2"
                                      required
                                      placeholder="Catat dasar perubahan status verifikasi..."
                                      class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#0F172A]"></textarea>
                        </div>

                        <button type="submit"
                                class="w-full px-4 py-2.5 rounded-lg bg-[#2563EB] hover:bg-blue-700 text-white font-telemetry text-xs font-bold uppercase tracking-wider cursor-pointer">
                            Perbarui Status Verifikasi
                        </button>
                    </form>
                @else
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-3.5 text-xs text-slate-600">
                        Role <strong>{{ $user->role?->label() }}</strong> tidak diizinkan mengubah status verifikasi kejadian. Hanya <strong>Verifikator</strong> dan <strong>Admin</strong> yang memiliki akses ini.
                    </div>
                @endif
            </section>

            <!-- Panel B: Status Tindak Lanjut Kejadian (Operator & Admin) -->
            <section class="bg-white border border-[#E2E8F0] rounded-lg p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#059669]">
                            2. Kelola Status Tindak Lanjut
                        </h2>
                        <p class="text-xs text-slate-500">Wewenang: Operator &amp; Admin</p>
                    </div>
                    <span class="px-2 py-0.5 rounded font-telemetry text-[10px] font-bold uppercase {{ $user->canManageOperations() ? 'bg-emerald-100 text-[#059669]' : 'bg-slate-100 text-slate-500' }}">
                        {{ $user->canManageOperations() ? 'Diizinkan' : 'Khusus Operator/Admin' }}
                    </span>
                </div>

                @if($user->canManageOperations())
                    <form method="POST"
                          action="{{ route('internal.incidents.follow-up.update', $incident) }}"
                          onsubmit="return confirm('Konfirmasi: Perbarui status tindak lanjut kejadian ini?');"
                          class="space-y-3">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="follow_up_status_select" class="block font-telemetry text-[11px] font-bold uppercase text-slate-700 mb-1">
                                Status Tindak Lanjut Kejadian <span class="text-[#DC2626]">*</span>
                            </label>
                            <select id="follow_up_status_select"
                                    name="follow_up_status"
                                    required
                                    class="w-full min-h-[42px] px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#0F172A]">
                                @foreach(\App\Enums\FollowUpStatus::cases() as $fCase)
                                    <option value="{{ $fCase->value }}" @selected(($incident->follow_up_status?->value ?? $incident->follow_up_status) === $fCase->value)>
                                        {{ $fCase->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="follow_up_notes" class="block font-telemetry text-[11px] font-bold uppercase text-slate-700 mb-1">
                                Catatan Perubahan Status Tindak Lanjut <span class="text-[#DC2626]">*</span>
                            </label>
                            <textarea id="follow_up_notes"
                                      name="notes"
                                      rows="2"
                                      required
                                      placeholder="Jelaskan perkembangan tindak lanjut saat ini..."
                                      class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#0F172A]"></textarea>
                        </div>

                        <button type="submit"
                                class="w-full px-4 py-2.5 rounded-lg bg-[#059669] hover:bg-emerald-700 text-white font-telemetry text-xs font-bold uppercase tracking-wider cursor-pointer">
                            Perbarui Status Tindak Lanjut
                        </button>
                    </form>
                @else
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-3.5 text-xs text-slate-600">
                        Role <strong>{{ $user->role?->label() }}</strong> tidak diizinkan mengubah status tindak lanjut kejadian. Hanya <strong>Operator</strong> dan <strong>Admin</strong> yang memiliki akses ini.
                    </div>
                @endif
            </section>

            <!-- Panel C: Tambah Catatan Tindak Lanjut Baru (Operator & Admin) -->
            @if($user->canManageOperations())
                <section class="bg-white border border-[#E2E8F0] rounded-lg p-5 space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                            3. Tambah Catatan Tindak Lanjut Baru
                        </h2>
                        <p class="text-xs text-slate-500">Mencatat perkembangan peninjauan informasi atau dokumentasi tim.</p>
                    </div>

                    <form method="POST" action="{{ route('internal.incidents.follow-ups.store', $incident) }}" class="space-y-3">
                        @csrf

                        <div>
                            <label for="action_title" class="block font-telemetry text-[11px] font-bold uppercase text-slate-700 mb-1">
                                Judul Tindakan / Pembaruan <span class="text-[#DC2626]">*</span>
                            </label>
                            <input type="text"
                                   id="action_title"
                                   name="action_title"
                                   required
                                   placeholder="Contoh: Pemutakhiran Peta Titik Genangan & Jalur Alternatif"
                                   class="w-full min-h-[40px] px-3 py-1.5 bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#0F172A]">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="action_date" class="block font-telemetry text-[11px] font-bold uppercase text-slate-700 mb-1">
                                    Waktu Tindakan <span class="text-[#DC2626]">*</span>
                                </label>
                                <input type="datetime-local"
                                       id="action_date"
                                       name="action_date"
                                       value="{{ now()->format('Y-m-d\TH:i') }}"
                                       required
                                       class="w-full min-h-[40px] px-3 py-1.5 bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#0F172A]">
                            </div>

                            <div>
                                <label for="action_status" class="block font-telemetry text-[11px] font-bold uppercase text-slate-700 mb-1">
                                    Status Tahapan <span class="text-[#DC2626]">*</span>
                                </label>
                                <select id="action_status"
                                        name="status"
                                        class="w-full min-h-[40px] px-3 py-1.5 bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#0F172A]">
                                    <option value="sedang_ditindaklanjuti">Sedang Ditindaklanjuti</option>
                                    <option value="dipantau">Dipantau</option>
                                    <option value="selesai">Selesai</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="record_notes" class="block font-telemetry text-[11px] font-bold uppercase text-slate-700 mb-1">
                                Uraian Catatan Tindak Lanjut <span class="text-[#DC2626]">*</span>
                            </label>
                            <textarea id="record_notes"
                                      name="notes"
                                      rows="2"
                                      required
                                      placeholder="Rincian tindak lanjut tim TanggapTerang..."
                                      class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#0F172A]"></textarea>
                        </div>

                        <div>
                            <label for="record_evidence_url" class="block font-telemetry text-[11px] font-bold uppercase text-slate-700 mb-1">
                                URL Dokumentasi (Opsional)
                            </label>
                            <input type="url"
                                   id="record_evidence_url"
                                   name="evidence_url"
                                   placeholder="https://contoh-sumber.id/dokumentasi"
                                   class="w-full min-h-[40px] px-3 py-1.5 bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#0F172A]">
                        </div>

                        <button type="submit"
                                class="w-full px-4 py-2.5 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white font-telemetry text-xs font-bold uppercase tracking-wider cursor-pointer">
                            + Simpan Catatan Tindak Lanjut
                        </button>
                    </form>
                </section>

                <!-- Panel D: Tambah Sumber Informasi Terbuka (Operator & Admin) -->
                <section class="bg-white border border-[#E2E8F0] rounded-lg p-5 space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A]">
                            4. Tambah Sumber Informasi Terbuka
                        </h2>
                        <p class="text-xs text-slate-500">Tautkan rujukan berita, rilis terbuka, atau kanal komunitas.</p>
                    </div>

                    <form method="POST" action="{{ route('internal.incidents.sources.store', $incident) }}" class="space-y-3">
                        @csrf

                        <div>
                            <label for="source_id" class="block font-telemetry text-[11px] font-bold uppercase text-slate-700 mb-1">
                                Pilih Sumber Terdaftar
                            </label>
                            <select id="source_id"
                                    name="source_id"
                                    class="w-full min-h-[40px] px-3 py-1.5 bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#0F172A]">
                                <option value="">-- Pilih Sumber atau Buat Baru di Bawah --</option>
                                @foreach($availableSources as $src)
                                    <option value="{{ $src->id }}">{{ $src->name }} ({{ $src->source_type?->label((bool) $src->is_simulation) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <div>
                                <label for="new_source_name" class="block font-telemetry text-[10px] font-bold uppercase text-slate-500 mb-1">
                                    Atau Nama Sumber Baru
                                </label>
                                <input type="text"
                                       id="new_source_name"
                                       name="new_source_name"
                                       placeholder="Nama media/instansi terbuka"
                                       class="w-full min-h-[38px] px-3 py-1.5 bg-white border border-[#CBD5E1] rounded text-xs text-[#0F172A]">
                            </div>
                            <div>
                                <label for="new_source_type" class="block font-telemetry text-[10px] font-bold uppercase text-slate-500 mb-1">
                                    Kategori Sumber Baru
                                </label>
                                <select id="new_source_type"
                                        name="new_source_type"
                                        class="w-full min-h-[38px] px-3 py-1.5 bg-white border border-[#CBD5E1] rounded text-xs text-[#0F172A]">
                                    @foreach(\App\Enums\SourceType::cases() as $stCase)
                                        <option value="{{ $stCase->value }}">{{ $stCase->label((bool) $incident->is_simulation) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="source_headline" class="block font-telemetry text-[11px] font-bold uppercase text-slate-700 mb-1">
                                Judul Rujukan / Headline <span class="text-[#DC2626]">*</span>
                            </label>
                            <input type="text"
                                   id="source_headline"
                                   name="headline"
                                   required
                                   placeholder="Contoh: [Simulasi] Rilis Pemantauan Debit Sungai Hulu"
                                   class="w-full min-h-[40px] px-3 py-1.5 bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#0F172A]">
                        </div>

                        <div>
                            <label for="source_reference_url" class="block font-telemetry text-[11px] font-bold uppercase text-slate-700 mb-1">
                                URL Referensi (Opsional)
                            </label>
                            <input type="url"
                                   id="source_reference_url"
                                   name="reference_url"
                                   placeholder="https://info-terbuka.example.org/rilis-1"
                                   class="w-full min-h-[40px] px-3 py-1.5 bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#0F172A]">
                        </div>

                        <div>
                            <label for="source_notes" class="block font-telemetry text-[11px] font-bold uppercase text-slate-700 mb-1">
                                Catatan Sumber (Opsional)
                            </label>
                            <input type="text"
                                   id="source_notes"
                                   name="notes"
                                   placeholder="Ringkasan informasi yang dikutip dari sumber..."
                                   class="w-full min-h-[40px] px-3 py-1.5 bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#0F172A]">
                        </div>

                        <button type="submit"
                                class="w-full px-4 py-2.5 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white font-telemetry text-xs font-bold uppercase tracking-wider cursor-pointer">
                            + Tautkan Sumber Informasi
                        </button>
                    </form>
                </section>
            @endif
        </div>
    </div>
@endsection
