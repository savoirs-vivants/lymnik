@extends('layouts.desktop')

@section('title', 'Résultats des campagnes')
@section('page-title', 'Résultats des campagnes')
@section('page-subtitle', 'Visualisez les analyses par groupe de terrain')

@section('content')

    @php
        use App\Support\QualiteConfig;
        $qualiteConfig = QualiteConfig::all();
    @endphp

    <div
        class="flex flex-col lg:flex-row flex-1 overflow-hidden h-[calc(100vh-10rem)] border border-slate-100 rounded-2xl shadow-sm">

        <aside id="sidebar-list"
            class="w-full lg:w-80 flex-shrink-0 border-r border-slate-100 bg-white flex flex-col z-10 transition-all">

            <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-sm font-black text-[#222a60]">Campagnes ({{ count($campagnes) }})</h2>

                {{-- Onglets de filtre visibles uniquement par l'admin --}}
                @if($isAdmin)
                <div class="mt-3 flex gap-1 bg-slate-200/50 p-1 rounded-lg">
                    <button id="tab-all" onclick="filterCampagnes('all')" class="flex-1 text-xs font-bold px-3 py-1.5 rounded-md bg-white shadow-sm text-[#222a60] transition-all">Toutes</button>
                    <button id="tab-mine" onclick="filterCampagnes('mine')" class="flex-1 text-xs font-bold px-3 py-1.5 rounded-md text-slate-500 hover:bg-white/50 transition-all">Mes campagnes</button>
                </div>
                @endif
            </div>

            <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                @forelse ($campagnes as $campagne)
                    {{-- Ajout du data-is-mine pour le filtrage JS --}}
                    <div class="campagne-accordion block" data-is-mine="{{ $campagne['is_mine'] ? 'true' : 'false' }}">
                        <button
                            class="w-full text-left px-5 py-4 hover:bg-slate-50 transition-colors flex justify-between items-center toggle-accordion focus:outline-none">
                            <div>
                                <p class="font-bold text-slate-800">{{ $campagne['nom'] }}</p>
                                <p class="text-[11px] text-slate-400 font-mono mt-0.5">Code: {{ $campagne['code'] }}</p>
                            </div>
                            <svg class="w-5 h-5 text-slate-400 transform transition-transform duration-200 chevron"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div class="hidden bg-slate-50/30 border-t border-slate-100 groupes-container">
                            @forelse ($campagne['groupes'] as $groupe)
                                <button
                                    onclick="selectGroupe({{ $campagne['id'] }}, {{ $groupe['id_groupe'] }}, '{{ addslashes($campagne['nom']) }}')"
                                    data-campagne-id="{{ $campagne['id'] }}" data-groupe-id="{{ $groupe['id_groupe'] }}"
                                    class="groupe-item w-full text-left px-5 py-3 hover:bg-blue-50/50 transition-all group focus:outline-none border-l-4 border-transparent flex items-center justify-between">
                                    <div>
                                        <p
                                            class="text-[13px] font-bold text-slate-700 group-hover:text-[#222a60] transition-colors">
                                            {{ $groupe['label'] }}</p>
                                        <p class="text-[10px] text-slate-500 mt-0.5 font-mono">
                                            {{ $groupe['total_analyses'] }} analyses</p>
                                    </div>
                                    <x-quality-badge :qualite="$groupe['qualite_globale']" class="shrink-0" />
                                </button>
                            @empty
                                <div class="px-5 py-4 text-xs text-slate-400 italic font-mono text-center">
                                    Aucune donnée pour le moment.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-sm text-slate-400 italic">Aucune campagne trouvée.</div>
                @endforelse
            </div>
        </aside>

        <div id="analyses-detail" class="max-lg:hidden flex flex-col flex-1 overflow-y-auto bg-slate-50/50 relative">

            <div id="empty-state" class="absolute inset-0 flex flex-col items-center justify-center text-center px-8 z-10">
                <div
                    class="w-20 h-20 rounded-3xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-300 mb-5">
                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <p class="text-lg font-bold text-slate-600">Sélectionnez un groupe</p>
                <p class="text-sm text-slate-400 mt-2 max-w-sm">Choisissez un groupe dans la liste à gauche pour voir les
                    analyses qu'ils ont effectuées sur le terrain.</p>
            </div>

            <div id="detail-panel" class="hidden w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-10 space-y-6">

                <button onclick="backToList()"
                    class="lg:hidden flex items-center gap-1.5 text-sm font-bold text-slate-500 hover:text-[#222a60] transition-colors mb-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Retour aux groupes
                </button>

                <div class="flex items-center justify-between bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                    <div class="w-full">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 mb-2">
                            <h2 id="detail-nom"
                                class="text-xl sm:text-2xl font-black text-[#222a60] font-grotesk break-words"></h2>
                            <span id="detail-qualite-badge" class="self-start"></span>
                        </div>
                        <p id="detail-meta" class="text-sm text-slate-500 font-mono"></p>
                    </div>
                </div>

                <div id="detail-kpis" class="grid grid-cols-2 md:grid-cols-5 gap-3 sm:gap-4"></div>

                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden mt-6">
                    <div class="p-4 sm:p-6 border-b border-slate-50">
                        <h3 class="text-base sm:text-lg font-bold text-[#222a60]">Analyses par point de mesure</h3>
                    </div>
                    <div class="overflow-x-auto p-2 sm:p-4">
                        <table class="w-full text-sm min-w-[500px]">
                            <thead>
                                <tr
                                    class="text-left text-[11px] font-mono font-bold uppercase tracking-widest text-slate-400 border-b-2 border-slate-100">
                                    <th class="pb-4 pl-4 pr-4">Point</th>
                                    <th class="pb-4 pr-4">Type d'analyse</th>
                                    <th class="pb-4 pr-4">Qualité</th>
                                    <th class="pb-4 pr-4 text-center">Historique</th>
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
        <div class="absolute inset-0 bg-[#0f1d42]/60 backdrop-blur-sm" onclick="closeOverlay()"></div>
        <div class="absolute right-0 top-0 h-full w-full max-w-xl bg-white shadow-2xl flex flex-col overflow-hidden">
            <div
                class="flex items-center justify-between px-6 sm:px-8 py-5 sm:py-6 border-b border-slate-100 bg-slate-50/50">
                <div class="min-w-0 pr-4">
                    <h3 id="overlay-title" class="text-lg sm:text-xl font-black text-[#222a60] font-grotesk truncate"></h3>
                    <p id="overlay-subtitle" class="text-[11px] sm:text-xs text-slate-500 font-mono mt-1"></p>
                </div>
                <button onclick="closeOverlay()"
                    class="shrink-0 w-10 h-10 rounded-full bg-white border border-slate-200 hover:bg-slate-100 flex items-center justify-center text-slate-500 transition-colors">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="overlay-map" class="w-full h-40 sm:h-48 shrink-0 bg-slate-200 relative"></div>
            <div id="overlay-content" class="flex-1 overflow-y-auto p-4 sm:p-8 space-y-4 sm:space-y-6 bg-slate-50/30">
            </div>
        </div>
    </div>

@endsection

<script>
    window.__campagnes = @json($campagnes);
    window.__qualiteConfig = @json($qualiteConfig);
</script>
