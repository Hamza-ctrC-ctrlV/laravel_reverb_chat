<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark" style="background-color: #0f172a;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f172a">
    <meta name="color-scheme" content="dark">

    <title>{{ $title ?? config('app.name') }}</title>

    {{-- Anti-Flicker Script --}}
    <script>
        document.documentElement.classList.add('dark');
        document.documentElement.style.backgroundColor = '#0f172a';
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#0f172a] text-white antialiased font-sans selection:bg-sky-500/30">
    
    <div class="flex min-h-screen">
        {{-- The Fixed Sidebar --}}
        <x-layouts.app.sidebar />

        {{-- Added flex flex-col and overflow-y-auto to allow internal centering --}}
        <main class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>