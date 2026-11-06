import { listenWhileConnected } from './listen';

export default () => ({
    open: false,

    init() {
        listenWhileConnected(this.$el, document, 'keydown', (event) => {
            if (event.key === 'Escape') {
                this.close();
            }
        });
    },

    toggle() {
        if (this.open) {
            this.close();
        } else {
            this.show();
        }
    },

    // The drawer comes before its button in the page, so tabbing from the
    // button would not reach it.
    show() {
        this.open = true;
        this.$nextTick(() => document.querySelector('#sidebar a')?.focus());
    },

    close() {
        if (!this.open) {
            return;
        }

        this.open = false;
        this.$refs.menuButton?.focus();
    },
});
