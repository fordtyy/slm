<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">

    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ config('app.name') }}</title>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @filamentStyles
</head>

<body class="antialiased">
    <x-navbar></x-navbar>
    {{ $slot }}
    <x-footer></x-footer>
    @livewire('notifications')
    @filamentScripts
    <script>
        const navbar = document.getElementById('navbar')
        window.addEventListener('scroll', () => {

            if (window.scrollY >= 64) {
                navbar.classList.remove('border-transparent')
            } else {
                navbar.classList.add('border-transparent')
            }
        })
    </script>
</body>

</html>
