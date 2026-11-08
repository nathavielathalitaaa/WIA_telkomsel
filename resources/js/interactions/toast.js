/**
 * TanggapToast - Sistem Toast Notification Ringan & Aksesibel
 */
export class TanggapToast {
    static container = null;

    static init() {
        if (!this.container) {
            let el = document.getElementById('tanggap-toast-container');
            if (!el) {
                el = document.createElement('div');
                el.id = 'tanggap-toast-container';
                el.className = 'fixed top-5 right-5 z-50 flex flex-col gap-2.5 max-w-sm pointer-events-none';
                el.setAttribute('aria-live', 'polite');
                document.body.appendChild(el);
            }
            this.container = el;
        }

        // Auto-check flash messages from body data attributes
        const flashSuccess = document.body.dataset.flashSuccess;
        const flashError = document.body.dataset.flashError;
        if (flashSuccess) {
            this.success(flashSuccess);
        }
        if (flashError) {
            this.error(flashError);
        }
    }

    static show({ type = 'info', title = '', message = '', duration = 4000 }) {
        this.init();

        const toast = document.createElement('div');
        toast.className = 'toast-item';
        toast.setAttribute('role', 'status');

        const icons = {
            success: `<div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>`,
            error: `<div class="w-7 h-7 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>`,
            warning: `<div class="w-7 h-7 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
            </div>`,
            info: `<div class="w-7 h-7 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>`
        };

        const defaultTitles = {
            success: 'Berhasil',
            error: 'Pemberitahuan',
            warning: 'Perhatian',
            info: 'Informasi'
        };

        const headerTitle = title || defaultTitles[type] || 'Informasi';

        toast.innerHTML = `
            ${icons[type] || icons.info}
            <div class="flex-1 min-w-0 pr-1">
                <div class="font-telemetry text-xs font-bold uppercase tracking-wider text-[#0F172A] leading-tight">
                    ${headerTitle}
                </div>
                <div class="text-xs text-slate-600 mt-0.5 leading-snug">
                    ${message}
                </div>
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-600 p-1 -mr-1 -mt-1 rounded cursor-pointer" aria-label="Tutup notifikasi">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        `;

        const closeBtn = toast.querySelector('button');
        const dismiss = () => {
            toast.classList.remove('show');
            setTimeout(() => {
                if (toast.parentElement) toast.remove();
            }, 250);
        };

        closeBtn.addEventListener('click', dismiss);
        this.container.appendChild(toast);

        // Trigger entrance transition
        requestAnimationFrame(() => {
            toast.classList.add('show');
        });

        if (duration > 0) {
            setTimeout(dismiss, duration);
        }
    }

    static success(message, title = 'Berhasil') {
        this.show({ type: 'success', title, message });
    }

    static error(message, title = 'Terjadi Kesalahan') {
        this.show({ type: 'error', title, message });
    }

    static info(message, title = 'Informasi') {
        this.show({ type: 'info', title, message });
    }

    static warning(message, title = 'Perhatian') {
        this.show({ type: 'warning', title, message });
    }
}

