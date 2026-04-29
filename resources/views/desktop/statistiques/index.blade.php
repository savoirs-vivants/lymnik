@extends('layouts.desktop')

@section('title', 'Statistiques')
@section('page-title', 'Statistiques')
@section('page-subtitle', 'Analyse comparative de la qualité de l\'eau')

@push('scripts')
    @vite('resources/js/statistiques.js')
@endpush

@section('content')

    <div class="flex h-full overflow-hidden">

        <aside
            class="w-80 flex-shrink-0 border-r border-slate-100 bg-white flex flex-col h-full overflow-hidden shadow-[2px_0_10px_rgba(0,0,0,0.02)] z-10">

            <div class="px-5 py-4 border-b border-slate-100">
                <p class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">Filtres</p>
            </div>

            <div class="flex-1 overflow-y-auto">

                <div class="px-5 py-4 border-b border-slate-100">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">Mesures</p>
                        <div class="flex gap-2">
                            <button id="mesures-all"
                                class="text-[10px] text-[#222a60] font-semibold hover:underline">Tout</button>
                            <span class="text-slate-300 text-[10px]">·</span>
                            <button id="mesures-none"
                                class="text-[10px] text-slate-400 font-semibold hover:underline">Aucun</button>
                        </div>
                    </div>
                    <div class="space-y-1" id="mesures-checkboxes">
                        @php
                            $mesuresList = [
                                'nitrates' => ['label' => 'Nitrates', 'unit' => 'mg/L', 'color' => 'bg-blue-500'],
                                'nitrites' => ['label' => 'Nitrites', 'unit' => 'mg/L', 'color' => 'bg-violet-500'],
                                'ph' => ['label' => 'pH', 'unit' => '', 'color' => 'bg-teal-500'],
                                'chlore' => ['label' => 'Chlore', 'unit' => 'mg/L', 'color' => 'bg-cyan-500'],
                                'durete' => ['label' => 'Dureté totale', 'unit' => 'mg/L', 'color' => 'bg-amber-500'],
                                'phosphate' => ['label' => 'Phosphate', 'unit' => 'mg/L', 'color' => 'bg-orange-500'],
                                'ammoniaque' => ['label' => 'Ammoniaque', 'unit' => 'mg/L', 'color' => 'bg-red-500'],
                                'nitrate_photo' => [
                                    'label' => 'Nitrate (photo)',
                                    'unit' => 'mg/L',
                                    'color' => 'bg-indigo-500',
                                ],
                            ];
                        @endphp
                        @foreach ($mesuresList as $key => $m)
                            <label
                                class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors group">
                                <input type="checkbox" value="{{ $key }}"
                                    class="filter-mesure w-3.5 h-3.5 rounded accent-[#222a60]"
                                    checked>
                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                    <span class="w-2 h-2 rounded-full {{ $m['color'] }} shrink-0"></span>
                                    <span
                                        class="text-sm font-medium text-slate-700 group-hover:text-slate-900 truncate">{{ $m['label'] }}</span>
                                </div>
                                @if ($m['unit'])
                                    <span class="text-[10px] font-mono text-slate-400 shrink-0">{{ $m['unit'] }}</span>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="px-5 py-4 border-b border-slate-100">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">Cours d'eau</p>
                        <div class="flex gap-2">
                            <button id="rivers-all"
                                class="text-[10px] text-[#222a60] font-semibold hover:underline">Tout</button>
                            <span class="text-slate-300 text-[10px]">·</span>
                            <button id="rivers-none"
                                class="text-[10px] text-slate-400 font-semibold hover:underline">Aucun</button>
                        </div>
                    </div>
                    <div class="space-y-1 max-h-52 overflow-y-auto" id="rivers-checkboxes">
                        @foreach ($coursDEaux as $cd)
                            <label
                                class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors group">
                                <input type="checkbox" value="{{ $cd->id }}"
                                    class="filter-river w-3.5 h-3.5 rounded accent-[#222a60]" checked>
                                <span
                                    class="text-sm text-slate-600 group-hover:text-slate-900 truncate">{{ $cd->nom }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="px-5 py-4">
                    <p class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-3">Période</p>
                    <div class="grid grid-cols-4 gap-1.5 mb-4">
                        @foreach (['7' => '7J', '30' => '30J', '365' => '1 an', 'all' => 'Tout'] as $range => $label)
                            <button
                                class="filter-time-btn py-2 px-1 rounded-xl text-[11px] font-bold transition-all
                        {{ $range === 'all' ? 'bg-[#222a60] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                                data-range="{{ $range }}">{{ $label }}</button>
                        @endforeach
                    </div>
                    <div class="space-y-2">
                        <div>
                            <p class="text-[10px] text-slate-400 font-mono mb-1">Du</p>
                            <input type="date" id="date-start"
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#222a60]/20 focus:border-[#222a60]">
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-mono mb-1">Au</p>
                            <input type="date" id="date-end"
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#222a60]/20 focus:border-[#222a60]">
                        </div>
                    </div>
                </div>

            </div>

            <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/80">
                <p id="filter-summary" class="text-[11px] font-mono text-slate-500"></p>
            </div>
        </aside>

        <div class="flex-1 overflow-y-auto bg-slate-50">
            <div class="max-w-6xl mx-auto p-8 space-y-6">
                <div class="mb-4">
                    <div class="flex gap-3">
                        <button onclick="exportData('csv')" class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 shadow-sm transition-all">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export CSV
                        </button>
                        <button onclick="exportData('xlsx')" class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-xl text-sm font-bold hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition-all">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export Excel
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-4" id="kpi-row">
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-4">
                        <p class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-2">Analyses</p>
                        <p id="kpi-analyses" class="text-2xl font-bold text-[#222a60]">—</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-4">
                        <p class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-2">Cours d'eau
                        </p>
                        <p id="kpi-rivers" class="text-2xl font-bold text-[#222a60]">—</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-4">
                        <p class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-2">Période</p>
                        <p id="kpi-periode" class="text-sm font-bold text-[#222a60] mt-1">—</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-6">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-700">Évolution temporelle des mesures</h3>
                            <p class="text-[10px] text-slate-400 font-mono mt-0.5">Moyenne journalière · tous les cours
                                d'eau sélectionnés confondus</p>
                        </div>
                    </div>
                    <div class="relative h-80">
                        <canvas id="mainTimeChart"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-6">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-700">Moyenne par cours d'eau</h3>
                            <p class="text-[10px] text-slate-400 font-mono mt-0.5">Valeur moyenne sur la période
                                sélectionnée</p>
                        </div>
                        <select id="bar-mesure"
                            class="text-xs bg-slate-50 border border-slate-200 rounded-lg px-2 py-1.5 text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#222a60]/20">
                            <option value="nitrates">Nitrates</option>
                            <option value="nitrites">Nitrites</option>
                            <option value="ph">pH</option>
                            <option value="chlore">Chlore</option>
                            <option value="durete">Dureté</option>
                            <option value="phosphate">Phosphate</option>
                            <option value="ammoniaque">Ammoniaque</option>
                            <option value="nitrate_photo">Nitrate (photo)</option>
                        </select>
                    </div>
                    <div class="relative h-64">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-6">
                    <div class="mb-5">
                        <h3 class="text-sm font-semibold text-slate-700">Distribution de la qualité</h3>
                        <p class="text-[10px] text-slate-400 font-mono mt-0.5">Répartition des niveaux de qualité par cours
                            d'eau</p>
                    </div>
                    <div class="relative h-56">
                        <canvas id="qualiteChart"></canvas>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

<script>
    window.__RAW_DATA = @json($analyses);
    window.__COURS_DEAUX = @json($coursDEaux);
</script>
