import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                serif: ['Noto Serif', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                "primary": "#000000",
                "on-primary": "#ffffff",
                "secondary-container": "#EB843C",
                "secondary": "#EB843C",
                "surface": "#f7f9fb",
                "surface-container-lowest": "#ffffff",
                "surface-container": "#eceef0",
                "surface-container-low": "#f2f4f6",
                "on-surface": "#191c1e",
                "on-surface-variant": "#45464d",
                "outline": "#76777d",
                "outline-variant": "#c6c6cd",
                "primary-container": "#131b2e",
                "primary-fixed": "#dae2fd",
                "secondary-fixed": "#ffdbca",
                "on-secondary-fixed-variant": "#783200",
                "tertiary-fixed": "#d5e3fd",
                "on-tertiary-fixed": "#0d1c2f",
            },
        },
    },

    plugins: [forms],
};
