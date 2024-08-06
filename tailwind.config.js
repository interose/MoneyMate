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
    extend: {},
  },
  plugins: [
    plugin(function({ addVariant }) {
      addVariant('turbo-frame', 'turbo-frame[src] &');
    }),
  ],
}
