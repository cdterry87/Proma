const POINTER_GAP = 12;

/**
 * The tooltip and crosshair for a server-rendered chart. Marks describe
 * themselves with a data-tooltip attribute. The values come from user input, so
 * they only ever reach the page through textContent.
 */
export default () => ({
    show(event) {
        const mark = event.target.closest?.('[data-tooltip]');

        if (!mark) {
            return;
        }

        const { title, rows } = JSON.parse(mark.dataset.tooltip);
        const tooltip = this.$refs.tooltip;

        tooltip.replaceChildren(this.titleElement(title), ...rows.map((row) => this.rowElement(row)));
        tooltip.hidden = false;

        this.moveCrosshair(mark);
        this.position(event, mark);
    },

    hide() {
        this.$refs.tooltip.hidden = true;

        if (this.$refs.crosshair) {
            this.$refs.crosshair.setAttribute('visibility', 'hidden');
        }
    },

    titleElement(title) {
        const element = document.createElement('p');
        element.className = 'chart__tooltip-title';
        element.textContent = title;

        return element;
    },

    rowElement({ label, value, series }) {
        const row = document.createElement('p');
        row.className = 'chart__tooltip-row';

        if (series) {
            const key = document.createElement('span');
            key.className = `chart__key chart__key--${series}`;
            row.append(key);
        }

        const amount = document.createElement('strong');
        amount.textContent = value;

        const name = document.createElement('span');
        name.textContent = label;

        row.append(amount, name);

        return row;
    },

    moveCrosshair(mark) {
        const crosshair = this.$refs.crosshair;

        if (crosshair && mark.dataset.x) {
            crosshair.setAttribute('x1', mark.dataset.x);
            crosshair.setAttribute('x2', mark.dataset.x);
            crosshair.setAttribute('visibility', 'visible');
        }
    },

    position(event, mark) {
        const tooltip = this.$refs.tooltip;
        const bounds = this.$el.getBoundingClientRect();
        const markBounds = mark.getBoundingClientRect();
        const pointerX = event.clientX || markBounds.left + markBounds.width / 2;
        const pointerY = event.clientY || markBounds.top + markBounds.height / 2;

        let left = pointerX - bounds.left + POINTER_GAP;

        if (left + tooltip.offsetWidth > bounds.width) {
            left = pointerX - bounds.left - tooltip.offsetWidth - POINTER_GAP;
        }

        tooltip.style.left = `${Math.max(0, left)}px`;
        tooltip.style.top = `${Math.max(0, pointerY - bounds.top - tooltip.offsetHeight / 2)}px`;
    },
});
