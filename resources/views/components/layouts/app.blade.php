<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
 
        <meta name="application-name" content="{{ config('app.name') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
 
        <title>{{ config('app.name') }}</title>
 
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
 
        @filamentStyles
        @vite('resources/css/app.css')
        @wirechatStyles
    </head>
 
    <body class="antialiased">
    {{ $slot }}

    <!-- <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script> -->
    <script>
    document.addEventListener('alpine:init', () => {
        console.log('Alpine.js is loaded and ready!');
    });
    </script>

        @filamentScripts
        @vite('resources/js/app.js')
        @wirechatAssets
    </body>
</html>