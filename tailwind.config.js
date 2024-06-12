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
        'btn-accent',
        'btn-error'
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Afacad', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                "pg-primary": colors.gray,
                "pg-secondary": colors.red,
            },
        },
    },
    darkMode: 'class',
    plugins: [require("daisyui")],
    daisyui: {
        themes: ["light", "dark"],
    },
}

