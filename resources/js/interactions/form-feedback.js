import { TanggapToast } from './toast';

/**
 * FormFeedback - Loading State, Double-Submit Guard, dan Unsaved Changes Warning
 */
export class FormFeedback {
    static init() {
        // 1. Submit Button Loading States & Anti-Double Click
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (!(form instanceof HTMLFormElement)) return;

            const submitBtn = form.querySelector('button[type="submit"]');
            if (!submitBtn || submitBtn.classList.contains('btn-loading')) return;

            // Mark form as submitting to disable unsaved changes prompt
            form.dataset.submitting = 'true';

            // Wrap text content in a span if not already wrapped
            if (!submitBtn.querySelector('.btn-text-content')) {
                const textSpan = document.createElement('span');
                textSpan.className = 'btn-text-content';
                while (submitBtn.firstChild) {
                    textSpan.appendChild(submitBtn.firstChild);
                }
                submitBtn.appendChild(textSpan);
            }

            submitBtn.classList.add('btn-loading');
            submitBtn.style.pointerEvents = 'none';
            setTimeout(() => {
                submitBtn.setAttribute('disabled', 'disabled');
            }, 50);
        });

        // 2. Unsaved Changes Warning for Internal / Complex Forms
        const protectedForms = document.querySelectorAll('form[data-warn-unsaved="true"]');
        protectedForms.forEach((form) => {
            let isDirty = false;

            form.addEventListener('input', () => {
                isDirty = true;
                const dirtyIndicator = document.getElementById('unsaved-changes-indicator');
                if (dirtyIndicator) {
                    dirtyIndicator.classList.remove('hidden');
                }
            });

            form.addEventListener('change', () => {
                isDirty = true;
                const dirtyIndicator = document.getElementById('unsaved-changes-indicator');
                if (dirtyIndicator) {
                    dirtyIndicator.classList.remove('hidden');
                }
            });

            window.addEventListener('beforeunload', (e) => {
                if (isDirty && form.dataset.submitting !== 'true') {
                    e.preventDefault();
                    e.returnValue = 'Terdapat perubahan formulir yang belum disimpan.';
                    return e.returnValue;
                }
            });
        });

        // 3. Copy-to-Clipboard Buttons (e.g. tracking tickets)
        document.addEventListener('click', async (e) => {
            const copyBtn = e.target.closest('[data-copy-text]');
            if (!copyBtn) return;

            const textToCopy = copyBtn.getAttribute('data-copy-text');
            if (!textToCopy) return;

            try {
                await navigator.clipboard.writeText(textToCopy);
                TanggapToast.success(`Nomor tiket ${textToCopy} berhasil disalin ke clipboard!`, 'Tersalin');
            } catch {
                // Fallback for older browsers / iframe
                const tempInput = document.createElement('input');
                tempInput.value = textToCopy;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                TanggapToast.success(`Nomor tiket ${textToCopy} berhasil disalin!`, 'Tersalin');
            }
        });
    }
}

