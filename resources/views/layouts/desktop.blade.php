<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @hasSection('title')
            Lymnik — @yield('title')
        @else
            Lymnik
        @endif
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/map.js', 'resources/js/dashboard.js', 'resources/js/analyses-desktop.js', 'resources/js/statistiques.js', 'resources/js/header.js'])
    @stack('styles')
</head>

<body class="bg-slate-50 font-grotesk text-slate-900 antialiased">

    <div class="flex h-screen overflow-hidden">

        @include('desktop.partials._sidebar')

        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

            @include('desktop.partials._header')

            <main class="flex-1 {{ request()->routeIs('map') || request()->routeIs('analyses.index') || request()->routeIs('statistiques.index') ? 'p-0 overflow-hidden flex flex-col' : 'p-4 sm:p-6 lg:p-8 overflow-y-auto' }}">
                @yield('content')
            </main>

        </div>
    </div>

    @stack('scripts')
</body>

</html>
