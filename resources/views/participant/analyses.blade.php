@extends('layouts.participant')

@section('title', 'Mes analyses')
@section('page-title', 'Analyses')
@section('page-subtitle',
    $participant['nb_groupes'] > 0
        ? 'Groupe ' . chr(64 + $participant['id_groupe']) . ' — ' . $participant['campagne_nom']
        : $participant['pseudo'] . ' — ' . $participant['campagne_nom']
)


@section('content')

<div class="flex flex-col lg:flex-row flex-1 overflow-hidden">

    {{-- Sidebar liste cours d'eau --}}
    <aside class="w-full lg:w-72 flex-shrink-0 border-r border-slate-100 bg-white flex flex-col overflow-hidden z-10">

        <div class="p-4 border-b border-slate-100 flex items-center justify-between gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
                <input id="search-cours-eau" type="text" placeholder="Rechercher…"
                    class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#222a60]/20 focus:border-[#222a60] transition-all">
            </div>
            <a href="{{ route('participant.map') }}"
                class="shrink-0 flex items-center gap-1.5 px-3 py-2 rounded-xl bg-[#222a60] text-white text-xs font-bold hover:bg-[#1a2050] transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvelle
            </a>
        </div>

        <div class="px-4 py-2 border-b border-slate-50 bg-slate-50/50">
            <p class="text-[11px] font-mono font-bold uppercase tracking-widest text-slate-500">
                {{ $coursDEaux->count() }} cours d'eau
            </p>
        </div>

        <div id="cours-eau-list" class="flex-1 overflow-y-auto divide-y divide-slate-50">
            @forelse ($coursDEaux as $cd)
                <button data-id="{{ $cd['id'] }}" data-nom="{{ strtolower($cd['nom']) }}"
                    onclick="selectCoursDEau({{ $cd['id'] }})"
                    class="cours-eau-item w-full text-left px-4 py-3.5 hover:bg-blue-50/50 transition-all group focus:outline-none border-l-4 border-transparent">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-slate-700 truncate group-hover:text-[#222a60]">{{ $cd['nom'] }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5 font-mono">{{ $cd['total_analyses'] }} analyse{{ $cd['total_analyses'] > 1 ? 's' : '' }}</p>
                        </div>
                        <x-quality-badge :qualite="$cd['qualite_globale']" class="shrink-0" />
                    </div>
                </button>
            @empty
                <div class="px-4 py-10 text-center text-sm text-slate-400 italic">
                    Aucune analyse pour l'instant.<br>
                    <a href="{{ route('participant.map') }}" class="text-[#222a60] font-semibold hover:underline">Ajouter une analyse →</a>
                </div>
            @endforelse
        </div>
    </aside>

    {{-- Panneau détail --}}
    <div id="analyses-detail" class="hidden lg:flex flex-col flex-1 overflow-y-auto bg-slate-50/50 relative">

        <div id="empty-state" class="absolute inset-0 flex flex-col items-center justify-center text-center px-8">
            <div class="w-16 h-16 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-300 mb-4">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <p class="text-base font-bold text-slate-600">Sélectionnez un cours d'eau</p>
            <p class="text-sm text-slate-400 mt-1 max-w-xs">Choisissez un cours d'eau dans la liste pour voir les analyses.</p>
        </div>

         <div id="detail-panel" class="hidden w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-10 space-y-6 sm:space-y-8">

            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h2 id="detail-nom" class="text-2xl font-black text-[#222a60] font-grotesk"></h2>
                        <span id="detail-qualite-badge" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider"></span>
                    </div>
                    <p id="detail-meta" class="text-xs text-slate-400 font-mono"></p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
                <h3 class="text-sm font-bold text-[#222a60] mb-4">Évolution de la qualité</h3>
                <div class="h-[200px]">
                    <canvas id="qualite-chart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-50">
                    <h3 class="text-sm font-bold text-[#222a60]">Détail des analyses</h3>
                </div>
                <div class="overflow-x-auto p-3">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">
                                <th class="pb-3 pl-3 pr-4">Date</th>
                                <th class="pb-3 pr-4">Type</th>
                                <th class="pb-3 pr-4">Qualité</th>
                            </tr>
                        </thead>
                        <tbody id="points-tbody" class="divide-y divide-slate-50"></tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
window.__coursDEaux = @json($coursDEaux);
</script>
<script src="{{ Vite::asset('resources/js/participant-analyses.js') }}"></script>
@endpush
