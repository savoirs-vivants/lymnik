@extends('layouts.mobile')
@section('title', 'Carte des points')

@section('content')

    <div id="app-shell"
        class="relative w-full h-[100dvh] flex flex-col overflow-hidden bg-slate-50 font-grotesk text-slate-900">

        <div id="map" class="absolute inset-0 z-0 outline-none bg-slate-200"></div>

        <div id="top-bar"
            class="absolute top-0 inset-x-0 z-10 pt-[max(52px,env(safe-area-inset-top))] px-4 pb-3 pointer-events-none">
            <div class="flex items-center gap-2.5 pointer-events-auto">
                <div
                    class="flex-1 flex items-center gap-2 bg-white/95 backdrop-blur-md rounded-2xl px-3.5 h-[46px] shadow-[0_4px_20px_rgba(34,42,96,0.12)] border border-sv-blue/5">
                    <svg class="text-slate-400 shrink-0" width="16" height="16" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" stroke-width="2" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35" />
                    </svg>
                    <input type="text" placeholder="Rechercher un cours d'eau..."
                        class="bg-transparent border-none outline-none w-full text-sm text-slate-800 placeholder-slate-400 font-grotesk">
                </div>
                <div
                    class="w-[46px] h-[46px] rounded-full bg-sv-blue flex items-center justify-center font-mono text-[13px] font-bold text-white shrink-0 shadow-[0_4px_16px_rgba(34,42,96,0.25)] cursor-pointer transition-transform active:scale-[0.93] select-none">
                    SV
                </div>
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

        <div id="bottom-sheet"
            class="absolute bottom-0 inset-x-0 z-20 bg-white rounded-t-[20px] shadow-[0_-8px_40px_rgba(34,42,96,0.14)] translate-y-full [&.open]:translate-y-0 transition-transform duration-[380ms] ease-[cubic-bezier(0.34,1.1,0.64,1)] pb-[env(safe-area-inset-bottom,12px)]">
            <div class="w-9 h-1 bg-slate-200 rounded-full mx-auto mt-3"></div>
            <div class="p-4 px-5">
                <div class="flex items-center justify-between mb-0.5">
                    <div>
                        <div class="font-mono text-[10px] text-slate-400 tracking-wide">48.8153° N · 7.7884° E</div>
                        <div class="text-[10px] text-slate-400">Participant</div>
                    </div>
                    <button id="sheet-close-btn"
                        class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 active:bg-slate-200 transition-colors shrink-0">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <h2 class="text-[22px] font-bold text-sv-blue mt-1 mb-2.5 font-grotesk">La Moder</h2>
                <div
                    class="inline-flex items-center gap-1.5 bg-blue-50 rounded-full px-3 py-1.5 text-xs font-semibold text-blue-500 mb-4">
                    <span class="w-[7px] h-[7px] rounded-full bg-blue-500"></span> Qualité modérée
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 text-center">
                        <div class="text-[10px] text-slate-400 font-mono uppercase tracking-wide mb-1">Nitrates</div>
                        <div class="text-xl font-bold text-sv-blue leading-none">24<span
                                class="text-[10px] font-normal text-slate-400 ml-0.5">mg/L</span></div>
                    </div>
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 text-center">
                        <div class="text-[10px] text-slate-400 font-mono uppercase tracking-wide mb-1">pH</div>
                        <div class="text-xl font-bold text-sv-blue leading-none">7.4</div>
                    </div>
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 text-center">
                        <div class="text-[10px] text-slate-400 font-mono uppercase tracking-wide mb-1">Turbidité</div>
                        <div class="text-xl font-bold text-sv-blue leading-none">3.2<span
                                class="text-[10px] font-normal text-slate-400 ml-0.5">NTU</span></div>
                    </div>
                </div>
            </div>
        </div>

        <nav id="bottom-nav"
            class="absolute bottom-0 inset-x-0 z-[15] bg-white/95 backdrop-blur-md border-t border-sv-blue/5 flex justify-around items-center pt-2.5 pb-[calc(10px+env(safe-area-inset-bottom,0px))] translate-y-0 [&.hidden-nav]:translate-y-full transition-transform duration-[380ms] ease-[cubic-bezier(0.34,1.1,0.64,1)]">
            <a href="#"
                class="nav-item active group flex flex-col items-center gap-[3px] cursor-pointer px-5 py-1 rounded-xl transition-colors active:bg-slate-100 select-none no-underline">
                <svg class="text-slate-400 transition-colors group-[.active]:text-sv-blue" width="22" height="22"
                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
                <span
                    class="text-[10px] font-semibold text-slate-400 transition-colors group-[.active]:text-sv-blue">Carte</span>
            </a>
            <a href="#"
                class="nav-item group flex flex-col items-center gap-[3px] cursor-pointer px-5 py-1 rounded-xl transition-colors active:bg-slate-100 select-none no-underline">
                <svg class="text-slate-400 transition-colors group-[.active]:text-sv-blue" width="22" height="22"
                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-[10px] font-semibold text-slate-400 transition-colors group-[.active]:text-sv-blue">Mes
                    analyses</span>
            </a>
        </nav>
    </div>
@endsection
