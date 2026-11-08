/**
 * TanggapTooltip - Smart Floating Tooltip untuk Informasi Kontekstual Desktop
 */
export class TanggapTooltip {
    static tooltipEl = null;
    static activeTarget = null;

    static init() {
        if (!this.tooltipEl) {
            this.tooltipEl = document.createElement('div');
            this.tooltipEl.className = 'tanggap-tooltip';
            this.tooltipEl.setAttribute('role', 'tooltip');
            document.body.appendChild(this.tooltipEl);
        }

        // Global event delegation for mouseenter and focusin
        document.addEventListener('mouseover', (e) => {
            const target = e.target.closest('[data-tooltip]');
            if (target) {
                this.show(target, target.getAttribute('data-tooltip'));
            }
        });

        document.addEventListener('mouseout', (e) => {
            const target = e.target.closest('[data-tooltip]');
            if (target && target === this.activeTarget) {
                this.hide();
            }
        });

        document.addEventListener('focusin', (e) => {
            const target = e.target.closest('[data-tooltip]');
            if (target) {
                this.show(target, target.getAttribute('data-tooltip'));
            }
        });

        document.addEventListener('focusout', (e) => {
            const target = e.target.closest('[data-tooltip]');
            if (target && target === this.activeTarget) {
                this.hide();
            }
        });

        window.addEventListener('scroll', () => {
            if (this.activeTarget) {
                this.updatePosition(this.activeTarget);
            }
        }, { passive: true });
    }

    static show(target, text) {
        if (!text || !text.trim()) return;
        this.activeTarget = target;
        this.tooltipEl.textContent = text;
        this.updatePosition(target);
        this.tooltipEl.classList.add('active');
    }

    static hide() {
        this.activeTarget = null;
        if (this.tooltipEl) {
            this.tooltipEl.classList.remove('active');
        }
    }

    static updatePosition(target) {
        if (!this.tooltipEl) return;
        const rect = target.getBoundingClientRect();
        const tooltipRect = this.tooltipEl.getBoundingClientRect();

        let top = rect.top - tooltipRect.height - 8;
        let left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);

        // Adjust if overflowing top
        if (top < 10) {
            top = rect.bottom + 8;
        }

        // Adjust horizontal bounds
        const maxLeft = window.innerWidth - tooltipRect.width - 12;
        if (left < 12) left = 12;
        if (left > maxLeft) left = maxLeft;

        this.tooltipEl.style.top = `${top}px`;
        this.tooltipEl.style.left = `${left}px`;
    }
}

