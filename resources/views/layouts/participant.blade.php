<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @hasSection('title')Lymnik — @yield('title')@else Lymnik @endif
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-slate-50 font-grotesk text-slate-900 antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- Sidebar minimaliste --}}
    <aside class="w-14 lg:w-56 flex-shrink-0 bg-[#222a60] flex flex-col h-full">

        <div class="flex items-center justify-center lg:justify-start lg:px-5 py-4 border-b border-white/10 min-h-[56px]">
            <span class="text-white font-bold text-lg hidden lg:block tracking-tight">Lymnik</span>
            <span class="text-white font-black text-xl lg:hidden">L</span>
        </div>

        {{-- Infos participant --}}
        <div class="px-3 py-3 border-b border-white/10">
            <div class="hidden lg:block">
                <p class="text-[10px] font-mono text-white/40 uppercase tracking-widest">Session</p>
                <p class="text-xs font-bold text-white/80 truncate">{{ session('participant.campagne_nom') }}</p>
                @if(session('participant.id_groupe') > 0)
                    <p class="text-[11px] text-white/50 mt-0.5">Groupe {{ chr(64 + session('participant.id_groupe')) }}</p>
                @endif
                <p class="text-[11px] text-white/50 truncate">{{ session('participant.pseudo') }}</p>
            </div>
            <div class="lg:hidden flex items-center justify-center">
                <div class="w-7 h-7 rounded-full bg-white/15 flex items-center justify-center text-white text-xs font-bold">
                    {{ strtoupper(substr(session('participant.pseudo', '?'), 0, 1)) }}
                </div>
            </div>
        </div>

        <nav class="flex-1 px-2 py-3 space-y-0.5">
            @php
                $nav = [
                    ['label' => 'Analyses', 'route' => 'participant.analyses', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
                    ['label' => 'Carte',    'route' => 'participant.map',      'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7"/>'],
                ];
            @endphp
            @foreach ($nav as $item)
                @php $active = request()->routeIs($item['route']); @endphp
                <a href="{{ route($item['route']) }}" title="{{ $item['label'] }}"
                    class="flex items-center gap-3 px-2 py-2.5 rounded-xl text-sm font-medium transition-colors group
                           {{ $active ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/8 hover:text-white/90' }}
                           lg:px-3 justify-center lg:justify-start">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="flex-shrink-0">
                        {!! $item['icon'] !!}
                    </svg>
                    <span class="hidden lg:block truncate">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="px-2 pb-4">
            <form action="{{ route('participant.logout') }}" method="POST">
                @csrf
                <button type="submit" title="Quitter"
                    class="w-full flex items-center gap-3 px-2 py-2.5 rounded-xl text-white/40 hover:text-red-300 hover:bg-white/8 transition-colors text-sm justify-center lg:justify-start lg:px-3">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="hidden lg:block text-xs">Quitter la session</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Contenu principal --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

        {{-- Header --}}
        <header class="bg-white border-b border-slate-100 px-4 lg:px-6 h-14 flex items-center justify-between shrink-0">
            <div class="min-w-0">
                <h1 class="text-base font-black text-[#222a60] truncate">@yield('page-title', 'Session')</h1>
                @hasSection('page-subtitle')
                    <p class="text-xs text-slate-400 truncate">@yield('page-subtitle')</p>
                @endif
            </div>
            <div class="flex items-center gap-3 shrink-0">
                @if(session('participant.id_groupe') > 0)
                    <span class="hidden sm:flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#222a60]/10 text-[#222a60] text-xs font-bold">
                        Groupe {{ chr(64 + session('participant.id_groupe')) }}
                    </span>
                @endif
                <span class="hidden sm:flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">
                    {{ session('participant.pseudo') }}
                </span>
            </div>
        </header>

        <main class="flex-1 {{ request()->routeIs('participant.map') || request()->routeIs('participant.analyses') ? 'p-0 overflow-hidden flex flex-col' : 'p-4 sm:p-6 overflow-y-auto' }}">
            @yield('content')
        </main>

    </div>
</div>

@stack('scripts')
</body>
</html>
