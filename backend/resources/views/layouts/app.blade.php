<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Personal Blog'))</title>
    <meta name="description" content="@yield('meta_description', 'Catatan, tutorial, dan perjalanan seorang programmer.')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&family=lora:400,500,600,700i&display=swap" rel="stylesheet" />

    <!-- Scripts / Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 font-sans text-stone-800 antialiased">
    <div class="flex min-h-screen flex-col">
        <x-navbar :profile="$profile ?? null" />

        <main class="flex-1">
            @yield('content')
        </main>

        <x-footer :profile="$profile ?? null" />
    </div>
</body>
</html>
