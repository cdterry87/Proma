<label
    x-data="{
        light: false,
        themeToggle() {
            const theme = this.light ? 'light' : 'dark';
            localStorage.setItem('theme', theme);
            this.setTheme(theme);
        },
        setTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.classList[theme === 'dark' ? 'add' : 'remove']('dark');
        },
        init() {
            const theme = localStorage.getItem('theme') || 'dark';
            this.light = theme === 'light';
            this.setTheme(theme);
        }
    }"
    class ="swap swap-rotate"
>
    <input
        type="checkbox"
        class="theme-controller"
        x-model="light"
        @change.prevent="themeToggle()"
    />
    <x-icons.light class="swap-on" />
    <x-icons.dark class="swap-off" />
</label>
