@extends('layouts.participant')

@section('title', 'Comparaison')
@section('page-title', 'Comparaison')
@section('page-subtitle', $campagne->nom)

@section('content')

@php
$qualiteColors = [
    'tres_bon' => '#10b981',
    'bon'      => '#14b8a6',
    'passable' => '#eab308',
    'mediocre' => '#f97316',
    'mauvais'  => '#ef4444',
];
$qualiteLabels = [
    'tres_bon' => 'Très bon',
    'bon'      => 'Bon',
    'passable' => 'Passable',
    'mediocre' => 'Médiocre',
    'mauvais'  => 'Mauvais',
];
$groupColors = ['#222a60','#16987c','#f59e0b','#8b5cf6','#ef4444','#0ea5e9','#ec4899','#84cc16'];
@endphp

<div class="max-w-5xl mx-auto p-4 sm:p-6 space-y-6">

    {{-- En-tête --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('participant.analyses') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="text-lg font-black text-[#222a60]">{{ $campagne->nom }}</h2>
            <p class="text-xs text-slate-400 font-mono">{{ count($groupesData) }} {{ $participant['nb_groupes'] > 0 ? 'groupe' . (count($groupesData) > 1 ? 's' : '') : 'participant' . (count($groupesData) > 1 ? 's' : '') }}</p>
        </div>
    </div>

    @if(empty($groupesData))
        <div class="bg-white rounded-2xl border border-slate-100 p-10 text-center">
            <p class="text-slate-400 italic text-sm">Aucune donnée disponible pour la comparaison.</p>
        </div>
    @else

    {{-- Cartes résumé --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
        @foreach($groupesData as $i => $g)
        @php $color = $groupColors[$i % count($groupColors)]; @endphp
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-3 h-3 rounded-full flex-shrink-0" style="background:{{ $color }}"></span>
                <p class="text-sm font-bold text-slate-700 truncate">{{ $g['label'] }}</p>
            </div>
            <p class="text-2xl font-black" style="color:{{ $color }}">{{ $g['analyses']['total'] }}</p>
            <p class="text-[11px] text-slate-400 font-mono">analyse{{ $g['analyses']['total'] > 1 ? 's' : '' }}</p>
        </div>
        @endforeach
    </div>

    {{-- Graphique répartition qualité par groupe --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
        <h3 class="text-sm font-bold text-[#222a60] mb-4">Répartition de la qualité par {{ $participant['nb_groupes'] > 0 ? 'groupe' : 'participant' }}</h3>
        <div class="h-[280px]">
            <canvas id="chart-qualite-compare"></canvas>
        </div>
    </div>

    {{-- Graphique moyennes paramètres --}}
    @php
        $allParams = collect($groupesData)->flatMap(fn($g) => array_keys($g['analyses']['param_means']))->unique()->values()->toArray();
    @endphp
    @if(!empty($allParams))
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-[#222a60]">Moyennes des paramètres</h3>
            <div class="flex gap-2">
                @foreach($allParams as $param)
                <button onclick="showParam('{{ $param }}')"
                    class="param-btn px-2.5 py-1 rounded-lg text-[11px] font-bold border border-slate-200 text-slate-500 hover:border-[#222a60] hover:text-[#222a60] transition-colors"
                    data-param="{{ $param }}">
                    {{ ucfirst($param) }}
                </button>
                @endforeach
            </div>
        </div>
        <div class="h-[250px]">
            <canvas id="chart-params"></canvas>
        </div>
    </div>
    @endif

    {{-- Évolution chronologique superposée --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
        <h3 class="text-sm font-bold text-[#222a60] mb-4">Évolution de la qualité (superposition)</h3>
        <div class="flex flex-wrap gap-3 mb-4">
            @foreach($groupesData as $i => $g)
            @php $color = $groupColors[$i % count($groupColors)]; @endphp
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-2.5 rounded-sm flex-shrink-0" style="background:{{ $color }}"></span>
                <span class="text-xs text-slate-500">{{ $g['label'] }}</span>
            </div>
            @endforeach
        </div>
        <div class="h-[250px]">
            <canvas id="chart-timeline"></canvas>
        </div>
    </div>

    {{-- Seuils de référence --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-6">
        <h3 class="text-sm font-bold text-[#222a60] mb-3">Seuils réglementaires de référence</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach([
                ['label' => 'Nitrites',   'unit' => 'mg/L', 'bon' => '≤ 0,03', 'max' => '1,0'],
                ['label' => 'Nitrates',   'unit' => 'mg/L', 'bon' => '≤ 2',    'max' => '50'],
                ['label' => 'Phosphate',  'unit' => 'mg/L', 'bon' => '≤ 0,05', 'max' => '1,0'],
                ['label' => 'Chlore',     'unit' => 'mg/L', 'bon' => '≤ 25',   'max' => '250'],
                ['label' => 'Ammoniaque', 'unit' => 'mg/L', 'bon' => '≤ 0,1',  'max' => '5,0'],
                ['label' => 'pH',         'unit' => '',     'bon' => '6,5–8,5', 'max' => '≠'],
            ] as $seuil)
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <p class="text-xs font-bold text-slate-600">{{ $seuil['label'] }} <span class="font-normal text-slate-400">{{ $seuil['unit'] }}</span></p>
                <p class="text-[11px] text-emerald-600 font-semibold mt-0.5">Très bon : {{ $seuil['bon'] }}</p>
                <p class="text-[11px] text-red-500 font-semibold">Mauvais : &gt; {{ $seuil['max'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    @endif

</div>

@endsection

@push('scripts')
<script>
window.__groupesData  = @json($groupesData);
window.__groupColors  = @json($groupColors);
window.__qualiteLabels = @json($qualiteLabels);
</script>
<script src="{{ Vite::asset('resources/js/participant-comparer.js') }}"></script>
@endpush
