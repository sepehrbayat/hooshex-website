import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/js/**/*.vue',
        './resources/js/**/*.ts',
    ],
    /**
     * Important Strategy
     * Using selector-based important strategy for third-party library overrides
     * This increases specificity of all Tailwind classes without breaking CSS rules
     */
    important: '#app',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Vazirmatn', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                xs: ['12px', { lineHeight: '18px' }],
                sm: ['14px', { lineHeight: '21px' }],
                base: ['16px', { lineHeight: '24px' }],
                lg: ['18px', { lineHeight: '27px' }],
                xl: ['20px', { lineHeight: '30px' }],
                '2xl': ['24px', { lineHeight: '36px' }],
                '3xl': ['32px', { lineHeight: '48px' }],
                '4xl': ['36px', { lineHeight: '54px' }],
                '5xl': ['56px', { lineHeight: '84px' }],
            },
            fontWeight: {
                normal: 400,
                medium: 500,
                semibold: 600,
                bold: 700,
                black: 900,
            },
            colors: {
                primary: {
                    50: 'rgba(119, 95, 238, 0.1)',
                    400: '#442CBB',
                    500: '#775FEE',
                    600: '#5537EA',
                    800: '#22165E',
                },
                accent: {
                    400: 'rgba(235, 85, 200, 0.36)',
                    500: '#EB55C8',
                },
                surface: '#FCF1FB',
                'surface-light': '#F5F5F5',
                text: {
                    primary: '#22165E',
                    secondary: '#2D2D2D',
                    muted: '#AAAAAA',
                },
                success: '#10B981',
                warning: '#F59E0B',
                error: '#EF4444',
                info: '#3B82F6',
            },
            borderRadius: {
                sm: '4px',
                DEFAULT: '8px',
                md: '12px',
                lg: '16px',
                xl: '24px',
                '2xl': '32px',
                pill: '9999px',
                card: '16px',
                btn: '8px',
            },
            spacing: {
                18: '72px',
                22: '88px',
            },
            screens: {
                sm: '430px',
                md: '768px',
                lg: '1024px',
                xl: '1280px',
                '2xl': '1440px',
            },
            container: {
                center: true,
                padding: {
                    DEFAULT: '1rem',
                    sm: '1.5rem',
                    md: '2rem',
                    lg: '2.5rem',
                    xl: '2.5rem',
                    '2xl': '2.5rem',
                },
                screens: {
                    sm: '430px',
                    md: '768px',
                    lg: '1024px',
                    xl: '1280px',
                    '2xl': '1440px',
                },
            },
            boxShadow: {
                'glass': '0px 6px 24px rgba(0, 0, 0, 0.2)',
                'button-primary': '0px 2px 8px rgba(235, 85, 200, 0.46), inset 0px -4px 16px rgba(102, 24, 84, 0.13), inset 0px 4px 16px rgba(102, 24, 84, 0.11)',
            },
        },
    },
    plugins: [forms],
};
