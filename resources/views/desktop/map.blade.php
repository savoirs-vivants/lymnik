@extends('layouts.desktop')

@section('title', 'Carte Interactive')
@section('page-title', 'Carte Interactive')

@section('content')
    <div class="relative h-[calc(100vh-64px)] w-full overflow-hidden font-grotesk flex flex-col p-0 m-0">

        <div id="map" class="flex-1 w-full z-0 outline-none bg-slate-200"></div>

        <div class="absolute top-6 left-6 z-[1001] w-full max-w-sm space-y-4 pointer-events-none">

            <div
                class="bg-white/95 backdrop-blur shadow-lg rounded-2xl p-2 flex items-center border border-slate-100 pointer-events-auto">
                <input type="text" id="search-input" placeholder="Rechercher un lieu..."
                    class="flex-1 px-4 bg-transparent outline-none text-sm font-bold text-slate-800 placeholder-slate-400">
                <div id="search-results"
                    class="absolute top-full left-0 right-0 bg-white mt-2 rounded-xl shadow-xl hidden overflow-hidden border border-slate-100">
                </div>
            </div>

            <div class="flex flex-wrap gap-2 pointer-events-auto">
                @foreach (['tres_bon' => '#3b82f6', 'bon' => '#16987c', 'passable' => '#eab308', 'mediocre' => '#f97316', 'mauvais' => '#ef4444'] as $q => $color)
                    <div data-quality="{{ $q }}"
                        class="pill cursor-pointer bg-white/90 px-3 py-1.5 rounded-full shadow-sm text-[10px] font-black uppercase tracking-wider flex items-center gap-2 border border-transparent transition-all active:scale-95 [&.active]:border-slate-200 hover:bg-white">
                        <span class="w-2 h-2 rounded-full" style="background: {{ $color }}"></span>
                        {{ str_replace('_', ' ', $q) }}
                    </div>
                @endforeach
            </div>
        </div>

        <div id="bottom-sheet"
            class="absolute top-5 right-5 bottom-5 w-[350px] bg-white z-[1000] rounded-3xl shadow-[0_10px_50px_rgba(34,42,96,0.15)] border border-slate-100 translate-x-[120%] transition-transform duration-500 ease-out flex flex-col overflow-hidden [&.open]:translate-x-0">

            <div
                class="p-6 overflow-y-auto flex-1 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                <div class="flex justify-between items-start mb-4">
                    <span class="sheet-coords-text font-mono text-[10px] text-slate-400 tracking-widest uppercase"></span>
                    <button id="sheet-close-btn"
                        class="text-slate-300 hover:text-slate-600 transition-colors cursor-pointer outline-none">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="3"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="sheet-type-text text-[10px] font-black text-sv-blue uppercase mb-1"></div>
                <h2 class="sheet-river-name text-2xl font-black text-sv-blue mb-6 leading-tight"></h2>
                <div class="sheet-analyse-info"></div>
            </div>
        </div>

        <div id="create-card"
            class="group !absolute !bottom-6 !left-[88px] !top-auto !right-auto !w-auto !h-auto !p-0 !m-0 !rounded-none !shadow-none !transform-none !block z-[1000] pointer-events-none opacity-0 transition-opacity duration-300 ease-out bg-transparent [&.show]:opacity-100 [&.show]:pointer-events-auto">

            <div
                class="bg-white rounded-3xl shadow-[0_20px_60px_rgba(34,42,96,0.18)] border border-slate-100 p-6 w-[400px] flex flex-col gap-5 transform translate-y-4 transition-transform duration-300 ease-out group-[.show]:translate-y-0">

                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-full bg-sv-blue/10 flex items-center justify-center text-sv-blue shrink-0">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="pr-2">
                            <h3 id="cc-river" class="font-grotesk font-bold text-[16px] text-sv-blue leading-tight">
                                Position sélectionnée</h3>
                            <p class="text-[11px] text-slate-400 font-mono mt-1 uppercase tracking-widest">Nouvelle Analyse
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 flex justify-around text-center">
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 font-mono uppercase tracking-widest mb-1">Latitude
                        </div>
                        <div id="cc-lat" class="text-[15px] font-bold text-sv-blue font-mono">0.0000°</div>
                    </div>
                    <div class="w-px bg-slate-200"></div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 font-mono uppercase tracking-widest mb-1">Longitude
                        </div>
                        <div id="cc-lng" class="text-[15px] font-bold text-sv-blue font-mono">0.0000°</div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button id="cc-cancel"
                        class="flex-1 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl text-[13px] font-bold transition-colors cursor-pointer outline-none">
                        Annuler
                    </button>

                    <a id="cc-link" href="#"
                        class="flex-[1.5] py-3 bg-sv-blue hover:bg-[#1a2050] text-white rounded-xl text-[13px] font-bold text-center no-underline transition-transform active:scale-95 shadow-md flex items-center justify-center gap-2 cursor-pointer outline-none">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Saisir ici
                    </a>
                </div>
            </div>
        </div>

        <button id="btn-locate"
            class="absolute left-6 bottom-6 z-[1001] w-12 h-12 bg-white rounded-2xl shadow-xl flex items-center justify-center text-sv-blue hover:bg-slate-50 transition-all outline-none cursor-pointer border border-slate-100">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="3" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v3m0 14v3M2 12h3m14 0h3" />
            </svg>
        </button>
    </div>

    <script>
        window.mapPoints = @json($pointsJson ?? []);
        window.mapRivers = @json($riversJson ?? []);
        window.mapCapteurs = {!! $capteursJson ?? '[]' !!};
        window.createAnalyseUrl = "{{ route('mobile.analyse.create') }}";
        window.userAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
        window.loginUrl = "{{ route('login') }}";
    </script>

@endsection
