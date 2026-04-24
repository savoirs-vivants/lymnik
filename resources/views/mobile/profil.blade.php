@extends('layouts.mobile')
@section('title', 'Profil')

@section('content')

    @php
        $user = Auth::user();
        $initials = strtoupper(substr($user->firstname, 0, 1) . substr($user->name, 0, 1));
    @endphp

    <div id="page-shell" class="flex flex-col h-[100dvh] overflow-hidden bg-slate-50 font-grotesk text-slate-900 relative">

        <div id="page-header"
            class="shrink-0 bg-gradient-to-br from-[#0d1533] via-[#0f1d42] to-[#1a2a6c] pt-[max(48px,env(safe-area-inset-top))] relative z-10">

            <div class="flex items-center justify-between px-4 pb-5">
                <a href="{{ route('index_mobile') }}"
                    class="w-[34px] h-[34px] rounded-full bg-white/10 flex items-center justify-center text-white no-underline cursor-pointer active:bg-white/20 transition-colors [touch-action:manipulation] tap-highlight-transparent shrink-0"
                    aria-label="Retour">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <span class="font-mono text-[11px] text-white/40">Mon profil</span>
                <div class="w-[34px]"></div>
            </div>

            <div class="flex flex-col items-center px-4 pb-7 gap-2.5">
                <div
                    class="w-[76px] h-[76px] rounded-full bg-gradient-to-br from-[#1a7fc4] to-[#1565c0] border-4 border-white/20 flex items-center justify-center font-mono text-2xl font-bold text-white shadow-[0_8px_24px_rgba(0,0,0,0.2)]">
                    {{ $initials }}
                </div>
                <div class="text-xl font-bold text-white font-grotesk">{{ $user->firstname }} {{ $user->name }}</div>
                <div class="font-mono text-[11px] text-white/50 -mt-1.5">{{ $user->email }}</div>

                <div
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-mono text-[10px] font-bold tracking-[0.08em] uppercase {{ $isAdmin ? 'bg-emerald-500/20 text-emerald-300' : 'bg-white/10 text-white/60' }}">
                    @if ($isAdmin)
                        <svg width="10" height="10" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        Administrateur
                    @else
                        Participant
                    @endif
                </div>
            </div>
        </div>

        <div id="profile-scroll"
            class="flex-1 overflow-y-auto px-3.5 pt-4 pb-[calc(88px+env(safe-area-inset-bottom,0px))] touch-pan-y [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">

            <div
                class="bg-white rounded-[18px] overflow-hidden shadow-[0_2px_12px_rgba(34,42,96,0.07)] border border-sv-blue/5 mb-3">
                <div class="font-mono text-[9px] font-bold tracking-[0.14em] uppercase text-slate-400 px-4 pt-3 pb-1.5">
                    Informations du compte</div>

                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-50 gap-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-[34px] h-[34px] rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <div class="font-mono text-[9px] font-bold tracking-[0.1em] uppercase text-slate-400 mb-0.5">
                                Prénom</div>
                            <div class="text-sm font-semibold text-slate-800 font-grotesk">{{ $user->firstname }}</div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-50 gap-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-[34px] h-[34px] rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <div class="font-mono text-[9px] font-bold tracking-[0.1em] uppercase text-slate-400 mb-0.5">Nom
                            </div>
                            <div class="text-sm font-semibold text-slate-800 font-grotesk">{{ $user->name }}</div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-50 gap-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-[34px] h-[34px] rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <div class="font-mono text-[9px] font-bold tracking-[0.1em] uppercase text-slate-400 mb-0.5">
                                E-mail</div>
                            <div class="font-mono text-xs text-slate-600">{{ $user->email }}</div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between px-4 py-3 gap-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-[34px] h-[34px] rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <div class="font-mono text-[9px] font-bold tracking-[0.1em] uppercase text-slate-400 mb-0.5">
                                Membre depuis</div>
                            <div class="text-sm font-semibold text-slate-800 font-grotesk">
                                {{ $user->created_at->translatedFormat('d M Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-[18px] overflow-hidden shadow-[0_2px_12px_rgba(34,42,96,0.07)] border border-sv-blue/5 mb-3">
                <div class="font-mono text-[9px] font-bold tracking-[0.14em] uppercase text-slate-400 px-4 pt-3 pb-1.5">
                    Activité</div>
                <div class="grid grid-cols-2">
                    <div class="p-3.5 border-r border-b border-slate-50">
                        <div class="text-[22px] font-extrabold text-[#222a60] font-grotesk leading-none mb-1">
                            {{ $stats['analyses'] }}</div>
                        <div class="font-mono text-[9px] font-bold tracking-[0.1em] uppercase text-slate-400">Analyses</div>
                    </div>
                    <div class="p-3.5 border-b border-slate-50">
                        <div class="text-[22px] font-extrabold text-[#1565c0] font-grotesk leading-none mb-1">
                            {{ $stats['points'] }}</div>
                        <div class="font-mono text-[9px] font-bold tracking-[0.1em] uppercase text-slate-400">Points créés
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-[18px] overflow-hidden shadow-[0_2px_12px_rgba(34,42,96,0.07)] border border-sv-blue/5 mb-3">
                <div class="font-mono text-[9px] font-bold tracking-[0.14em] uppercase text-slate-400 px-4 pt-3 pb-1.5">
                    Interface</div>

                <div class="flex items-center gap-3 p-3.5 border-b border-slate-50 cursor-default select-none">
                    <div
                        class="w-10 h-10 rounded-[12px] flex items-center justify-center shrink-0 bg-blue-50 text-[#1565c0]">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <rect x="7" y="2" width="10" height="20" rx="2" />
                            <path stroke-linecap="round" d="M11 18h2" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-semibold text-slate-800">Application mobile</div>
                        <div class="text-[11px] text-slate-400 mt-[1px]">Carte interactive &amp; saisie terrain</div>
                    </div>
                    <div class="w-2 h-2 rounded-full bg-[#16987c] shrink-0" title="Interface active"></div>
                </div>

                <a href="{{ url('index_web') }}"
                    class="flex items-center gap-3 p-3.5 no-underline transition-colors active:bg-slate-50 [touch-action:manipulation] tap-highlight-transparent">
                    <div
                        class="w-10 h-10 rounded-[12px] flex items-center justify-center shrink-0 bg-emerald-500/10 text-[#16987c]">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <rect x="2" y="3" width="20" height="14" rx="2" />
                            <path stroke-linecap="round" d="M8 21h8M12 17v4" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-semibold text-slate-800">Plateforme web</div>
                        <div class="text-[11px] text-slate-400 mt-[1px]">
                            @if ($isAdmin)
                                Tableau de bord &amp; administration
                            @else
                                Mes données &amp; graphiques
                            @endif
                        </div>
                    </div>
                    <svg class="text-slate-300" width="16" height="16" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <div
                class="bg-white rounded-[18px] overflow-hidden shadow-[0_2px_12px_rgba(34,42,96,0.07)] border border-sv-blue/5 mb-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 p-3.5 bg-transparent border-none text-left cursor-pointer transition-colors active:bg-slate-50 [touch-action:manipulation] tap-highlight-transparent outline-none">
                        <div
                            class="w-10 h-10 rounded-[12px] flex items-center justify-center shrink-0 bg-red-50 text-red-500">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-semibold text-red-500">Se déconnecter</div>
                            <div class="text-[11px] text-slate-400 mt-[1px]">Fermer la session en cours</div>
                        </div>
                    </button>
                </form>
            </div>

            <p class="text-center font-mono text-[9px] text-slate-300 mt-2 pb-1">
                Lymnik · v1.0 · 2025
            </p>

        </div>

        <nav id="bottom-nav"
            class="absolute bottom-0 inset-x-0 z-30 bg-white/95 backdrop-blur-md border-t border-sv-blue/5 flex justify-around items-center pt-2.5 pb-[calc(10px+env(safe-area-inset-bottom,0px))]">
            <a href="{{ route('index_mobile') }}"
                class="group flex flex-col items-center gap-[3px] cursor-pointer px-5 py-1 rounded-xl transition-colors active:bg-slate-100 select-none no-underline [touch-action:manipulation] tap-highlight-transparent">
                <svg class="text-slate-400 transition-colors group-[.active]:text-[#222a60]" width="22"
                    height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
                <span
                    class="text-[10px] font-semibold text-slate-400 transition-colors group-[.active]:text-[#222a60]">Carte</span>
            </a>
            <a href="{{ route('mobile.analyses') }}"
                class="group flex flex-col items-center gap-[3px] cursor-pointer px-5 py-1 rounded-xl transition-colors active:bg-slate-100 select-none no-underline [touch-action:manipulation] tap-highlight-transparent">
                <svg class="text-slate-400 transition-colors group-[.active]:text-[#222a60]" width="22"
                    height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-[10px] font-semibold text-slate-400 transition-colors group-[.active]:text-[#222a60]">Mes
                    analyses</span>
            </a>
            <a href="{{ route('mobile.profil') }}"
                class="active group flex flex-col items-center gap-[3px] cursor-pointer px-5 py-1 rounded-xl transition-colors active:bg-slate-100 select-none no-underline [touch-action:manipulation] tap-highlight-transparent">
                <svg class="text-[#222a60] transition-colors" width="22" height="22" fill="none"
                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-[10px] font-semibold text-[#222a60] transition-colors">Profil</span>
            </a>
        </nav>

    </div>

@endsection
