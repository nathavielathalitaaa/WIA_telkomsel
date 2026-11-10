/**
 * TanggapSidepanel - Expandable Side Panel / Drawer untuk Desktop
 */
export class TanggapSidepanel {
    static backdropEl = null;
    static drawerEl = null;
    static titleEl = null;
    static subtitleEl = null;
    static bodyEl = null;
    static footerEl = null;

    static init() {
        if (!this.drawerEl) {
            let backdrop = document.getElementById('tanggap-sidepanel-backdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.id = 'tanggap-sidepanel-backdrop';
                backdrop.className = 'sidepanel-backdrop';
                document.body.appendChild(backdrop);
            }
            this.backdropEl = backdrop;

            let drawer = document.getElementById('tanggap-sidepanel-drawer');
            if (!drawer) {
                drawer = document.createElement('aside');
                drawer.id = 'tanggap-sidepanel-drawer';
                drawer.className = 'sidepanel-drawer';
                drawer.setAttribute('role', 'dialog');
                drawer.setAttribute('aria-modal', 'true');
                drawer.innerHTML = `
                    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-3 bg-[#F8F9FC]">
                        <div class="min-w-0">
                            <span class="font-telemetry text-[10px] font-bold uppercase tracking-wider text-[#788397] block sidepanel-subtitle">
                                Pratinjau Cepat
                            </span>
                            <h3 class="font-extrabold text-base text-[#202938] truncate sidepanel-title">
                                Detail Informasi
                            </h3>
                        </div>
                        <button type="button" class="btn-sidepanel-close p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200 transition cursor-pointer" aria-label="Tutup panel">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="flex-1 overflow-y-auto p-5 space-y-4 sidepanel-body">
                        <!-- Dynamic Content Inserted Here -->
                    </div>
                    <div class="p-4 border-t border-slate-200 bg-white sidepanel-footer">
                        <!-- Dynamic Action Buttons Inserted Here -->
                    </div>
                `;
                document.body.appendChild(drawer);
            }
            this.drawerEl = drawer;
            this.titleEl = drawer.querySelector('.sidepanel-title');
            this.subtitleEl = drawer.querySelector('.sidepanel-subtitle');
            this.bodyEl = drawer.querySelector('.sidepanel-body');
            this.footerEl = drawer.querySelector('.sidepanel-footer');

            // Close listeners
            drawer.querySelector('.btn-sidepanel-close').addEventListener('click', () => this.close());
            backdrop.addEventListener('click', () => this.close());

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isOpen()) {
                    this.close();
                }
            });
        }
    }

    static open({ title = 'Detail', subtitle = 'Pratinjau Cepat', content = '', footerHtml = '' }) {
        this.init();

        this.titleEl.textContent = title;
        this.subtitleEl.textContent = subtitle;

        if (typeof content === 'string') {
            this.bodyEl.innerHTML = content;
        } else if (content instanceof HTMLElement) {
            this.bodyEl.innerHTML = '';
            this.bodyEl.appendChild(content);
        }

        if (footerHtml) {
            this.footerEl.innerHTML = footerHtml;
            this.footerEl.classList.remove('hidden');
        } else {
            this.footerEl.innerHTML = '';
            this.footerEl.classList.add('hidden');
        }

        this.backdropEl.classList.add('open');
        this.drawerEl.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    static close() {
        if (!this.drawerEl) return;
        this.backdropEl.classList.remove('open');
        this.drawerEl.classList.remove('open');
        document.body.style.overflow = '';
    }

    static isOpen() {
        return this.drawerEl && this.drawerEl.classList.contains('open');
    }
}

