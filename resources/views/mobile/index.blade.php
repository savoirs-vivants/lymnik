@extends('layouts.mobile')
@section('title', 'Carte des points')
@section('content')

<div id="app-shell"
    class="relative w-full h-[100dvh] flex flex-col overflow-hidden bg-slate-50 font-grotesk text-slate-900">

    <div id="map" class="absolute inset-0 z-0 outline-none bg-slate-200"></div>

    <div id="top-bar" class="absolute top-0 inset-x-0 z-50 pt-[max(52px,env(safe-area-inset-top))] px-4 pb-3 pointer-events-none">
        <div class="flex items-center gap-2.5 pointer-events-auto relative z-20">
            <div class="flex-1 flex items-center gap-2 bg-white/95 backdrop-blur-md rounded-2xl px-3.5 h-[46px] shadow-[0_4px_20px_rgba(34,42,96,0.12)] border border-sv-blue/5 relative">
                <svg class="text-slate-400 shrink-0" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" stroke-width="2"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35"/>
                </svg>
                <input type="text" id="search-input" placeholder="Commune ou Code Postal" autocomplete="off"
                    class="bg-transparent border-none outline-none w-full text-sm text-slate-800 placeholder-slate-400 font-grotesk">

                <div id="search-results" class="absolute top-[52px] left-0 right-0 bg-white rounded-xl shadow-[0_12px_40px_rgba(34,42,96,0.15)] border border-slate-100 overflow-hidden hidden">
                    </div>
            </div>

            @guest
                <a href="{{ route('login', ['source' => 'mobile']) }}"
                    class="w-[46px] h-[46px] rounded-full bg-slate-100 flex items-center justify-center text-slate-400 shrink-0 shadow-[0_4px_16px_rgba(0,0,0,0.08)] border border-slate-200 no-underline transition-transform active:scale-[0.93]">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </a>
            @endguest

            @auth
                <a href="{{ route('mobile.profil') }}"
                    class="w-[46px] h-[46px] rounded-full bg-sv-blue flex items-center justify-center font-grotesk text-[13px] font-bold text-white shrink-0 shadow-[0_4px_16px_rgba(34,42,96,0.25)] no-underline border-2 border-white transition-transform active:scale-[0.93]">
                    {{ strtoupper(substr(Auth::user()->firstname, 0, 1) . substr(Auth::user()->name, 0, 1)) }}
                </a>
            @endauth
        </div>

        <div class="flex gap-2 mt-2.5 pl-0.5 pointer-events-auto overflow-x-auto no-scrollbar">
                <div
                    class="pill active flex items-center gap-1.5 bg-white/95 backdrop-blur-md rounded-full px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-[0_2px_10px_rgba(0,0,0,0.08)] border-[1.5px] border-transparent cursor-pointer transition-all active:scale-95 select-none whitespace-nowrap [&.active]:border-current [&.active]:text-[#16987c] [&.active]:bg-[#16987c]/10">
                    <span class="w-[7px] h-[7px] rounded-full bg-[#16987c]"></span> Bonne
                </div>
                <div
                    class="pill active flex items-center gap-1.5 bg-white/95 backdrop-blur-md rounded-full px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-[0_2px_10px_rgba(0,0,0,0.08)] border-[1.5px] border-transparent cursor-pointer transition-all active:scale-95 select-none whitespace-nowrap [&.active]:border-current [&.active]:text-amber-500 [&.active]:bg-amber-500/10">
                    <span class="w-[7px] h-[7px] rounded-full bg-amber-500"></span> Modérée
                </div>
                <div
                    class="pill active flex items-center gap-1.5 bg-white/95 backdrop-blur-md rounded-full px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-[0_2px_10px_rgba(0,0,0,0.08)] border-[1.5px] border-transparent cursor-pointer transition-all active:scale-95 select-none whitespace-nowrap [&.active]:border-current [&.active]:text-red-500 [&.active]:bg-red-500/10">
                    <span class="w-[7px] h-[7px] rounded-full bg-red-500"></span> Mauvaise
                </div>
            </div>
    </div>

    <button id="btn-locate" class="absolute right-4 bottom-28 z-[1000] w-12 h-12 flex items-center justify-center rounded-full shadow-[0_4px_16px_rgba(0,0,0,0.15)] border border-white/80 bg-white/90 backdrop-blur-md text-[#222a60] active:bg-[#1565c0] active:text-white transition-colors cursor-pointer outline-none group">
        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" class="transition-transform group-active:scale-95">
            <circle cx="12" cy="12" r="3"/>
            <path d="M12 2v3m0 14v3M2 12h3m14 0h3"/>
            <circle cx="12" cy="12" r="7" stroke-dasharray="3 2"/>
        </svg>
    </button>

    <div id="tap-hint"
         class="absolute bottom-24 left-1/2 -translate-x-1/2 z-10 bg-sv-blue/85 backdrop-blur-md text-white text-[11px] font-mono px-4 py-2 rounded-full pointer-events-none whitespace-nowrap transition-opacity duration-400 [&.fade-out]:opacity-0">
        Appuyez sur la carte pour créer une analyse
    </div>

    <div id="bottom-sheet"
        class="absolute bottom-0 inset-x-0 z-20 bg-white rounded-t-[20px] shadow-[0_-8px_40px_rgba(34,42,96,0.14)] translate-y-full [&.open]:translate-y-0 transition-transform duration-[380ms] ease-[cubic-bezier(0.34,1.1,0.64,1)] pb-[env(safe-area-inset-bottom,12px)]">
        <div class="w-9 h-1 bg-slate-200 rounded-full mx-auto mt-3"></div>
        <div class="p-4 px-5">
            <div class="flex items-start justify-between mb-1">
                <div>
                    <div class="sheet-coords-text font-mono text-[10px] text-slate-400 tracking-wide">—</div>
                    <div class="sheet-type-text text-[10px] text-slate-400">—</div>
                </div>
                <button id="sheet-close-btn"
                    class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 active:bg-slate-200 transition-colors shrink-0 mt-0.5">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <h2 class="sheet-river-name text-[20px] font-bold text-sv-blue mt-1 mb-3 font-grotesk">—</h2>
            <div class="sheet-analyse-info">
            </div>
        </div>
    </div>

    <div id="create-card">
        <div class="create-card-handle"></div>
        <div class="create-card-inner">
            <div class="create-card-pin">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div class="create-card-info">
                <div class="create-card-river" id="cc-river">Cours d'eau</div>
                <div class="create-card-coords">
                    <span id="cc-lat">—</span> · <span id="cc-lng">—</span>
                </div>
            </div>
        </div>
        <div class="create-card-actions">
            <button class="cc-btn-cancel" id="cc-cancel">Annuler</button>
            <a href="#" class="cc-btn-create" id="cc-link">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Créer une analyse
            </a>
        </div>
    </div>

    <nav id="bottom-nav"
        class="absolute bottom-0 inset-x-0 z-[15] bg-white/95 backdrop-blur-md border-t border-sv-blue/5 flex justify-around items-center pt-2.5 pb-[calc(10px+env(safe-area-inset-bottom,0px))] translate-y-0 [&.hidden-nav]:translate-y-full transition-transform duration-[380ms] ease-[cubic-bezier(0.34,1.1,0.64,1)]">
        <a href="{{ route('index_mobile')}}" class="nav-item active group flex flex-col items-center gap-[3px] cursor-pointer px-5 py-1 rounded-xl transition-colors active:bg-slate-100 select-none no-underline">
            <svg class="text-slate-400 transition-colors group-[.active]:text-sv-blue" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            <span class="text-[10px] font-semibold text-slate-400 transition-colors group-[.active]:text-sv-blue">Carte</span>
        </a>
        <a href="/" class="nav-item group flex flex-col items-center gap-[3px] cursor-pointer px-5 py-1 rounded-xl transition-colors active:bg-slate-100 select-none no-underline">
            <svg class="text-slate-400 transition-colors group-[.active]:text-sv-blue" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="text-[10px] font-semibold text-slate-400 transition-colors group-[.active]:text-sv-blue">Retour à l'accueil</span>
        </a>
        <a href="{{ route('mobile.analyses') }}" class="nav-item group flex flex-col items-center gap-[3px] cursor-pointer px-5 py-1 rounded-xl transition-colors active:bg-slate-100 select-none no-underline">
            <svg class="text-slate-400 transition-colors group-[.active]:text-sv-blue" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span class="text-[10px] font-semibold text-slate-400 transition-colors group-[.active]:text-sv-blue">Mes analyses</span>
        </a>
    </nav>

</div>

<script>
    window.mapPoints          = @json($pointsJson);
    window.mapRivers          = @json($riversJson);
    window.createAnalyseUrl   = "{{ route('mobile.analyse.create') }}";
    window.nearestRiverUrl    = "{{ route('mobile.cours-d-eau.nearest') }}";
    window.userAuthenticated  = {{ auth()->check() ? 'true' : 'false' }};
    window.loginUrl           = "{{ route('login', ['source' => 'mobile']) }}";
</script>

@endsection
