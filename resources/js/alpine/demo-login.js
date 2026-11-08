const SCRIPT_URL = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit';

let loading = null;

/**
 * Loads Turnstile once. The login page can be reached with wire:navigate, so
 * the script cannot rely on a tag in the page head.
 */
function loadTurnstile() {
    if (window.turnstile) {
        return Promise.resolve(window.turnstile);
    }

    loading ??= new Promise((resolve, reject) => {
        const script = document.createElement('script');

        script.src = SCRIPT_URL;
        script.async = true;
        script.onload = () => resolve(window.turnstile);
        script.onerror = () => {
            loading = null;
            reject(new Error('Turnstile failed to load.'));
        };

        document.head.append(script);
    });

    return loading;
}

/**
 * The demo sign in button, which waits for a Turnstile token and sends it
 * with the request.
 */
export default () => ({
    token: null,
    failed: false,
    busy: false,
    widget: null,

    get waiting() {
        return this.token === null || this.busy;
    },

    async init() {
        try {
            const turnstile = await loadTurnstile();

            if (!this.$el.isConnected) {
                return;
            }

            this.widget = turnstile.render(this.$refs.challenge, {
                sitekey: this.$el.dataset.sitekey,
                action: 'demo-login',
                theme: document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light',
                size: 'flexible',
                callback: (token) => {
                    this.token = token;
                    this.failed = false;
                },
                'expired-callback': () => {
                    this.token = null;
                },
                'error-callback': () => {
                    this.token = null;
                    this.failed = true;
                },
            });
        } catch (error) {
            this.failed = true;
        }
    },

    destroy() {
        if (this.widget !== null) {
            window.turnstile?.remove(this.widget);
        }
    },

    async signIn() {
        if (this.waiting) {
            return;
        }

        // Each token works once, so a failed attempt needs a new challenge.
        const token = this.token;

        this.token = null;
        this.busy = true;

        try {
            await this.$wire.loginAsDemo(token);
        } finally {
            this.busy = false;
            window.turnstile?.reset(this.widget);
        }
    },
});
