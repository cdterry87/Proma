export function registerDialogTriggers() {
    document.addEventListener('click', (event) => {
        const opener = event.target.closest('[data-open-dialog]');

        if (opener) {
            window.dispatchEvent(new CustomEvent('open-dialog', { detail: { name: opener.dataset.openDialog } }));

            return;
        }

        if (event.target.closest('[data-close-dialog]')) {
            event.target.closest('dialog')?.close();
        }
    });
}
