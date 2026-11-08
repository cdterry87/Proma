import chart from './alpine/chart';
import demoLogin from './alpine/demo-login';
import dialog from './alpine/dialog';
import disclosure from './alpine/disclosure';
import shell from './alpine/shell';
import themeToggle from './alpine/theme-toggle';
import toasts from './alpine/toasts';
import { registerDialogTriggers } from './dialog-triggers';
import { registerFocusManagement } from './focus';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('chart', chart);
    window.Alpine.data('demoLogin', demoLogin);
    window.Alpine.data('dialog', dialog);
    window.Alpine.data('disclosure', disclosure);
    window.Alpine.data('shell', shell);
    window.Alpine.data('themeToggle', themeToggle);
    window.Alpine.data('toasts', toasts);
});

registerDialogTriggers();
registerFocusManagement();

// Navigation replaces the html attributes with the server's, which has no theme.
document.addEventListener('livewire:navigating', ({ detail }) => {
    const { theme } = document.documentElement.dataset;

    detail.onSwap(() => {
        document.documentElement.dataset.theme = theme;
    });
});
