const plugin = require('tailwindcss/plugin');

/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        "./vendor/tales-from-a-dev/flowbite-bundle/templates/**/*.html.twig",
        "./assets/**/*.js",
        "./templates/**/*.html.twig",
        "./src/Twig/Components/**/*.php",
    ],
    safelist: [
        'bg-green/10',
        'text-green',
        'bg-teal-400/10',
        'text-teal-400',
        'bg-white/[.07]',
        'text-white/50',
        'bg-violet-400/10',
        'text-violet-400',
        'bg-pink/10',
        'text-pink/70',
        'bg-accent/10',
        'text-accent/80',
        'bg-accent',
        'bg-orange-400/10',
        'text-orange-400',
        'bg-orange-400',
        'bg-pink/10',
        'text-pink',
        'bg-pink',
    ],
    theme: {
        extend: {
            colors: {
                bg:          'var(--bg)',
                surface:     'var(--surface)',
                raised:      'var(--raised)',
                border:      'var(--border)',
                'border-hi': 'var(--border-hi)',
                tx:          'var(--tx)',
                muted:       'var(--muted)',
                accent:      '#5b8af5',
                green:       '#3ecf8e',
                amber:       '#f5a623',
                red:         '#f55b5b',
            },
            fontFamily: {
                sans: ['"DM Sans"', 'sans-serif'],
                mono: ['"IBM Plex Mono"', 'monospace'],
            },
            aria: {
                busy: 'busy="true"',
            },
        },
    },
    plugins: [
        plugin(function({ addVariant }) {
            addVariant('turbo-frame', 'turbo-frame[src] &');
            addVariant('subaccounts-expanded', '.selected &');
            addVariant('active', '.active &');
        }),
    ],
}
