@extends('layouts.mobile')
@section('title', 'Carte des points')
@section('content')

<div id="app-shell"
    class="relative w-full h-[100dvh] flex flex-col overflow-hidden bg-slate-50 font-grotesk text-slate-900 md:flex-row">

    <aside class="hidden md:flex md:flex-col md:w-64 lg:w-72 md:h-full md:shrink-0 bg-white border-r border-slate-100 shadow-[2px_0_10px_rgba(0,0,0,0.04)] z-20 overflow-hidden">

        <div class="px-5 py-4 border-b border-slate-100 shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-[#222a60] flex items-center justify-center">
                    <svg width="14" height="14" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <span class="font-bold text-[#222a60] text-base tracking-tight">Lymnik</span>
            </div>
            <p class="text-[10px] text-slate-400 font-mono mt-1">Suivi qualité de l'eau</p>
        </div>

        <div class="px-4 py-3 border-b border-slate-100 shrink-0">
            @guest
                <a href="{{ route('login', ['source' => 'mobile']) }}"
                    class="flex items-center gap-2.5 text-sm text-slate-500 hover:text-[#222a60] transition-colors no-underline group">
                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center group-hover:bg-[#222a60]/10 transition-colors">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <span class="font-medium">Se connecter</span>
                </a>
            @endguest
            @auth
                <a href="{{ route('profil') }}" class="flex items-center gap-2.5 no-underline group">
                    <div class="w-8 h-8 rounded-full bg-[#222a60] flex items-center justify-center text-[11px] font-bold text-white shrink-0">
                        {{ strtoupper(substr(Auth::user()->firstname, 0, 1) . substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-slate-800 truncate">{{ Auth::user()->firstname }} {{ Auth::user()->name }}</div>
                        <div class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email }}</div>
                    </div>
                </a>
            @endauth
        </div>

        <div class="px-4 py-3 border-b border-slate-100 shrink-0">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" stroke-width="2"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35"/>
                </svg>
                <input type="text" id="search-input" placeholder="Commune ou code postal" autocomplete="off"
                    class="w-full pl-8 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 outline-none focus:ring-2 focus:ring-[#222a60]/15 focus:border-[#222a60] transition-all">
                <div id="search-results" class="absolute top-[calc(100%+4px)] left-0 right-0 bg-white rounded-xl shadow-[0_12px_40px_rgba(34,42,96,0.15)] border border-slate-100 overflow-hidden hidden z-50"></div>
            </div>
            <button id="btn-locate-desk" class="btn-locate mt-2 w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-xs font-semibold text-[#222a60] bg-slate-50 border border-slate-200 hover:bg-slate-100 transition-colors cursor-pointer">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M12 2v3m0 14v3M2 12h3m14 0h3"/>
                    <circle cx="12" cy="12" r="7" stroke-dasharray="3 2"/>
                </svg>
                Me géolocaliser
            </button>
        </div>

        @auth
        <div class="px-4 py-3 border-b border-slate-100 shrink-0">
            <div class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-2">Signalement</div>
            <button id="btn-declare-coulee-desk"
                class="w-full flex items-center gap-2 px-3 py-2.5 rounded-xl text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 hover:bg-amber-100 transition-colors cursor-pointer">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                Signaler un problème ou une amélioration du cours d'eau
            </button>
        </div>
        @endauth

        <div class="px-4 py-3 border-b border-slate-100 shrink-0">
            <div class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-2">Filtrer par qualité</div>
            <div class="flex flex-col gap-1">
                <div data-quality="tres_bon" class="pill flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-slate-500 border border-transparent hover:border-[#3b82f6]/30 hover:text-[#3b82f6] hover:bg-[#3b82f6]/5 cursor-pointer transition-all select-none [&.active]:border-[#3b82f6]/40 [&.active]:text-[#3b82f6] [&.active]:bg-[#3b82f6]/8">
                    <span class="w-2 h-2 rounded-full bg-[#3b82f6] shrink-0"></span> Très bon
                </div>
                <div data-quality="bon" class="pill flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-slate-500 border border-transparent hover:border-[#10b981]/30 hover:text-[#10b981] hover:bg-[#10b981]/5 cursor-pointer transition-all select-none [&.active]:border-[#10b981]/40 [&.active]:text-[#10b981] [&.active]:bg-[#10b981]/8">
                    <span class="w-2 h-2 rounded-full bg-[#10b981] shrink-0"></span> Bon
                </div>
                <div data-quality="passable" class="pill flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-slate-500 border border-transparent hover:border-[#eab308]/30 hover:text-[#eab308] hover:bg-[#eab308]/5 cursor-pointer transition-all select-none [&.active]:border-[#eab308]/40 [&.active]:text-[#eab308] [&.active]:bg-[#eab308]/8">
                    <span class="w-2 h-2 rounded-full bg-[#eab308] shrink-0"></span> Passable
                </div>
                <div data-quality="mediocre" class="pill flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-slate-500 border border-transparent hover:border-[#f97316]/30 hover:text-[#f97316] hover:bg-[#f97316]/5 cursor-pointer transition-all select-none [&.active]:border-[#f97316]/40 [&.active]:text-[#f97316] [&.active]:bg-[#f97316]/8">
                    <span class="w-2 h-2 rounded-full bg-[#f97316] shrink-0"></span> Médiocre
                </div>
                <div data-quality="mauvais" class="pill flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-slate-500 border border-transparent hover:border-[#ef4444]/30 hover:text-[#ef4444] hover:bg-[#ef4444]/5 cursor-pointer transition-all select-none [&.active]:border-[#ef4444]/40 [&.active]:text-[#ef4444] [&.active]:bg-[#ef4444]/8">
                    <span class="w-2 h-2 rounded-full bg-[#ef4444] shrink-0"></span> Mauvais
                </div>
            </div>
        </div>

        <div id="sidebar-info" class="flex-1 overflow-y-auto"></div>

        @auth
        <div class="border-t border-slate-100 p-3 shrink-0">
            <nav class="flex flex-col gap-0.5">
                <a href="{{ route('mobile') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-[#222a60] bg-[#222a60]/6 no-underline">
                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Carte
                </a>
                <a href="{{ route('analyses') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:text-[#222a60] hover:bg-slate-50 transition-colors no-underline">
                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Mes analyses
                </a>
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:text-[#222a60] hover:bg-slate-50 transition-colors no-underline">
                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
            </nav>
        </div>
        @endauth
    </aside>

    <div class="relative flex-1 h-full overflow-hidden">

        <div id="map" class="absolute inset-0 z-0 outline-none bg-slate-200"></div>

        <div id="top-bar" class="absolute top-0 inset-x-0 z-50 pt-[max(52px,env(safe-area-inset-top))] px-4 pb-3 pointer-events-none md:hidden">
            <div class="flex items-center gap-2.5 pointer-events-auto relative z-20">
                <div class="flex-1 flex items-center gap-2 bg-white/95 backdrop-blur-md rounded-2xl px-3.5 h-[46px] shadow-[0_4px_20px_rgba(34,42,96,0.12)] border border-sv-blue/5 relative">
                    <svg class="text-slate-400 shrink-0" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" stroke-width="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35"/>
                    </svg>
                    <input type="text" id="search-input" placeholder="Commune ou Code Postal" autocomplete="off"
                        class="bg-transparent border-none outline-none w-full text-sm text-slate-800 placeholder-slate-400 font-grotesk">
                    <div id="search-results" class="absolute top-[52px] left-0 right-0 bg-white rounded-xl shadow-[0_12px_40px_rgba(34,42,96,0.15)] border border-slate-100 overflow-hidden hidden"></div>
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
                    <a href="{{ route('profil') }}"
                        class="w-[46px] h-[46px] rounded-full bg-sv-blue flex items-center justify-center font-grotesk text-[13px] font-bold text-white shrink-0 shadow-[0_4px_16px_rgba(34,42,96,0.25)] no-underline border-2 border-white transition-transform active:scale-[0.93]">
                        {{ strtoupper(substr(Auth::user()->firstname, 0, 1) . substr(Auth::user()->name, 0, 1)) }}
                    </a>
                @endauth
            </div>

            <button id="btn-locate-mobile" class="btn-locate mt-2.5 pointer-events-auto w-full flex items-center justify-center gap-2 bg-white/95 backdrop-blur-md rounded-2xl px-3.5 h-[42px] shadow-[0_4px_20px_rgba(34,42,96,0.12)] border border-sv-blue/5 text-[#222a60] text-sm font-semibold active:scale-[0.98] transition-transform">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" class="shrink-0">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M12 2v3m0 14v3M2 12h3m14 0h3"/>
                    <circle cx="12" cy="12" r="7" stroke-dasharray="3 2"/>
                </svg>
                Me géolocaliser
            </button>

            @auth
            <button id="btn-declare-coulee"
                class="mt-2.5 pointer-events-auto w-full flex items-center justify-center gap-2 px-3.5 h-[42px] rounded-2xl shadow-[0_4px_16px_rgba(0,0,0,0.1)] border border-amber-300 bg-amber-50/95 backdrop-blur-md text-amber-700 font-semibold text-xs active:scale-[0.98] transition-transform cursor-pointer">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                Signaler un problème ou une amélioration du cours d'eau
            </button>
            @endauth

            <div class="flex gap-1.5 mt-2.5 px-4 pointer-events-auto overflow-x-auto [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <div data-quality="tres_bon" class="pill flex items-center gap-1 bg-white/95 backdrop-blur-md rounded-full px-2.5 py-1 text-[10px] font-semibold text-slate-500 shadow-sm border border-slate-100 cursor-pointer transition-all active:scale-95 select-none whitespace-nowrap [&.active]:border-[#3b82f6] [&.active]:text-[#3b82f6] [&.active]:bg-[#3b82f6]/10">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#3b82f6]"></span> Très bon
                </div>
                <div data-quality="bon" class="pill flex items-center gap-1 bg-white/95 backdrop-blur-md rounded-full px-2.5 py-1 text-[10px] font-semibold text-slate-500 shadow-sm border border-slate-100 cursor-pointer transition-all active:scale-95 select-none whitespace-nowrap [&.active]:border-[#10b981] [&.active]:text-[#10b981] [&.active]:bg-[#10b981]/10">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#10b981]"></span> Bon
                </div>
                <div data-quality="passable" class="pill flex items-center gap-1 bg-white/95 backdrop-blur-md rounded-full px-2.5 py-1 text-[10px] font-semibold text-slate-500 shadow-sm border border-slate-100 cursor-pointer transition-all active:scale-95 select-none whitespace-nowrap [&.active]:border-[#eab308] [&.active]:text-[#eab308] [&.active]:bg-[#eab308]/10">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#eab308]"></span> Passable
                </div>
                <div data-quality="mediocre" class="pill flex items-center gap-1 bg-white/95 backdrop-blur-md rounded-full px-2.5 py-1 text-[10px] font-semibold text-slate-500 shadow-sm border border-slate-100 cursor-pointer transition-all active:scale-95 select-none whitespace-nowrap [&.active]:border-[#f97316] [&.active]:text-[#f97316] [&.active]:bg-[#f97316]/10">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#f97316]"></span> Médiocre
                </div>
                <div data-quality="mauvais" class="pill flex items-center gap-1 bg-white/95 backdrop-blur-md rounded-full px-2.5 py-1 text-[10px] font-semibold text-slate-500 shadow-sm border border-slate-100 cursor-pointer transition-all active:scale-95 select-none whitespace-nowrap [&.active]:border-[#ef4444] [&.active]:text-[#ef4444] [&.active]:bg-[#ef4444]/10">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#ef4444]"></span> Mauvais
                </div>
            </div>
        </div>

        <div id="tap-hint" class="absolute bottom-24 left-1/2 -translate-x-1/2 z-10 bg-sv-blue/85 backdrop-blur-md text-white text-[11px] font-mono px-4 py-2 rounded-full pointer-events-none whitespace-nowrap transition-opacity duration-400 [&.fade-out]:opacity-0 md:bottom-6">
            Appuyez sur la carte pour créer une analyse
        </div>

        <div id="bottom-sheet"
            class="absolute bottom-0 inset-x-0 z-20 bg-white rounded-t-[20px] shadow-[0_-8px_40px_rgba(34,42,96,0.14)] translate-y-full [&.open]:translate-y-0 transition-transform duration-[380ms] ease-[cubic-bezier(0.34,1.1,0.64,1)] pb-[env(safe-area-inset-bottom,12px)]
                   md:bottom-4 md:top-4 md:left-auto md:right-4 md:w-80 md:rounded-2xl md:inset-x-auto md:shadow-[0_8px_40px_rgba(34,42,96,0.18)]">
            <div class="w-9 h-1 bg-slate-200 rounded-full mx-auto mt-3 md:hidden"></div>
            <div class="p-4 px-5">
                <div class="flex items-start justify-between mb-1">
                    <div>
                        <div class="sheet-type-text text-[10px] text-slate-400">—</div>
                    </div>
                    <button id="sheet-close-btn" class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 active:bg-slate-200 transition-colors shrink-0 mt-0.5">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M18 6L6 18M6 6l12 12"/></svg>
                    </button>
                </div>
                <h2 class="sheet-river-name text-[20px] font-bold text-sv-blue mt-1 mb-3 font-grotesk">—</h2>
                <div class="sheet-analyse-info"></div>
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

        <div id="bt-modal" class="absolute inset-0 z-[100] bg-slate-50 flex flex-col transition-transform duration-300 translate-y-full">
            <div class="pt-[max(40px,env(safe-area-inset-top))] md:pt-4 px-4 pb-3 bg-white flex justify-between items-center border-b border-slate-100 shadow-sm">
                <h2 class="text-lg font-bold text-sv-blue flex items-center gap-2">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M7 7l10 10-5 5V2l5 5-10 10"/></svg>
                    Dashboard Capteur
                </h2>
                <button id="bt-close" class="w-8 h-8 flex items-center justify-center bg-slate-100 rounded-full text-slate-500 active:bg-slate-200">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 p-4 flex flex-col overflow-y-auto no-scrollbar md:max-w-lg md:mx-auto md:w-full">
                <button id="bt-action-connect" class="w-full bg-sv-blue text-white font-bold py-3.5 rounded-xl active:scale-95 transition-transform flex justify-center items-center gap-2 text-sm shadow-md mb-4">
                    1. Connecter le PCB
                </button>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">2. Commandes</h3>
                <div class="flex gap-2 mb-4">
                    <button id="bt-action-start" class="flex-1 bg-[#16987c] text-white font-bold py-2.5 rounded-xl active:scale-95 transition-transform text-sm shadow-sm flex justify-center items-center gap-1">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3l14 9-14 9V3z"/></svg>
                        Démarrer
                    </button>
                    <button id="bt-action-stop" class="flex-1 bg-red-500 text-white font-bold py-2.5 rounded-xl active:scale-95 transition-transform text-sm shadow-sm flex justify-center items-center gap-1">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="6" y="6" width="12" height="12" rx="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Éteindre
                    </button>
                </div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">3. Valeurs en temps réel</h3>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-white p-3 rounded-xl shadow-[0_2px_10px_rgba(0,0,0,0.04)] border border-slate-100 border-l-4 border-l-sv-blue col-span-2">
                        <div class="text-[10px] font-bold text-slate-400 uppercase">UID Capteur</div>
                        <div class="text-sm font-bold text-sv-blue font-mono truncate" id="valUid">--</div>
                    </div>
                    <div class="bg-white p-3 rounded-xl shadow-[0_2px_10px_rgba(0,0,0,0.04)] border border-slate-100 border-l-4 border-l-amber-500">
                        <div class="text-[10px] font-bold text-slate-400 uppercase">Turbidité</div>
                        <div class="text-lg font-bold text-amber-500" id="valTurb">-- <span class="text-xs font-normal">raw</span></div>
                    </div>
                    <div class="bg-white p-3 rounded-xl shadow-[0_2px_10px_rgba(0,0,0,0.04)] border border-slate-100 border-l-4 border-l-violet-500">
                        <div class="text-[10px] font-bold text-slate-400 uppercase">Conductivité</div>
                        <div class="text-lg font-bold text-violet-500" id="valCond">-- <span class="text-xs font-normal">raw</span></div>
                    </div>
                    <div class="bg-white p-3 rounded-xl shadow-[0_2px_10px_rgba(0,0,0,0.04)] border border-slate-100 border-l-4 border-l-orange-500">
                        <div class="text-[10px] font-bold text-slate-400 uppercase">Température</div>
                        <div class="text-lg font-bold text-orange-500" id="valTemp">-- °C</div>
                    </div>
                    <div class="bg-white p-3 rounded-xl shadow-[0_2px_10px_rgba(0,0,0,0.04)] border border-slate-100 border-l-4 border-l-cyan-500">
                        <div class="text-[10px] font-bold text-slate-400 uppercase">Hauteur</div>
                        <div class="text-lg font-bold text-cyan-500" id="valHaut">-- cm</div>
                    </div>
                    <div class="col-span-2 bg-white p-3 rounded-xl shadow-[0_2px_10px_rgba(0,0,0,0.04)] border border-slate-100 border-l-4 border-l-blue-500">
                        <div class="text-[10px] font-bold text-slate-400 uppercase">Débit</div>
                        <div class="text-lg font-bold text-blue-500" id="valDebit">-- L/min</div>
                    </div>
                </div>
                <div class="flex justify-between items-end mb-2">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Console UART</h3>
                    <button id="bt-action-download" class="text-xs font-bold text-sv-blue active:opacity-50">Télécharger log</button>
                </div>
                <textarea id="bt-console" class="w-full h-40 bg-[#1e1e1e] text-[#00ff00] font-mono text-xs p-3 rounded-xl outline-none resize-none shadow-inner" readonly>Prêt à scanner...</textarea>

                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-4 mb-2">4. Enregistrer en base</h3>
                <button id="bt-action-sync"
                    class="w-full bg-[#222a60] text-white font-bold py-3 rounded-xl active:scale-95 transition-transform flex justify-center items-center gap-2 text-sm shadow-md">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Synchroniser avec la BDD
                </button>
                <div id="bt-sync-status" class="hidden mt-2 p-3 rounded-xl text-xs font-mono"></div>
            </div>
        </div>

        @if (Auth::check())
        <nav id="bottom-nav" class="absolute bottom-0 inset-x-0 z-[15] bg-white/95 backdrop-blur-md border-t border-sv-blue/5 flex justify-around items-center pt-2.5 pb-[calc(10px+env(safe-area-inset-bottom,0px))] translate-y-0 [&.hidden-nav]:translate-y-full transition-transform duration-[380ms] ease-[cubic-bezier(0.34,1.1,0.64,1)] md:hidden">
            <a href="{{ route('mobile')}}" class="nav-item active group flex flex-col items-center gap-[3px] cursor-pointer px-5 py-1 rounded-xl transition-colors active:bg-slate-100 select-none no-underline">
                <svg class="text-slate-400 transition-colors group-[.active]:text-sv-blue" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <span class="text-[10px] font-semibold text-slate-400 transition-colors group-[.active]:text-sv-blue">Carte</span>
            </a>
            <a href="/" class="nav-item group flex flex-col items-center gap-[3px] cursor-pointer px-5 py-1 rounded-xl transition-colors active:bg-slate-100 select-none no-underline">
                <svg class="text-slate-400 transition-colors group-[.active]:text-sv-blue" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-[10px] font-semibold text-slate-400 transition-colors group-[.active]:text-sv-blue">Accueil</span>
            </a>
            <a href="{{ route('analyses') }}" class="nav-item group flex flex-col items-center gap-[3px] cursor-pointer px-5 py-1 rounded-xl transition-colors active:bg-slate-100 select-none no-underline">
                <svg class="text-slate-400 transition-colors group-[.active]:text-sv-blue" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span class="text-[10px] font-semibold text-slate-400 transition-colors group-[.active]:text-sv-blue">Mes analyses</span>
            </a>
            <button id="btn-open-bt" class="nav-item group flex flex-col items-center gap-[3px] cursor-pointer px-5 py-1 rounded-xl transition-colors active:bg-slate-100 select-none bg-transparent border-none outline-none">
                <svg class="text-slate-400 transition-colors group-active:text-sv-blue" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M7 7l10 10-5 5V2l5 5-10 10" />
                </svg>
                <span class="text-[10px] font-semibold text-slate-400 transition-colors group-active:text-sv-blue">Capteur</span>
            </button>
        </nav>
        @endif


        <div id="coulee-mode-overlay" class="hidden absolute inset-0 z-[60] flex flex-col pointer-events-none">
            <div class="pointer-events-auto bg-amber-500 text-white flex items-center justify-between px-4 py-3 shadow-lg">
                <div class="flex items-center gap-2 text-sm font-semibold">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    Touchez la carte pour placer l'emplacement du problème
                </div>
                <button id="coulee-mode-cancel" class="text-white/80 hover:text-white active:opacity-60 text-xs font-bold underline bg-transparent border-none cursor-pointer">Annuler</button>
            </div>
            <div id="coulee-confirm-bar" class="hidden pointer-events-auto mt-auto bg-white border-t border-slate-100 px-4 py-3 shadow-[0_-4px_20px_rgba(0,0,0,0.08)] flex items-center gap-3">
                <div class="flex-1 text-xs text-slate-600">
                    <div class="font-semibold text-slate-800">Point placé</div>
                    <div id="coulee-confirm-coords" class="font-mono text-slate-400">—</div>
                </div>
                <button id="coulee-confirm-cancel" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-500 bg-slate-100 active:bg-slate-200 transition-colors border-none cursor-pointer">Replacer</button>
                <button id="coulee-confirm-save" class="px-4 py-2 rounded-xl text-xs font-semibold text-white bg-amber-500 hover:bg-amber-600 active:scale-95 transition-all border-none cursor-pointer flex items-center gap-1.5">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Valider
                </button>
            </div>
        </div>

        <div id="coulee-details-modal" class="hidden absolute inset-0 z-[70] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 pointer-events-auto">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden flex flex-col">
                <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 text-sm">Détails du signalement</h3>
                    <button id="coulee-details-close" class="text-slate-400 hover:text-slate-600 active:scale-95 transition-transform bg-transparent border-none">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nature du signalement</label>
                        <select id="coulee-categorie" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                            <option value="probleme">Problème</option>
                            <option value="amelioration">Amélioration</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Type</label>
                        <select id="coulee-type" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                            <option value="">Sélectionnez un type...</option>
                        </select>

                        <input type="text" id="coulee-type-autre" placeholder="Précisez la nature du signalement..."
                            class="hidden mt-3 w-full bg-white border border-amber-200 rounded-xl px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all shadow-sm">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Date de l'événement</label>
                        <input type="date" id="coulee-date" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Photos (Optionnelles)</label>
                        <input type="file" id="coulee-image" accept="image/*" multiple class="w-full text-sm text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 transition-colors cursor-pointer">
                        <p class="text-[11px] text-slate-400 mt-1.5">Vous pouvez ajouter plusieurs photos.</p>
                        <div id="coulee-image-preview" class="hidden mt-2 flex flex-wrap gap-2"></div>
                    </div>
                </div>

                <div class="p-4 border-t border-slate-100 flex gap-3">
                    <button id="coulee-details-cancel" class="flex-1 py-3 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors border-none cursor-pointer">Annuler</button>
                    <button id="coulee-details-submit" class="flex-1 py-3 rounded-xl text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 active:scale-95 transition-all border-none cursor-pointer">
                        Enregistrer
                    </button>
                </div>
            </div>
        </div>

        @if (Auth::check())
        <button id="btn-open-bt-desk"
            class="hidden md:flex absolute bottom-4 left-4 z-10 items-center gap-2 px-4 py-2.5 bg-white/90 backdrop-blur-md rounded-xl shadow-[0_4px_16px_rgba(0,0,0,0.12)] border border-slate-100 text-sm font-semibold text-[#222a60] hover:bg-white transition-colors cursor-pointer">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M7 7l10 10-5 5V2l5 5-10 10"/></svg>
            Capteur Bluetooth
        </button>
        @endif

    </div>

</div>

<div id="image-lightbox" class="hidden fixed inset-0 z-[9999] bg-black/90 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <button id="lightbox-close" class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center text-white/70 hover:text-white bg-black/50 hover:bg-black/80 rounded-full z-10 transition-all cursor-pointer border-none">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <img id="lightbox-img" src="" class="max-w-full max-h-full object-contain rounded-xl shadow-2xl transform scale-95 transition-transform duration-300" alt="Signalement en grand">
</div>

<script>
    window.mapPoints          = @json($pointsJson ?? []);
    window.mapRivers          = @json($riversJson ?? []);
    window.mapCapteurs        = {!! $capteursJson !!};
    window.mapCoulees         = @json($couleesJson ?? []);
    window.createAnalyseUrl   = "{{ route('analyse.create') }}";
    window.nearestRiverUrl    = "{{ route('cours-d-eau.nearest') }}";
    window.userAuthenticated  = {{ auth()->check() ? 'true' : 'false' }};
    window.loginUrl           = "{{ route('login', ['source' => 'mobile']) }}";
    window.btSyncUrl          = "{{ route('capteurs.bluetooth.sync') }}";
    window.couleesStoreUrl    = "{{ auth()->check() ? route('coulees-de-boue.store') : '' }}";
    window.couleesDestroyBase = "{{ auth()->check() ? url('coulees-de-boue') : '' }}";
    window.currentUserId      = {{ auth()->id() ?? 'null' }};
    window.isAdmin            = {{ (auth()->check() && auth()->user()->role === 'admin') ? 'true' : 'false' }};
    window.analyseEditUrlBase = "{{ url('analyse') }}";
    window.csrfToken          = "{{ csrf_token() }}";
</script>

@endsection
