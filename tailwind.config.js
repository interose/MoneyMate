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
                bg:        'rgb(15,22,41)',
                elevated:  'rgb(22,32,58)',
                card:      'rgb(28,40,70)',
                accent:    'rgb(67,72,224)',
                pink:      'rgb(233,67,143)',
                green:     'rgb(52,211,153)',
            },
            fontFamily: {
                sans: ['"DM Sans"', 'sans-serif'],
                mono: ['"DM Mono"', 'monospace'],
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
