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
            colors: {
                primary: 'rgb(var(--primary) / <alpha-value>)',
                'primary-hover': 'rgb(var(--primary-hover) / <alpha-value>)',
                'primary-foreground': 'rgb(var(--primary-foreground) / <alpha-value>)',
                secondary: 'rgb(var(--secondary) / <alpha-value>)',
                'secondary-hover': 'rgb(var(--secondary-hover) / <alpha-value>)',
                'secondary-foreground': 'rgb(var(--secondary-foreground) / <alpha-value>)',
                success: 'rgb(var(--success) / <alpha-value>)',
                'success-soft': 'rgb(var(--success-soft) / <alpha-value>)',
                warning: 'rgb(var(--warning) / <alpha-value>)',
                'warning-soft': 'rgb(var(--warning-soft) / <alpha-value>)',
                info: 'rgb(var(--info) / <alpha-value>)',
                'info-soft': 'rgb(var(--info-soft) / <alpha-value>)',
                danger: 'rgb(var(--danger) / <alpha-value>)',
                'danger-hover': 'rgb(var(--danger-hover) / <alpha-value>)',
                'danger-soft': 'rgb(var(--danger-soft) / <alpha-value>)',
                light: 'rgb(var(--light) / <alpha-value>)',
                dark: 'rgb(var(--dark) / <alpha-value>)',
                canvas: 'rgb(var(--canvas) / <alpha-value>)',
                surface: 'rgb(var(--surface) / <alpha-value>)',
                subtle: 'rgb(var(--subtle) / <alpha-value>)',
                content: 'rgb(var(--content) / <alpha-value>)',
                muted: 'rgb(var(--muted) / <alpha-value>)',
                'muted-foreground': 'rgb(var(--muted-foreground) / <alpha-value>)',
                default: 'rgb(var(--border) / <alpha-value>)',
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            borderRadius: {
                ui: 'var(--radius-md)',
                'ui-lg': 'var(--radius-lg)',
                'ui-xl': 'var(--radius-xl)',
            },
            boxShadow: {
                ui: 'var(--shadow-sm)',
                'ui-lg': 'var(--shadow-md)',
            },
        },
    },

    plugins: [forms],
};
