@props([
    'name' => 'wilayah_administratif_id',
    'selectedId' => null,
])

<div class="bg-[#F8F9FC] border border-slate-100 rounded-xl p-4 space-y-3" data-wilayah-selector>
    <div class="flex items-center justify-between">
        <span class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#202938]">
            Pilih Wilayah Administratif (Opsional — 4 Tingkat)
        </span>
        <span class="font-telemetry text-[11px] text-[#788397]" data-wilayah-status>Siap memilih provinsi</span>
    </div>

    <input type="hidden" name="{{ $name }}" value="{{ old($name, $selectedId) }}" data-wilayah-hidden-input>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label class="block font-telemetry text-[11px] font-bold uppercase text-[#788397] mb-1">1. Provinsi</label>
            <select data-level="provinsi" class="w-full min-h-[44px] px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-sm text-[#202938] focus:outline-none focus:border-[#173B70]">
                <option value="">-- Pilih Provinsi --</option>
            </select>
        </div>

        <div>
            <label class="block font-telemetry text-[11px] font-bold uppercase text-[#788397] mb-1">2. Kabupaten / Kota</label>
            <select data-level="kabupaten" disabled class="w-full min-h-[44px] px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-sm text-[#202938] disabled:bg-slate-100 disabled:text-slate-400 focus:outline-none focus:border-[#173B70]">
                <option value="">-- Pilih Kabupaten/Kota --</option>
            </select>
        </div>

        <div>
            <label class="block font-telemetry text-[11px] font-bold uppercase text-[#788397] mb-1">3. Kecamatan</label>
            <select data-level="kecamatan" disabled class="w-full min-h-[44px] px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-sm text-[#202938] disabled:bg-slate-100 disabled:text-slate-400 focus:outline-none focus:border-[#173B70]">
                <option value="">-- Pilih Kecamatan --</option>
            </select>
        </div>

        <div>
            <label class="block font-telemetry text-[11px] font-bold uppercase text-[#788397] mb-1">4. Desa / Kelurahan</label>
            <select data-level="desa" disabled class="w-full min-h-[44px] px-3 py-2 bg-white border border-slate-200/90 rounded-lg text-sm text-[#202938] disabled:bg-slate-100 disabled:text-slate-400 focus:outline-none focus:border-[#173B70]">
                <option value="">-- Pilih Desa/Kelurahan --</option>
            </select>
        </div>
    </div>
</div>

@once
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-wilayah-selector]').forEach(container => {
                const hiddenInput = container.querySelector('[data-wilayah-hidden-input]');
                const statusEl = container.querySelector('[data-wilayah-status]');
                const selProv = container.querySelector('[data-level="provinsi"]');
                const selKab = container.querySelector('[data-level="kabupaten"]');
                const selKec = container.querySelector('[data-level="kecamatan"]');
                const selDesa = container.querySelector('[data-level="desa"]');

                const resetSelect = (selectEl, placeholder) => {
                    selectEl.innerHTML = `<option value="">${placeholder}</option>`;
                    selectEl.disabled = true;
                };

                const populateSelect = (selectEl, items, placeholder) => {
                    selectEl.innerHTML = `<option value="">${placeholder}</option>`;
                    items.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.nama;
                        selectEl.appendChild(opt);
                    });
                    selectEl.disabled = items.length === 0;
                };

                // Load Provinsi
                fetch('/api/wilayah/provinsi')
                    .then(r => r.json())
                    .then(res => {
                        if (res.success && Array.isArray(res.data)) {
                            populateSelect(selProv, res.data, '-- Pilih Provinsi --');
                        }
                    })
                    .catch(() => {
                        if (statusEl) statusEl.textContent = 'Pemilihan wilayah opsional';
                    });

                selProv?.addEventListener('change', () => {
                    const id = selProv.value;
                    hiddenInput.value = id || '';
                    resetSelect(selKab, '-- Pilih Kabupaten/Kota --');
                    resetSelect(selKec, '-- Pilih Kecamatan --');
                    resetSelect(selDesa, '-- Pilih Desa/Kelurahan --');
                    if (!id) return;

                    if (statusEl) statusEl.textContent = 'Memuat kabupaten/kota...';
                    fetch(`/api/wilayah/${id}/kabupaten`)
                        .then(r => r.json())
                        .then(res => {
                            if (res.success && Array.isArray(res.data)) {
                                populateSelect(selKab, res.data, '-- Pilih Kabupaten/Kota --');
                                if (statusEl) statusEl.textContent = 'Pilih kabupaten/kota';
                            }
                        });
                });

                selKab?.addEventListener('change', () => {
                    const id = selKab.value;
                    hiddenInput.value = id || selProv.value || '';
                    resetSelect(selKec, '-- Pilih Kecamatan --');
                    resetSelect(selDesa, '-- Pilih Desa/Kelurahan --');
                    if (!id) return;

                    if (statusEl) statusEl.textContent = 'Memuat kecamatan...';
                    fetch(`/api/wilayah/${id}/kecamatan`)
                        .then(r => r.json())
                        .then(res => {
                            if (res.success && Array.isArray(res.data)) {
                                populateSelect(selKec, res.data, '-- Pilih Kecamatan --');
                                if (statusEl) statusEl.textContent = 'Pilih kecamatan';
                            }
                        });
                });

                selKec?.addEventListener('change', () => {
                    const id = selKec.value;
                    hiddenInput.value = id || selKab.value || '';
                    resetSelect(selDesa, '-- Pilih Desa/Kelurahan --');
                    if (!id) return;

                    if (statusEl) statusEl.textContent = 'Memuat desa/kelurahan...';
                    fetch(`/api/wilayah/${id}/desa?per_page=100`)
                        .then(r => r.json())
                        .then(res => {
                            if (res.success && Array.isArray(res.data)) {
                                populateSelect(selDesa, res.data, '-- Pilih Desa/Kelurahan --');
                                if (statusEl) statusEl.textContent = 'Pilih desa/kelurahan';
                            }
                        });
                });

                selDesa?.addEventListener('change', () => {
                    hiddenInput.value = selDesa.value || selKec.value || '';
                    if (statusEl && selDesa.value) {
                        statusEl.textContent = 'Wilayah terpilih';
                    }
                });
            });
        });
    </script>
    @endpush
@endonce
