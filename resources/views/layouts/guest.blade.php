<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'IT Job System') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-slate-900 antialiased bg-slate-50">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-50">
        <div class="text-center mb-8">
            <a href="{{ route('dashboard') }}" class="text-2xl font-bold">
                <span class="text-blue-600">IT</span> Job System
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white border border-slate-200 shadow-lg overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>
</body>

</html>