export default () => ({
    toggle() {
        const theme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';

        document.documentElement.dataset.theme = theme;

        try {
            localStorage.setItem('theme', theme);
        } catch (error) {
            // The choice still applies to this page when storage is unavailable.
        }
    },
});
