import preset from './vendor/filament/support/tailwind.config.preset'

/** @type {import('tailwindcss').Config} */
export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        '.resources/views/components/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './node_modules/flowbite/**/*.js',
    ],
    plugins: [require("flowbite/plugin")],
}
