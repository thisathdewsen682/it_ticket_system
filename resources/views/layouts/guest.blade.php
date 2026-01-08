<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'IT JOB REQUEST SYSTEM') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased bg-gradient-to-br from-emerald-50 to-teal-50">
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-emerald-50 to-teal-50">
        <div class="text-center">
            <a href="/" class="text-xl font-bold text-emerald-900 hover:text-emerald-600 transition-colors">
                IT JOB REQUEST SYSTEM
            </a>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-8 py-6 bg-white border border-emerald-200 shadow-lg overflow-hidden sm:rounded-xl">
            {{ $slot }}
        </div>
    </div>
</body>

</html>