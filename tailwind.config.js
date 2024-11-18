import preset from "./vendor/filament/support/tailwind.config.preset";

/** @type {import('tailwindcss').Config} */
export default {
    presets: [preset],
    content: [
        "./app/Filament/**/*.php",
        "./app/Livewire/**/*.php",
        "./vendor/filament/**/*.blade.php",
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./node_modules/flowbite/**/*.js",
    ],
    plugins: [require("flowbite/plugin")],
};
