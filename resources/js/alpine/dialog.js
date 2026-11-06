import { listenWhileConnected } from './listen';

export default (name) => ({
    init() {
        const dialog = this.$el;

        listenWhileConnected(dialog, window, 'open-dialog', (event) => {
            if (event.detail?.name === name && !dialog.open) {
                dialog.showModal();
            }
        });

        listenWhileConnected(dialog, window, 'close-dialog', (event) => {
            if (event.detail?.name === name && dialog.open) {
                dialog.close();
            }
        });

        dialog.addEventListener('close', () => {
            window.dispatchEvent(new CustomEvent('dialog-closed', { detail: { name } }));
        });
    },

    closeOnBackdrop(event) {
        if (event.target === this.$el) {
            this.$el.close();
        }
    },
});
