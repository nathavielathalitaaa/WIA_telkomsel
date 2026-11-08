import { TanggapSidepanel } from './sidepanel';

/**
 * MapCrossHighlight - Sistem Interaksi Dua Arah (Cross-Highlighting) Antara Card & Marker Peta
 */
export class MapCrossHighlight {
    /**
     * Inisialisasi cross-highlighting pada halaman Pantau
     */
    static init({ map, incidentMarkers = new Map(), incidentCards = [] }) {
        if (!map || incidentMarkers.size === 0) return;

        let panDebounceTimer = null;
        let activeCard = null;

        // 1. Hover Card -> Highlight Map Marker
        incidentCards.forEach((card) => {
            const incidentId = card.getAttribute('data-incident-id');
            const marker = incidentMarkers.get(String(incidentId));
            if (!marker) return;

            card.addEventListener('mouseenter', () => {
                this.highlightMarker(marker);

                // Debounced subtle map pan if card hovered deliberately for > 280ms
                clearTimeout(panDebounceTimer);
                panDebounceTimer = setTimeout(() => {
                    const latLng = marker.getLatLng();
                    if (!map.getBounds().contains(latLng)) {
                        map.panTo(latLng, { animate: true, duration: 0.5 });
                    }
                }, 280);
            });

            card.addEventListener('mouseleave', () => {
                clearTimeout(panDebounceTimer);
                this.resetMarker(marker);
            });

            // "Fokus di Peta" button trigger
            const focusBtn = card.querySelector('.btn-focus-map');
            if (focusBtn) {
                focusBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const latLng = marker.getLatLng();
                    map.setView(latLng, Math.max(map.getZoom(), 14), { animate: true });
                    marker.openPopup();
                    this.highlightMarker(marker);
                });
            }

            // "Pratinjau Cepat" button trigger
            const previewBtn = card.querySelector('.btn-quick-preview');
            if (previewBtn) {
                previewBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.openIncidentSidepanel(card, marker);
                });
            }
        });

        // 2. Hover Marker -> Highlight Incident Card
        incidentMarkers.forEach((marker, idStr) => {
            const card = incidentCards.find((c) => c.getAttribute('data-incident-id') === idStr);
            if (!card) return;

            marker.on('mouseover', () => {
                this.highlightMarker(marker);
                if (activeCard && activeCard !== card) {
                    activeCard.classList.remove('card-highlighted');
                }
                card.classList.add('card-highlighted');
                activeCard = card;

                // Smoothly scroll the card into view in the list if not visible
                const rect = card.getBoundingClientRect();
                const isInViewport = rect.top >= 0 && rect.bottom <= window.innerHeight;
                if (!isInViewport) {
                    card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });

            marker.on('mouseout', () => {
                this.resetMarker(marker);
                if (activeCard === card) {
                    card.classList.remove('card-highlighted');
                    activeCard = null;
                }
            });
        });
    }

    static highlightMarker(marker) {
        if (!marker) return;
        marker._origRadius = marker.options.radius;
        marker._origWeight = marker.options.weight;
        marker.setRadius(marker.options.radius + 4);
        marker.setStyle({
            weight: 4,
            color: '#2563EB',
            fillOpacity: 1,
        });
        if (marker.bringToFront) {
            marker.bringToFront();
        }
    }

    static resetMarker(marker) {
        if (!marker || typeof marker._origRadius === 'undefined') return;
        marker.setRadius(marker._origRadius);
        marker.setStyle({
            weight: marker._origWeight || 2.5,
            color: '#FFFFFF',
            fillOpacity: 0.92,
        });
    }

    static openIncidentSidepanel(card, marker) {
        const title = card.getAttribute('data-incident-title') || 'Detail Kejadian';
        const type = card.getAttribute('data-incident-type') || 'Bencana';
        const location = card.getAttribute('data-incident-location') || '-';
        const verification = card.getAttribute('data-incident-verification') || 'Menunggu Verifikasi';
        const followUp = card.getAttribute('data-incident-followup') || 'Belum Ditindaklanjuti';
        const reportsCount = card.getAttribute('data-incident-reports') || '0';
        const aidCount = card.getAttribute('data-incident-aid') || '0';
        const url = card.getAttribute('data-incident-url') || '#';
        const description = card.querySelector('p')?.textContent?.trim() || '';

        const content = `
            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-slate-900 text-white font-telemetry text-[10px] font-bold uppercase">
                        ${type}
                    </span>
                    <span class="font-telemetry text-xs text-slate-500">
                        ${location}
                    </span>
                </div>
                <h4 class="text-lg font-extrabold text-[#0F172A] leading-snug">
                    ${title}
                </h4>
                <p class="text-sm text-slate-600 leading-relaxed bg-slate-50 p-3.5 rounded-lg border border-slate-200">
                    ${description}
                </p>
                <div class="grid grid-cols-2 gap-2.5 pt-1">
                    <div class="bg-white border border-slate-200 rounded-lg p-3">
                        <span class="font-telemetry text-[10px] font-bold uppercase text-slate-500 block">1. Status Verifikasi</span>
                        <span class="font-telemetry text-xs font-bold text-[#2563EB] mt-0.5 block">${verification}</span>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-lg p-3">
                        <span class="font-telemetry text-[10px] font-bold uppercase text-slate-500 block">2. Tindak Lanjut Tim</span>
                        <span class="font-telemetry text-xs font-bold text-[#059669] mt-0.5 block">${followUp}</span>
                    </div>
                </div>
                <div class="flex items-center justify-between text-xs font-telemetry p-3 bg-blue-50 border border-blue-200 rounded-lg text-blue-900">
                    <span><strong>${reportsCount}</strong> Laporan Warga</span>
                    <span>•</span>
                    <span><strong>${aidCount}</strong> Respons Bantuan</span>
                </div>
            </div>
        `;

        const footerHtml = `
            <div class="flex items-center justify-between gap-3">
                <button type="button" class="btn-sidepanel-close px-4 py-2 rounded-lg border border-slate-300 text-xs font-telemetry font-bold text-[#0F172A] hover:bg-slate-100 transition cursor-pointer">
                    Tutup
                </button>
                <a href="${url}" class="px-4 py-2 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white text-xs font-telemetry font-bold uppercase tracking-wider inline-flex items-center gap-1.5 transition">
                    <span>Buka Halaman Lengkap</span>
                    <span>&rarr;</span>
                </a>
            </div>
        `;

        TanggapSidepanel.open({
            title: 'Ringkasan Kejadian',
            subtitle: location,
            content,
            footerHtml,
        });

        // Re-bind close button inside footer
        const drawerCloseBtn = document.querySelector('.sidepanel-footer .btn-sidepanel-close');
        if (drawerCloseBtn) {
            drawerCloseBtn.addEventListener('click', () => TanggapSidepanel.close());
        }
    }
}

