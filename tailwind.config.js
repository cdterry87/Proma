import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Afacad', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  plugins: [require("daisyui")],
}

