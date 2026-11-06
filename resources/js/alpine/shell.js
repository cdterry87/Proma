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
        this.open = !this.open;
    },

    close() {
        this.open = false;
    },
});
