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
