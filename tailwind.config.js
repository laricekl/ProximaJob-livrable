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
            fontSize: {
                '2xs': ['0.625rem', { lineHeight: '0.875rem' }], // 10px — badges, captions, sidebar headers
            },
            colors: {
                // Aligné avec --pj-* dans partials/head.blade.php
                'primary':            '#1f2433',
                'on-primary':         '#ffffff',
                'primary-container':  '#131b2e',
                'primary-fixed':      '#dae2fd',
                'accent':             '#eb843c',
                'accent-strong':      '#d9732c',
                'secondary':          '#d9732c',              // plus foncé → hover visible
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

                // Tokens sémantiques pour feedback
                'success': {
                    light: '#f0fdf4',  // green-50
                    DEFAULT: '#16a34a', // green-600
                    dark: '#15803d',    // green-700
                    deep: '#166534',    // green-800
                },
                'error': {
                    light: '#fef2f2',  // red-50
                    DEFAULT: '#dc2626', // red-600
                    dark: '#b91c1c',    // red-700
                    deep: '#991b1b',    // red-800
                },
                'warning': {
                    light: '#fffbeb',  // amber-50
                    DEFAULT: '#f59e0b', // amber-500
                    dark: '#b45309',    // amber-700
                },
                'info': {
                    light: '#eff6ff',  // blue-50
                    DEFAULT: '#2563eb', // blue-600
                    dark: '#1d4ed8',    // blue-700
                },
            },
        },
    },

    plugins: [forms],
};
