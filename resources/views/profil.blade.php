@extends('layouts.mobile')
@section('title', 'Profil')

@section('content')

@php
    $user     = Auth::user();
    $initials = strtoupper(substr($user->firstname, 0, 1) . substr($user->name, 0, 1));
@endphp

<div id="page-shell" class="flex flex-col min-h-[100dvh] bg-slate-50 font-grotesk text-slate-900 md:flex-row">

    {{-- ================================================================ --}}
    {{-- SIDEBAR — tablet / desktop uniquement                            --}}
    {{-- ================================================================ --}}
    <aside class="hidden md:flex md:flex-col md:w-64 lg:w-72 md:h-screen md:sticky md:top-0 md:shrink-0 bg-white border-r border-slate-100 shadow-[2px_0_10px_rgba(0,0,0,0.04)] z-10">

        {{-- Logo --}}
        <div class="px-5 py-5 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-[#222a60] flex items-center justify-center">
                    <svg width="14" height="14" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <span class="font-bold text-[#222a60] text-base tracking-tight">Lymnik</span>
            </div>
        </div>

        {{-- Avatar utilisateur --}}
        <div class="px-5 py-5 border-b border-slate-100">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#1a7fc4] to-[#1565c0] flex items-center justify-center font-mono text-base font-bold text-white shrink-0 shadow-sm">
                    {{ $initials }}
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-bold text-slate-800 truncate">{{ $user->firstname }} {{ $user->name }}</div>
                    <div class="text-[10px] text-slate-400 font-mono truncate">{{ $user->email }}</div>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-mono text-[10px] font-bold tracking-[0.08em] uppercase {{ $isAdmin ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                @if ($isAdmin)
                    <svg width="10" height="10" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Administrateur
                @else
                    Participant
                @endif
            </span>
        </div>

        {{-- Navigation --}}
        <nav class="flex flex-col gap-0.5 px-3 py-4 flex-1">
            <a href="{{ route('mobile') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:text-[#222a60] hover:bg-slate-50 transition-colors no-underline">
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
            <a href="{{ route('profil') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-[#222a60] bg-[#222a60]/6 no-underline">
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Mon profil
            </a>
        </nav>

        {{-- Déconnexion sidebar --}}
        <div class="border-t border-slate-100 p-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-red-500 hover:bg-red-50 transition-colors bg-transparent border-none cursor-pointer text-left">
                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Se déconnecter
                </button>
            </form>
        </div>

    </aside>

    {{-- ================================================================ --}}
    {{-- CONTENU PRINCIPAL                                                --}}
    {{-- ================================================================ --}}
    <div class="flex flex-col flex-1 md:min-h-screen overflow-hidden md:overflow-visible">

        {{-- Header mobile gradient --}}
        <div id="page-header"
            class="shrink-0 bg-gradient-to-br from-[#0d1533] via-[#0f1d42] to-[#1a2a6c] pt-[max(48px,env(safe-area-inset-top))] relative z-10 md:hidden">
            <div class="flex items-center justify-between px-4 pb-5">
                <a href="{{ route('mobile') }}"
                    class="w-[34px] h-[34px] rounded-full bg-white/10 flex items-center justify-center text-white no-underline cursor-pointer active:bg-white/20 transition-colors shrink-0">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <span class="font-mono text-[11px] text-white/40">Mon profil</span>
                <div class="w-[34px]"></div>
            </div>

            <div class="flex flex-col items-center px-4 pb-7 gap-2.5">
                <div class="w-[76px] h-[76px] rounded-full bg-gradient-to-br from-[#1a7fc4] to-[#1565c0] border-4 border-white/20 flex items-center justify-center font-mono text-2xl font-bold text-white shadow-[0_8px_24px_rgba(0,0,0,0.2)]">
                    {{ $initials }}
                </div>
                <div class="text-xl font-bold text-white font-grotesk">{{ $user->firstname }} {{ $user->name }}</div>
                <div class="font-mono text-[11px] text-white/50 -mt-1.5">{{ $user->email }}</div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-mono text-[10px] font-bold tracking-[0.08em] uppercase {{ $isAdmin ? 'bg-emerald-500/20 text-emerald-300' : 'bg-white/10 text-white/60' }}">
                    @if ($isAdmin)
                        <svg width="10" height="10" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Administrateur
                    @else
                        Participant
                    @endif
                </div>
            </div>
        </div>

        {{-- Header desktop --}}
        <div class="hidden md:flex items-center justify-between px-6 lg:px-8 py-5 bg-white border-b border-slate-100 shrink-0">
            <div>
                <h1 class="text-xl font-bold text-[#222a60]">Mon profil</h1>
                <p class="text-sm text-slate-400 font-mono mt-0.5">Membre depuis {{ $user->created_at->translatedFormat('d M Y') }}</p>
            </div>
            <a href="{{ route('profil.edit') }}"
                class="flex items-center gap-2 px-4 py-2.5 bg-[#222a60] text-white rounded-xl text-sm font-semibold no-underline hover:bg-[#1a2050] transition-colors shadow-sm">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier le profil
            </a>
        </div>

        {{-- Scroll --}}
        <div id="profile-scroll"
            class="flex-1 overflow-y-auto px-3.5 pt-4 pb-[calc(88px+env(safe-area-inset-bottom,0px))]
                   md:pb-8 md:px-6 lg:px-8 md:pt-6
                   touch-pan-y [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">

            {{-- Layout desktop : deux colonnes --}}
            <div class="md:grid md:grid-cols-2 md:gap-6 md:items-start lg:grid-cols-3">

                {{-- Colonne 1 : Informations + Activité --}}
                <div class="md:col-span-1 space-y-3 md:space-y-4">

                    {{-- Carte hero desktop (avatar centré) --}}
                    <div class="hidden md:flex flex-col items-center bg-gradient-to-br from-[#0d1533] via-[#0f1d42] to-[#1a2a6c] rounded-2xl p-6 gap-3 text-center">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#1a7fc4] to-[#1565c0] border-4 border-white/20 flex items-center justify-center font-mono text-xl font-bold text-white shadow-[0_8px_24px_rgba(0,0,0,0.2)]">
                            {{ $initials }}
                        </div>
                        <div>
                            <div class="text-base font-bold text-white">{{ $user->firstname }} {{ $user->name }}</div>
                            <div class="font-mono text-[10px] text-white/50 mt-0.5">{{ $user->email }}</div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-mono text-[10px] font-bold tracking-[0.08em] uppercase {{ $isAdmin ? 'bg-emerald-500/20 text-emerald-300' : 'bg-white/10 text-white/60' }}">
                            @if ($isAdmin)
                                <svg width="9" height="9" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Administrateur
                            @else
                                Participant
                            @endif
                        </span>
                    </div>

                    {{-- Informations du compte --}}
                    <div class="bg-white rounded-[18px] overflow-hidden shadow-[0_2px_12px_rgba(34,42,96,0.07)] border border-sv-blue/5 md:rounded-2xl">
                        <div class="font-mono text-[9px] font-bold tracking-[0.14em] uppercase text-slate-400 px-4 pt-3 pb-1.5">
                            Informations du compte
                        </div>

                        @foreach ([
                            ['icon' => 'user',     'label' => 'Prénom',         'value' => $user->firstname, 'mono' => false],
                            ['icon' => 'user',     'label' => 'Nom',            'value' => $user->name,      'mono' => false],
                            ['icon' => 'mail',     'label' => 'E-mail',         'value' => $user->email,     'mono' => true],
                            ['icon' => 'calendar', 'label' => 'Membre depuis',  'value' => $user->created_at->translatedFormat('d M Y'), 'mono' => false],
                        ] as $row)
                        <div class="flex items-center gap-3 px-4 py-3 border-t border-slate-50">
                            <div class="w-[34px] h-[34px] rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                                @if ($row['icon'] === 'user')
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                @elseif ($row['icon'] === 'mail')
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                @else
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @endif
                            </div>
                            <div>
                                <div class="font-mono text-[9px] font-bold tracking-[0.1em] uppercase text-slate-400 mb-0.5">{{ $row['label'] }}</div>
                                <div class="{{ $row['mono'] ? 'font-mono text-xs text-slate-600' : 'text-sm font-semibold text-slate-800 font-grotesk' }}">{{ $row['value'] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Activité --}}
                    <div class="bg-white rounded-[18px] overflow-hidden shadow-[0_2px_12px_rgba(34,42,96,0.07)] border border-sv-blue/5 md:rounded-2xl">
                        <div class="font-mono text-[9px] font-bold tracking-[0.14em] uppercase text-slate-400 px-4 pt-3 pb-1.5">Activité</div>
                        <div class="grid grid-cols-2">
                            <div class="p-3.5 border-r border-t border-slate-50">
                                <div class="text-[22px] font-extrabold text-[#222a60] font-grotesk leading-none mb-1">{{ $stats['analyses'] }}</div>
                                <div class="font-mono text-[9px] font-bold tracking-[0.1em] uppercase text-slate-400">Analyses</div>
                            </div>
                            <div class="p-3.5 border-t border-slate-50">
                                <div class="text-[22px] font-extrabold text-[#1565c0] font-grotesk leading-none mb-1">{{ $stats['points'] }}</div>
                                <div class="font-mono text-[9px] font-bold tracking-[0.1em] uppercase text-slate-400">Points créés</div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Colonne 2 : Interface + Actions --}}
                <div class="mt-3 md:mt-0 md:col-span-1 lg:col-span-2 space-y-3 md:space-y-4">

                    {{-- Interface --}}
                    <div class="bg-white rounded-[18px] overflow-hidden shadow-[0_2px_12px_rgba(34,42,96,0.07)] border border-sv-blue/5 md:rounded-2xl">
                        <div class="font-mono text-[9px] font-bold tracking-[0.14em] uppercase text-slate-400 px-4 pt-3 pb-1.5">Interface</div>

                        <div class="flex items-center gap-3 p-3.5 border-t border-slate-50 cursor-default select-none">
                            <div class="w-10 h-10 rounded-[12px] flex items-center justify-center shrink-0 bg-blue-50 text-[#1565c0]">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="7" y="2" width="10" height="20" rx="2"/>
                                    <path stroke-linecap="round" d="M11 18h2"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-semibold text-slate-800">Application mobile</div>
                                <div class="text-[11px] text-slate-400 mt-[1px]">Carte interactive &amp; saisie terrain</div>
                            </div>
                            <div class="w-2 h-2 rounded-full bg-[#16987c] shrink-0"></div>
                        </div>

                        <a href="{{ route('dashboard') }}"
                            class="flex items-center gap-3 p-3.5 border-t border-slate-50 no-underline transition-colors hover:bg-slate-50 active:bg-slate-50">
                            <div class="w-10 h-10 rounded-[12px] flex items-center justify-center shrink-0 bg-emerald-500/10 text-[#16987c]">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                                    <path stroke-linecap="round" d="M8 21h8M12 17v4"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-semibold text-slate-800">Plateforme web</div>
                                <div class="text-[11px] text-slate-400 mt-[1px]">
                                    @if ($isAdmin) Tableau de bord &amp; administration
                                    @else Mes données &amp; graphiques
                                    @endif
                                </div>
                            </div>
                            <svg class="text-slate-300" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>

                    {{-- Modifier le profil (desktop) --}}
                    <div class="hidden md:block bg-white rounded-2xl overflow-hidden shadow-[0_2px_12px_rgba(34,42,96,0.07)] border border-sv-blue/5">
                        <div class="font-mono text-[9px] font-bold tracking-[0.14em] uppercase text-slate-400 px-4 pt-3 pb-1.5">Paramètres du compte</div>
                        <a href="{{ route('profil.edit') }}"
                            class="flex items-center gap-3 p-3.5 border-t border-slate-50 no-underline transition-colors hover:bg-slate-50">
                            <div class="w-10 h-10 rounded-[12px] flex items-center justify-center shrink-0 bg-[#222a60]/8 text-[#222a60]">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-semibold text-slate-800">Modifier les informations</div>
                                <div class="text-[11px] text-slate-400 mt-[1px]">Nom, e-mail, mot de passe</div>
                            </div>
                            <svg class="text-slate-300" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>

                    {{-- Déconnexion (mobile uniquement — desktop a bouton dans sidebar) --}}
                    <div class="bg-white rounded-[18px] overflow-hidden shadow-[0_2px_12px_rgba(34,42,96,0.07)] border border-sv-blue/5 md:hidden">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 p-3.5 bg-transparent border-none text-left cursor-pointer transition-colors active:bg-slate-50 outline-none">
                                <div class="w-10 h-10 rounded-[12px] flex items-center justify-center shrink-0 bg-red-50 text-red-500">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-red-500">Se déconnecter</div>
                                    <div class="text-[11px] text-slate-400 mt-[1px]">Fermer la session en cours</div>
                                </div>
                            </button>
                        </form>
                    </div>

                </div>

            </div>{{-- fin grid --}}

            <p class="text-center font-mono text-[9px] text-slate-300 mt-6 pb-1">Lymnik · v1.0 · 2025</p>

        </div>{{-- fin scroll --}}

        {{-- Bottom nav mobile --}}
        <nav id="bottom-nav"
            class="fixed bottom-0 inset-x-0 z-30 bg-white/95 backdrop-blur-md border-t border-sv-blue/5 flex justify-around items-center pt-2.5 pb-[calc(10px+env(safe-area-inset-bottom,0px))] md:hidden">
            <a href="{{ route('mobile') }}"
                class="group flex flex-col items-center gap-[3px] px-5 py-1 rounded-xl transition-colors active:bg-slate-100 select-none no-underline">
                <svg class="text-slate-400" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <span class="text-[10px] font-semibold text-slate-400">Carte</span>
            </a>
            <a href="{{ route('analyses') }}"
                class="group flex flex-col items-center gap-[3px] px-5 py-1 rounded-xl transition-colors active:bg-slate-100 select-none no-underline">
                <svg class="text-slate-400" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span class="text-[10px] font-semibold text-slate-400">Mes analyses</span>
            </a>
            <a href="{{ route('profil') }}"
                class="group flex flex-col items-center gap-[3px] px-5 py-1 rounded-xl transition-colors active:bg-slate-100 select-none no-underline">
                <svg class="text-[#222a60]" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-[10px] font-semibold text-[#222a60]">Profil</span>
            </a>
        </nav>

    </div>{{-- fin contenu principal --}}

</div>{{-- fin page-shell --}}

@endsection
