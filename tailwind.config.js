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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                evergreen: {
                    50: 'rgb(var(--evergreen-50) / <alpha-value>)',
                    100: 'rgb(var(--evergreen-100) / <alpha-value>)',
                    200: 'rgb(var(--evergreen-200) / <alpha-value>)',
                    300: 'rgb(var(--evergreen-300) / <alpha-value>)',
                    400: 'rgb(var(--evergreen-400) / <alpha-value>)',
                    500: 'rgb(var(--evergreen-500) / <alpha-value>)',
                    600: 'rgb(var(--evergreen-600) / <alpha-value>)',
                    700: 'rgb(var(--evergreen-700) / <alpha-value>)',
                    800: 'rgb(var(--evergreen-800) / <alpha-value>)',
                    900: 'rgb(var(--evergreen-900) / <alpha-value>)',
                    950: 'rgb(var(--evergreen-950) / <alpha-value>)',
                },
                primary: {
                    50: 'rgb(var(--primary-50) / <alpha-value>)',
                    100: 'rgb(var(--primary-100) / <alpha-value>)',
                    200: 'rgb(var(--primary-200) / <alpha-value>)',
                    300: 'rgb(var(--primary-300) / <alpha-value>)',
                    400: 'rgb(var(--primary-400) / <alpha-value>)',
                    500: 'rgb(var(--primary-500) / <alpha-value>)',
                    600: 'rgb(var(--primary-600) / <alpha-value>)',
                    700: 'rgb(var(--primary-700) / <alpha-value>)',
                    800: 'rgb(var(--primary-800) / <alpha-value>)',
                    900: 'rgb(var(--primary-900) / <alpha-value>)',
                    950: 'rgb(var(--primary-950) / <alpha-value>)',
                },
            },
        },
    },

    plugins: [forms],
};
