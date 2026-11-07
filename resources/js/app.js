const THEME_KEY = 'billify-theme';
const TOAST_DURATION = 5000;

const readTheme = () => {
    try {
        return localStorage.getItem(THEME_KEY) ?? 'system';
    } catch {
        return 'system';
    }
};

document.addEventListener('alpine:init', () => {
    Alpine.store('theme', {
        value: readTheme(),

        set(value) {
            this.value = value;

            try {
                localStorage.setItem(THEME_KEY, value);
            } catch {
                // Private browsing can block storage; the theme still applies for this page.
            }

            if (value === 'system') {
                delete document.documentElement.dataset.theme;
            } else {
                document.documentElement.dataset.theme = value;
            }
        },
    });

    Alpine.data('toasts', (flashed = null) => ({
        items: [],
        nextId: 0,

        init() {
            if (flashed) {
                this.push(flashed);
            }
        },

        push({ type = 'info', message }) {
            const id = this.nextId++;

            this.items.push({ id, type, message });
            setTimeout(() => this.dismiss(id), TOAST_DURATION);
        },

        dismiss(id) {
            this.items = this.items.filter((toast) => toast.id !== id);
        },
    }));
});
