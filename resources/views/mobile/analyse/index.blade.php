@extends('layouts.mobile')
@section('title', 'Toutes mes analyses')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div id="page-shell" class="flex flex-col min-h-[100dvh] bg-slate-50 font-grotesk text-slate-900 md:flex-row">

    {{-- ================================================================ --}}
    {{-- SIDEBAR — tablet / desktop uniquement                            --}}
    {{-- ================================================================ --}}
    <aside class="hidden md:flex md:flex-col md:w-64 lg:w-72 md:h-screen md:sticky md:top-0 md:shrink-0 bg-white border-r border-slate-100 shadow-[2px_0_10px_rgba(0,0,0,0.04)] z-10">

        {{-- Logo --}}
        <div class="px-5 py-5 border-b border-slate-100">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-7 h-7 rounded-lg bg-[#222a60] flex items-center justify-center">
                    <svg width="14" height="14" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <span class="font-bold text-[#222a60] text-base tracking-tight">Lymnik</span>
            </div>
        </div>

        {{-- Utilisateur --}}
        <div class="px-4 py-3 border-b border-slate-100">
            <a href="{{ route('profil') }}" class="flex items-center gap-2.5 no-underline group">
                <div class="w-9 h-9 rounded-full bg-[#222a60] flex items-center justify-center text-[12px] font-bold text-white shrink-0">
                    {{ strtoupper(substr(Auth::user()->firstname, 0, 1) . substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-semibold text-slate-800 truncate">{{ Auth::user()->firstname }} {{ Auth::user()->name }}</div>
                    <div class="text-[10px] text-slate-400 truncate">{{ $count }} {{ Str::plural('analyse', $count) }} · {{ $month }}</div>
                </div>
            </a>
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
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-[#222a60] bg-[#222a60]/6 no-underline">
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
    </aside>

    {{-- ================================================================ --}}
    {{-- CONTENU PRINCIPAL                                                --}}
    {{-- ================================================================ --}}
    <div class="flex flex-col flex-1 md:min-h-screen">

        {{-- Header mobile (gradient) --}}
        <div id="page-header"
            class="shrink-0 bg-gradient-to-br from-[#0d1533] via-[#0f1d42] to-[#1a2a6c] pt-[max(48px,env(safe-area-inset-top))] relative z-10 md:hidden">
            <div class="flex items-center justify-between px-4 pb-2.5">
                <div class="text-center flex-1">
                    <div class="text-lg font-bold text-white font-grotesk">Mes analyses</div>
                    <div class="text-[11px] text-white/50 font-mono mt-[1px]">{{ $count }}
                        {{ Str::plural('analyse', $count) }} · {{ $month }}</div>
                </div>
            </div>
        </div>


        {{-- Scroll content --}}
        <div id="analyses-scroll"
            class="flex-1 overflow-y-auto p-3.5 pb-[calc(80px+env(safe-area-inset-bottom,0px))]
                   md:pb-6 md:px-6 lg:px-8 md:pt-6
                   touch-pan-y [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">

            @if ($analyses->isEmpty())
                <div class="flex flex-col items-center justify-center min-h-[50vh] gap-3 text-slate-400 py-10 px-6 text-center">
                    <div class="w-16 h-16 rounded-[20px] bg-white flex items-center justify-center shadow-[0_4px_16px_rgba(34,42,96,0.08)] mb-1">
                        <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="text-base font-bold text-slate-700">Aucune analyse</div>
                    <div class="text-[13px] text-slate-400 leading-relaxed max-w-[260px]">Retournez sur la carte et appuyez sur un cours d'eau pour déposer votre première mesure.</div>
                    <a href="{{ route('mobile') }}"
                        class="mt-2 px-6 py-3 bg-[#222a60] text-white rounded-xl text-[13px] font-bold no-underline active:scale-95 transition-transform">
                        Voir la carte
                    </a>
                </div>
            @else
                @php
                    $seuils = [
                        'nitrates'      => ['max' => 500, 'warn' => 50,  'danger' => 100],
                        'nitrites'      => ['max' => 10,  'warn' => 0.5, 'danger' => 2],
                        'durete_totale' => ['max' => 375, 'warn' => 250, 'danger' => 300],
                        'durete_carb'   => ['max' => 357, 'warn' => 250, 'danger' => 300],
                        'ph'            => ['max' => 14,  'warn' => 8.5, 'danger' => 9],
                        'chlore'        => ['max' => 3,   'warn' => 0.8, 'danger' => 1.5],
                        'phosphate'     => ['max' => 2,   'warn' => 0.5, 'danger' => 1],
                        'nitrate'       => ['max' => 500, 'warn' => 50,  'danger' => 100],
                        'ammoniaque'    => ['max' => 5,   'warn' => 1,   'danger' => 3],
                        'ammonium'      => ['max' => 5,   'warn' => 1,   'danger' => 3],
                    ];
                    $dictLabels = [
                        'bandelette' => [
                            'nitrates'      => ['label' => 'Nitrates',      'unit' => 'mg/L'],
                            'nitrites'      => ['label' => 'Nitrites',      'unit' => 'mg/L'],
                            'durete_totale' => ['label' => 'Dureté totale', 'unit' => 'mg/L CaCO₃'],
                            'durete_carb'   => ['label' => 'Dureté carb.',  'unit' => 'mg/L CaCO₃'],
                            'ph'            => ['label' => 'pH',            'unit' => ''],
                            'chlore'        => ['label' => 'Chlore',        'unit' => 'mg/L Cl₂'],
                        ],
                        'photometre' => [
                            'phosphate'  => ['label' => 'Phosphate',  'unit' => 'mg/L'],
                            'nitrate'    => ['label' => 'Nitrate',    'unit' => 'mg/L'],
                            'ammoniaque' => ['label' => 'Ammoniaque', 'unit' => 'mg/L'],
                        ],
                    ];
                @endphp

                {{-- Grille responsive : 1 col mobile, 2 col md, 3 col xl --}}
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 md:gap-4">

                @foreach ($analyses as $a)
                    @php
                        $mesures  = $a['mesures'] ?? [];
                        $hasBand  = in_array($a['type'], ['bandelette', 'les_deux']);
                        $hasPhoto = in_array($a['type'], ['photometre', 'les_deux']);
                        $types    = match ($a['type']) {
                            'bandelette' => ['Bandelette JBL'],
                            'photometre' => ['Photomètre'],
                            'les_deux'   => ['Bandelette JBL', 'Photomètre'],
                            default      => [$a['type']],
                        };
                        $qualiteCfg = match ($a['qualite']) {
                            'tres_bon'  => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'label' => 'Très bon'],
                            'bon'       => ['bg' => 'bg-teal-50',    'text' => 'text-teal-700',    'dot' => 'bg-teal-500',    'label' => 'Bon'],
                            'passable'  => ['bg' => 'bg-yellow-50',  'text' => 'text-yellow-700',  'dot' => 'bg-yellow-400',  'label' => 'Passable'],
                            'mediocre'  => ['bg' => 'bg-orange-50',  'text' => 'text-orange-700',  'dot' => 'bg-orange-400',  'label' => 'Médiocre'],
                            'mauvais'   => ['bg' => 'bg-red-50',     'text' => 'text-red-700',     'dot' => 'bg-red-500',     'label' => 'Mauvais'],
                            default     => ['bg' => 'bg-slate-50',   'text' => 'text-slate-500',   'dot' => 'bg-slate-400',   'label' => $a['qualite']],
                        };
                    @endphp

                    <div class="analyse-card bg-white rounded-[18px] shadow-[0_2px_12px_rgba(34,42,96,0.07)] overflow-hidden border border-sv-blue/5 group md:rounded-2xl md:flex md:flex-col"
                        data-id="{{ $a['id'] }}" data-lat="{{ $a['latitude'] }}" data-lng="{{ $a['longitude'] }}">

                        {{-- En-tête carte --}}
                        <div class="flex items-start gap-3 p-3.5 pb-3 cursor-pointer select-none [touch-action:manipulation] md:pb-3.5"
                            onclick="toggleCard(this)">

                            {{-- Icône qualité --}}
                            <div class="w-11 h-11 rounded-[13px] flex items-center justify-center shrink-0 {{ $qualiteCfg['bg'] }} {{ $qualiteCfg['text'] }}">
                                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C8 6 5 9.5 5 13a7 7 0 0014 0c0-3.5-3-7-7-11z" />
                                </svg>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="text-[15px] font-bold text-[#222a60] truncate mb-0.5">
                                    {{ $a['cours_d_eau'] }}
                                    @if ($a['localite'])
                                        <span class="font-normal text-slate-400"> — {{ $a['localite'] }}</span>
                                    @endif
                                </div>
                                <div class="font-mono text-[10px] text-slate-400 mb-1.5">
                                    {{ $a['created_at'] }} · {{ $a['time'] }}
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($types as $t)
                                        <span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-[10px] font-bold font-mono {{ $a['type'] === 'photometre' ? 'bg-indigo-50 text-indigo-700' : 'bg-blue-50 text-blue-700' }}">
                                            {{ $t }}
                                        </span>
                                    @endforeach
                                    <span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-[10px] font-bold font-mono {{ $qualiteCfg['bg'] }} {{ $qualiteCfg['text'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $qualiteCfg['dot'] }}"></span>
                                        {{ $qualiteCfg['label'] }}
                                    </span>
                                </div>
                            </div>

                            <svg class="shrink-0 text-slate-300 transition-transform duration-300 ease-out mt-0.5 group-[.open]:rotate-180"
                                width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>

                        {{-- Détail déployable --}}
                        <div class="max-h-0 overflow-hidden transition-all duration-[420ms] ease-[cubic-bezier(0.4,0,0.2,1)] group-[.open]:max-h-[1200px] md:flex-1">
                            <div class="border-t border-slate-100 p-3.5 pb-4">

                                @if ($a['latitude'])
                                    <div class="w-full h-[130px] md:h-[150px] rounded-xl overflow-hidden bg-slate-200 mb-3 relative [&_.leaflet-container]:outline-none [&_.leaflet-control-attribution]:hidden [&_.leaflet-control-zoom]:hidden">
                                        <div id="map-{{ $a['id'] }}" class="w-full h-full"></div>
                                    </div>
                                @endif

                                @if ($hasBand && !empty($mesures['bandelette']))
                                    <div class="mb-3.5">
                                        <div class="flex items-center gap-2 font-mono text-[9px] font-bold tracking-[0.12em] uppercase mb-2.5 pb-2 border-b border-slate-100 text-blue-700">
                                            <div class="w-[22px] h-[22px] rounded-md flex items-center justify-center bg-blue-50 text-blue-700">
                                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="9" y="2" width="6" height="20" rx="2" /></svg>
                                            </div>
                                            Bandelette JBL
                                        </div>
                                        @foreach ($dictLabels['bandelette'] as $key => $info)
                                            @php $val = $mesures['bandelette'][$key] ?? null; @endphp
                                            @if ($val !== null)
                                                @php
                                                    $s        = $seuils[$key] ?? null;
                                                    $pct      = $s ? min(100, max(0, ($val / $s['max']) * 100)) : 50;
                                                    $thPct    = $s ? min(100, ($s['warn'] / $s['max']) * 100) : null;
                                                    $barColor = 'bg-emerald-500';
                                                    if ($s) {
                                                        if ($val >= $s['danger'])     $barColor = 'bg-red-500';
                                                        elseif ($val >= $s['warn'])   $barColor = 'bg-amber-500';
                                                    }
                                                @endphp
                                                <div class="flex items-center gap-2 mb-2.5 last:mb-0">
                                                    <div class="w-[90px] shrink-0">
                                                        <div class="text-xs text-slate-700">{{ $info['label'] }}</div>
                                                        @if ($info['unit'])
                                                            <div class="text-[9px] text-slate-400 font-mono">{{ $info['unit'] }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="relative flex-1">
                                                        <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                                            <div class="h-full rounded-full transition-all duration-500 ease-out min-w-[6px] {{ $barColor }}" style="width:{{ $pct }}%"></div>
                                                        </div>
                                                        @if ($thPct)
                                                            <div class="absolute top-0 bottom-0 border-l-[1.5px] border-white/80" style="left:{{ $thPct }}%;"></div>
                                                        @endif
                                                    </div>
                                                    <div class="w-[42px] text-right text-[13px] font-bold text-[#222a60] shrink-0 font-grotesk">{{ $val }}</div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                @if ($hasPhoto && !empty($mesures['photometre']))
                                    <div class="mb-3.5">
                                        <div class="flex items-center gap-2 font-mono text-[9px] font-bold tracking-[0.12em] uppercase mb-2.5 pb-2 border-b border-slate-100 text-indigo-700">
                                            <div class="w-[22px] h-[22px] rounded-md flex items-center justify-center bg-indigo-50 text-indigo-700">
                                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4" /><path stroke-linecap="round" d="M12 2v2M12 20v2M2 12h2M20 12h2" /></svg>
                                            </div>
                                            Photomètre
                                        </div>
                                        @foreach ($dictLabels['photometre'] as $key => $info)
                                            @php $val = $mesures['photometre'][$key] ?? null; @endphp
                                            @if ($val !== null)
                                                @php
                                                    $s        = $seuils[$key] ?? null;
                                                    $pct      = $s ? min(100, max(0, ($val / $s['max']) * 100)) : 50;
                                                    $thPct    = $s ? min(100, ($s['warn'] / $s['max']) * 100) : null;
                                                    $barColor = 'bg-emerald-500';
                                                    if ($s) {
                                                        if ($val >= $s['danger'])   $barColor = 'bg-red-500';
                                                        elseif ($val >= $s['warn']) $barColor = 'bg-amber-500';
                                                    }
                                                @endphp
                                                <div class="flex items-center gap-2 mb-2.5 last:mb-0">
                                                    <div class="w-[90px] shrink-0">
                                                        <div class="text-xs text-slate-700">{{ $info['label'] }}</div>
                                                        @if ($info['unit'])
                                                            <div class="text-[9px] text-slate-400 font-mono">{{ $info['unit'] }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="relative flex-1">
                                                        <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                                            <div class="h-full rounded-full transition-all duration-500 ease-out min-w-[6px] {{ $barColor }}" style="width:{{ $pct }}%"></div>
                                                        </div>
                                                        @if ($thPct)
                                                            <div class="absolute top-0 bottom-0 border-l-[1.5px] border-white/80" style="left:{{ $thPct }}%;"></div>
                                                        @endif
                                                    </div>
                                                    <div class="w-[42px] text-right text-[13px] font-bold text-[#222a60] shrink-0 font-grotesk">{{ $val }}</div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                @if (!empty($mesures['note']))
                                    <div class="flex items-center gap-2 p-2.5 bg-slate-50 rounded-lg border border-slate-100 mt-3 text-xs text-slate-600 leading-relaxed">
                                        <svg class="text-slate-400 shrink-0" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" /><path stroke-linecap="round" d="M3 9h18M9 21V9" /></svg>
                                        <div>
                                            @if ($a['image'])
                                                <span class="font-medium text-slate-700">1 photo · </span>
                                            @endif
                                            {{ $mesures['note'] }}
                                        </div>
                                    </div>
                                @elseif($a['image'])
                                    <div class="flex items-center gap-2 p-2.5 bg-slate-50 rounded-lg border border-slate-100 mt-3 text-xs text-slate-600 leading-relaxed">
                                        <svg class="text-slate-400 shrink-0" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" /><path stroke-linecap="round" d="M3 15l5-5 4 4 3-3 4 4" /></svg>
                                        <span class="font-medium text-slate-700">1 photo jointe</span>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                @endforeach

                </div>{{-- fin grille --}}
            @endif
        </div>

        {{-- Bottom nav mobile --}}
        <nav id="bottom-nav"
            class="fixed bottom-0 inset-x-0 z-30 bg-white/95 backdrop-blur-md border-t border-sv-blue/5 flex justify-around items-center pt-2.5 pb-[calc(10px+env(safe-area-inset-bottom,0px))] md:hidden">
            <a href="{{ route('mobile') }}"
                class="group flex flex-col items-center gap-[3px] cursor-pointer px-5 py-1 rounded-xl transition-colors active:bg-slate-100 select-none no-underline">
                <svg class="text-slate-400" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
                <span class="text-[10px] font-semibold text-slate-400">Carte</span>
            </a>
            <a href="/"
                class="group flex flex-col items-center gap-[3px] cursor-pointer px-5 py-1 rounded-xl transition-colors active:bg-slate-100 select-none no-underline">
                <svg class="text-slate-400" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-[10px] font-semibold text-slate-400">Accueil</span>
            </a>
            <a href="{{ route('analyses') }}"
                class="group flex flex-col items-center gap-[3px] cursor-pointer px-5 py-1 rounded-xl transition-colors active:bg-slate-100 select-none no-underline">
                <svg class="text-[#222a60]" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-[10px] font-semibold text-[#222a60]">Mes analyses</span>
            </a>
        </nav>

    </div>{{-- fin contenu principal --}}

</div>{{-- fin page-shell --}}

<script src="{{ asset('js/mes-analyses.js') }}"></script>

@endsection
