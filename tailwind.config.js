import defaultTheme from 'tailwindcss/defaultTheme';
const colors = require('tailwindcss/colors')

/** @type {import('tailwindcss').Config} */
export default {
    presets: [
        require("./vendor/power-components/livewire-powergrid/tailwind.config.js"),
    ],
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.css",
        './app/Traits/*.php',
        './app/Http/Livewire/**/*Table.php',
        './vendor/power-components/livewire-powergrid/resources/views/**/*.php',
        './vendor/power-components/livewire-powergrid/src/Themes/Tailwind.php'
    ],
    safelist: [
        'btn-primary',
        'btn-secondary',
        'btn-accent',
        'btn-error'
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Futura', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                "pg-primary": colors.gray,
                "pg-secondary": colors.red,
            },
            spacing: {
                '120': '30rem',
                '144': '36rem',
            },
        },
    },
    darkMode: 'class',
    plugins: [require("daisyui")],
    daisyui: {
        themes: [
            {
                light: {
                    "primary": "#494694",
                    "secondary": "#336a6b",
                    "accent": "#374151",
                    "neutral": "#344052",
                    "base-100": "#ffffff",
                    "base-200": "#e5e7eb",
                    "base-300": "#d5d9e0",
                    "info": "#1d4ed8",
                    "success": "#047857",
                    "warning": "#6d28d9",
                    "error": "#be123c",
                },
                dark: {
                    "primary": "#8b86eb",
                    "secondary": "#63c2af",
                    "accent": "#e1e8e7",
                    "neutral": "#111827",
                    "base-100": "#29374a",
                    "base-200": "#1f2937",
                    "base-300": "#1b2431",
                    "info": "#1d4ed8",
                    "success": "#047857",
                    "warning": "#6d28d9",
                    "error": "#be123c",
                },
            },
        ],
    },
}

