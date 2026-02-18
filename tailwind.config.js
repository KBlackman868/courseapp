import { createRequire } from 'module';
const require = createRequire(import.meta.url);

const defaultTheme = require('tailwindcss/defaultTheme');
const forms = require('@tailwindcss/forms');
const daisyui = require('daisyui');

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.jsx',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms({ strategy: 'class' }), daisyui],

    daisyui: {
        themes: ["light", "dark"],
        darkTheme: "dark",
        base: true,
        styled: true,
        utils: true,
        logs: false,
    },
};
