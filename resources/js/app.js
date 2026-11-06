import dialog from './alpine/dialog';
import shell from './alpine/shell';
import themeToggle from './alpine/theme-toggle';
import toasts from './alpine/toasts';
import { registerDialogTriggers } from './dialog-triggers';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('dialog', dialog);
    window.Alpine.data('shell', shell);
    window.Alpine.data('themeToggle', themeToggle);
    window.Alpine.data('toasts', toasts);
});

registerDialogTriggers();

// Navigation replaces the html attributes with the server's, which has no theme.
document.addEventListener('livewire:navigating', ({ detail }) => {
    const { theme } = document.documentElement.dataset;

    detail.onSwap(() => {
        document.documentElement.dataset.theme = theme;
    });
});
