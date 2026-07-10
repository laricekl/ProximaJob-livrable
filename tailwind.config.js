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
                // Aligné avec --pj-* dans partials/head.blade.php
                'primary':           '#1f2433',
                'on-primary':        '#ffffff',
                'primary-container':  '#131b2e',
                'primary-fixed':      '#dae2fd',
                'accent':             '#eb843c',
                'accent-strong':      '#d9732c',
                'secondary':          '#eb843c',
                'secondary-container':'#eb843c',
                'secondary-fixed':     '#ffdbca',
                'on-secondary-fixed-variant': '#783200',
                'tertiary-fixed':      '#d5e3fd',
                'on-tertiary-fixed':   '#0d1c2f',
                'surface':             '#f7f9fb',
                'surface-container-lowest': '#ffffff',
                'surface-container-low': '#f2f4f6',
                'surface-container':    '#eceef0',
                'on-surface':           '#191c1e',
                'on-surface-variant':   '#45464d',
                'outline':              '#76777d',
                'outline-variant':      '#c6c6cd',
                'bg':                   '#dde1e6',
                'border-color':         'rgba(15,23,42,0.08)',
            },
        },
    },

    plugins: [forms],
};
