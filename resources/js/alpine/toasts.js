import { listenWhileConnected } from './listen';

const DISMISS_AFTER = { error: 10000, warning: 8000 };
const DEFAULT_DISMISS_AFTER = 5000;

export default () => ({
    items: [],
    nextId: 1,

    init() {
        listenWhileConnected(this.$el, window, 'toast', (event) => this.add(event.detail));

        if (this.$el.dataset.flash) {
            this.add(JSON.parse(this.$el.dataset.flash));
        }
    },

    add({ type = 'info', message }) {
        const id = this.nextId++;

        this.items.push({ id, type, message });

        setTimeout(() => this.dismiss(id), DISMISS_AFTER[type] ?? DEFAULT_DISMISS_AFTER);
    },

    dismiss(id) {
        this.items = this.items.filter((toast) => toast.id !== id);
    },

    classFor(toast) {
        return `toast--${toast.type}`;
    },
});
