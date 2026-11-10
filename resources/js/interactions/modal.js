/**
 * TanggapModal - Sistem Dialog Modal & Konfirmasi Tindakan
 */
export class TanggapModal {
    static activeModal = null;
    static previousFocus = null;

    static init() {
        // Global delegated listeners for open / close triggers
        document.addEventListener('click', (e) => {
            const openTrigger = e.target.closest('[data-modal-target]');
            if (openTrigger) {
                e.preventDefault();
                const selector = openTrigger.getAttribute('data-modal-target');
                this.open(selector);
                return;
            }

            const closeTrigger = e.target.closest('[data-modal-close]');
            if (closeTrigger) {
                e.preventDefault();
                this.close();
                return;
            }

            // Click backdrop to close
            if (e.target.classList.contains('modal-backdrop')) {
                this.close();
            }
        });

        // ESC key to close
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.activeModal) {
                this.close();
            }
        });
    }

    static open(selectorOrElement) {
        const modal = typeof selectorOrElement === 'string'
            ? document.querySelector(selectorOrElement)
            : selectorOrElement;

        if (!modal) return;

        this.previousFocus = document.activeElement;
        this.activeModal = modal;
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');

        // Focus first focusable element inside modal
        const focusable = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        if (focusable.length > 0) {
            focusable[0].focus();
        }
    }

    static close() {
        if (!this.activeModal) return;

        this.activeModal.classList.remove('open');
        this.activeModal.setAttribute('aria-hidden', 'true');
        this.activeModal = null;

        if (this.previousFocus && typeof this.previousFocus.focus === 'function') {
            this.previousFocus.focus();
            this.previousFocus = null;
        }
    }

    /**
     * Tampilkan konfirmasi cepat untuk aksi penting (misal: penolakan laporan, verifikasi)
     */
    static confirm({ title = 'Konfirmasi Tindakan', message = 'Apakah Anda yakin ingin melanjutkan tindakan ini?', confirmText = 'Lanjutkan', cancelText = 'Batal', isDestructive = false, onConfirm }) {
        let confirmModal = document.getElementById('tanggap-confirm-modal');
        if (!confirmModal) {
            confirmModal = document.createElement('div');
            confirmModal.id = 'tanggap-confirm-modal';
            confirmModal.className = 'modal-backdrop';
            confirmModal.setAttribute('role', 'dialog');
            confirmModal.setAttribute('aria-modal', 'true');
            confirmModal.innerHTML = `
                <div class="modal-container p-6 space-y-4">
                    <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3">
                        <h3 class="font-extrabold text-base text-[#202938] confirm-title"></h3>
                        <button type="button" data-modal-close class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <p class="text-sm text-slate-600 confirm-message leading-relaxed"></p>
                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" data-modal-close class="px-4 py-2 rounded-lg border border-slate-300 text-xs font-telemetry font-bold text-[#202938] hover:bg-slate-100 transition cursor-pointer">
                            ${cancelText}
                        </button>
                        <button type="button" class="btn-confirm-action px-4 py-2 rounded-lg text-xs font-telemetry font-bold text-white transition cursor-pointer">
                            ${confirmText}
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(confirmModal);
        }

        confirmModal.querySelector('.confirm-title').textContent = title;
        confirmModal.querySelector('.confirm-message').textContent = message;

        const confirmBtn = confirmModal.querySelector('.btn-confirm-action');
        confirmBtn.textContent = confirmText;
        confirmBtn.className = isDestructive
            ? 'btn-confirm-action px-4 py-2 rounded-lg bg-[#DC2626] hover:bg-red-700 text-xs font-telemetry font-bold text-white transition cursor-pointer'
            : 'btn-confirm-action px-4 py-2 rounded-lg bg-[#173B70] hover:bg-[#0F2A52] text-xs font-telemetry font-bold text-white transition cursor-pointer';

        // Bind single click handler
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

        newConfirmBtn.addEventListener('click', () => {
            this.close();
            if (typeof onConfirm === 'function') {
                onConfirm();
            }
        });

        this.open(confirmModal);
    }
}

