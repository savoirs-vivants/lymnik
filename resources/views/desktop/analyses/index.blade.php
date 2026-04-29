@extends('layouts.desktop')

@section('title', 'Analyses')
@section('page-title', 'Analyses')
@section('page-subtitle', 'Qualité de l\'eau par cours d\'eau')

@section('content')

    @php
        $qualiteConfig = [
            'tres_bon' => [
                'label' => 'Très bon',
                'bg' => 'bg-emerald-100',
                'text' => 'text-emerald-700',
                'dot' => 'bg-emerald-500',
                'border' => 'border-emerald-200',
                'chart' => '#10b981',
            ],
            'bon' => [
                'label' => 'Bon',
                'bg' => 'bg-teal-100',
                'text' => 'text-teal-700',
                'dot' => 'bg-teal-500',
                'border' => 'border-teal-200',
                'chart' => '#14b8a6',
            ],
            'passable' => [
                'label' => 'Passable',
                'bg' => 'bg-yellow-100',
                'text' => 'text-yellow-700',
                'dot' => 'bg-yellow-400',
                'border' => 'border-yellow-200',
                'chart' => '#eab308',
            ],
            'mediocre' => [
                'label' => 'Médiocre',
                'bg' => 'bg-orange-100',
                'text' => 'text-orange-700',
                'dot' => 'bg-orange-400',
                'border' => 'border-orange-200',
                'chart' => '#f97316',
            ],
            'mauvais' => [
                'label' => 'Mauvais',
                'bg' => 'bg-red-100',
                'text' => 'text-red-700',
                'dot' => 'bg-red-500',
                'border' => 'border-red-200',
                'chart' => '#ef4444',
            ],
        ];
    @endphp

    <div class="flex h-[calc(100vh-80px)] overflow-hidden">

        <aside
            class="w-80 flex-shrink-0 border-r border-slate-100 bg-white flex flex-col h-full overflow-hidden shadow-[2px_0_10px_rgba(0,0,0,0.02)] z-10">

            <div class="p-5 border-b border-slate-100 space-y-3">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                    </svg>
                    <input id="search-cours-eau" type="text" placeholder="Rechercher un cours d'eau…"
                        class="w-full pl-10 pr-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#222a60]/20 focus:border-[#222a60] transition-all">
                </div>
                <select id="filter-qualite"
                    class="w-full px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#222a60]/20 focus:border-[#222a60] transition-all cursor-pointer">
                    <option value="">Toutes les qualités</option>
                    <option value="tres_bon">Très bon</option>
                    <option value="bon">Bon</option>
                    <option value="passable">Passable</option>
                    <option value="mediocre">Médiocre</option>
                    <option value="mauvais">Mauvais</option>
                </select>
            </div>

            <div class="px-5 py-2.5 border-b border-slate-50 bg-slate-50/50">
                <p id="cours-eau-count" class="text-[11px] font-mono font-bold uppercase tracking-widest text-slate-500">
                    {{ $coursDEaux->count() }} cours d'eau
                </p>
            </div>

            <div id="cours-eau-list" class="flex-1 overflow-y-auto divide-y divide-slate-50 scroll-smooth">
                @forelse ($coursDEaux as $cd)
                    @php $cfg = $qualiteConfig[$cd['qualite_globale']] ?? $qualiteConfig['tres_bon']; @endphp
                    <button data-id="{{ $cd['id'] }}" data-qualite="{{ $cd['qualite_globale'] }}"
                        data-nom="{{ strtolower($cd['nom']) }}" onclick="selectCoursDEau({{ $cd['id'] }})"
                        class="cours-eau-item w-full text-left px-5 py-4 hover:bg-blue-50/50 transition-all group focus:outline-none border-l-4 border-transparent">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p
                                    class="text-[15px] font-bold text-slate-700 truncate group-hover:text-[#222a60] transition-colors">
                                    {{ $cd['nom'] }}
                                </p>
                                <p class="text-[11px] text-slate-500 mt-1 font-mono">
                                    {{ $cd['total_analyses'] }} analyse{{ $cd['total_analyses'] > 1 ? 's' : '' }}
                                    · {{ $cd['total_points'] }} point{{ $cd['total_points'] > 1 ? 's' : '' }}
                                </p>
                            </div>
                            <span
                                class="shrink-0 inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $cfg['bg'] }} {{ $cfg['text'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $cfg['dot'] }}"></span>
                                {{ $cfg['label'] }}
                            </span>
                        </div>
                    </button>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-slate-400 italic">
                        Aucun cours d'eau avec des analyses.
                    </div>
                @endforelse
            </div>
        </aside>

        <div class="flex-1 overflow-y-auto bg-slate-50/50 relative">

            <div id="empty-state" class="absolute inset-0 flex flex-col items-center justify-center text-center px-8 z-10">
                <div
                    class="w-20 h-20 rounded-3xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-300 mb-5">
                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <p class="text-lg font-bold text-slate-600">Sélectionnez un cours d'eau</p>
                <p class="text-sm text-slate-400 mt-2 max-w-sm">Choisissez un cours d'eau dans la liste à gauche pour voir
                    l'historique complet de ses analyses.</p>
            </div>

            <div id="detail-panel" class="hidden max-w-6xl mx-auto p-8 lg:p-10 space-y-8">

                <div
                    class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                    <div>
                        <div class="flex items-center gap-4 mb-2">
                            <h2 id="detail-nom" class="text-3xl font-black text-[#222a60] font-grotesk"></h2>
                            <span id="detail-qualite-badge"
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider"></span>
                        </div>
                        <p id="detail-meta" class="text-sm text-slate-500 font-mono"></p>
                    </div>
                </div>

                <div id="detail-kpis" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4"></div>

                <div
                    class="bg-white rounded-[2rem] border border-slate-100 shadow-[0_4px_20px_rgba(34,42,96,0.03)] p-6 lg:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-[#222a60]">Évolution de la qualité</h3>
                        <span
                            class="text-[11px] font-mono font-bold uppercase tracking-widest text-slate-400">Chronologique</span>
                    </div>
                    <div class="relative w-full h-[250px]">
                        <canvas id="qualite-chart"></canvas>
                    </div>
                </div>

                <div
                    class="bg-white rounded-[2rem] border border-slate-100 shadow-[0_4px_20px_rgba(34,42,96,0.03)] overflow-hidden">
                    <div class="p-6 lg:p-8 border-b border-slate-50">
                        <h3 class="text-lg font-bold text-[#222a60]">Analyses détaillées par point de mesure</h3>
                    </div>
                    <div class="overflow-x-auto p-4">
                        <table class="w-full text-sm">
                            <thead>
                                <tr
                                    class="text-left text-[11px] font-mono font-bold uppercase tracking-widest text-slate-400 border-b-2 border-slate-100">
                                    <th class="pb-4 pl-4 pr-4">Point & Date</th>
                                    <th class="pb-4 pr-4">Type</th>
                                    <th class="pb-4 pr-4">Qualité</th>
                                    <th class="pb-4 pr-4">Nitrates</th>
                                    <th class="pb-4 pr-4">Nitrites</th>
                                    <th class="pb-4 pr-4">pH</th>
                                    <th class="pb-4 pr-4">Phosphate</th>
                                    <th class="pb-4 pr-4">Ammoniaque</th>
                                    <th class="pb-4 pr-4 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="points-tbody" class="divide-y divide-slate-100"></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="point-overlay" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-[#0f1d42]/60 backdrop-blur-sm transition-opacity" onclick="closeOverlay()"></div>
        <div
            class="absolute right-0 top-0 h-full w-full max-w-xl bg-white shadow-2xl flex flex-col overflow-hidden transform transition-transform">

            <div class="flex items-center justify-between px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                <div>
                    <h3 id="overlay-title" class="text-xl font-black text-[#222a60] font-grotesk"></h3>
                    <p id="overlay-subtitle" class="text-xs text-slate-500 font-mono mt-1"></p>
                </div>
                <button onclick="closeOverlay()"
                    class="w-10 h-10 rounded-full bg-white border border-slate-200 hover:bg-slate-100 flex items-center justify-center transition-colors shadow-sm text-slate-500">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div id="overlay-content" class="flex-1 overflow-y-auto p-8 space-y-6 bg-slate-50/30"></div>
        </div>
    </div>

@endsection

<script>
    window.__coursDEaux = @json($coursDEaux);
    window.__qualiteConfig = @json($qualiteConfig);
</script>
