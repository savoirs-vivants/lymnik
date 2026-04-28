@extends('layouts.desktop')

@section('title', 'Capteur #' . $capteur->id)
@section('page-title', 'Capteur #' . $capteur->id)
@section('page-subtitle', $capteur->coursDEau->nom ?? 'Cours d\'eau non associé')

@section('content')

@php
    $kpis = [
        ['key' => 'temp_eau',     'label' => 'Température',  'unit' => '°C',    'color' => 'text-orange-500', 'bg' => 'bg-orange-50',  'border' => 'border-orange-100'],
        ['key' => 'debit',        'label' => 'Débit',        'unit' => 'm³/s',  'color' => 'text-blue-500',   'bg' => 'bg-blue-50',    'border' => 'border-blue-100'],
        ['key' => 'hauteur',      'label' => 'Hauteur',      'unit' => 'm',     'color' => 'text-cyan-500',   'bg' => 'bg-cyan-50',    'border' => 'border-cyan-100'],
        ['key' => 'turbidite',    'label' => 'Turbidité',    'unit' => 'NTU',   'color' => 'text-amber-500',  'bg' => 'bg-amber-50',   'border' => 'border-amber-100'],
        ['key' => 'conductivite', 'label' => 'Conductivité', 'unit' => 'µS/cm', 'color' => 'text-violet-500', 'bg' => 'bg-violet-50',  'border' => 'border-violet-100'],
    ];
    $derniere = $mesures->first();
@endphp

{{-- KPI dernières valeurs --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    @foreach ($kpis as $k)
    @php $val = $derniere?->{$k['key']}; @endphp
    <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-4">
        <p class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-2">{{ $k['label'] }}</p>
        <p class="text-2xl font-bold {{ $val !== null ? $k['color'] : 'text-slate-300' }}">
            {{ $val !== null ? $val : '—' }}
        </p>
        @if ($val !== null)
            <p class="text-[10px] text-slate-400 mt-0.5">{{ $k['unit'] }}</p>
        @endif
    </div>
    @endforeach
</div>

{{-- Infos capteur + statut --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-5 mb-6 flex flex-wrap items-center gap-6">
    <div>
        <p class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1">Cours d'eau</p>
        <p class="text-sm font-semibold text-slate-800">{{ $capteur->coursDEau->nom ?? '—' }}</p>
    </div>
    <div>
        <p class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1">Coordonnées</p>
        <p class="font-mono text-sm text-slate-600">{{ $capteur->lat }}, {{ $capteur->long }}</p>
    </div>
    <div>
        <p class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1">Mesures (50 dern.)</p>
        <p class="text-sm font-semibold text-slate-800">{{ $mesures->count() }}</p>
    </div>
    @if ($derniere)
    <div>
        <p class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1">Dernière mesure</p>
        <p class="text-sm font-semibold text-slate-800">{{ $derniere->created_at->diffForHumans() }}</p>
    </div>
    @endif
    <div class="ml-auto">
        <a href="{{ route('capteurs.index') }}"
            class="flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors no-underline">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour
        </a>
    </div>
</div>

{{-- Graphique --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-6 mb-6">
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-sm font-semibold text-slate-700">Évolution des paramètres</h2>
        <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">50 dernières mesures</span>
    </div>
    <div class="relative w-full h-72 lg:h-96">
        @if ($mesures->isEmpty())
            <div class="absolute inset-0 flex items-center justify-center text-slate-400 text-sm italic">
                Aucune donnée historique pour ce capteur.
            </div>
        @else
            <canvas id="capteurChart"></canvas>
        @endif
    </div>
</div>

{{-- Tableau historique --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-6">
    <h2 class="text-sm font-semibold text-slate-700 mb-4">Historique des mesures</h2>

    @if ($mesures->isEmpty())
        <p class="text-sm text-slate-400 italic text-center py-8">Aucune mesure enregistrée.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">
                        <th class="pb-3 pr-4">Date & Heure</th>
                        <th class="pb-3 pr-4">Température</th>
                        <th class="pb-3 pr-4">Débit</th>
                        <th class="pb-3 pr-4">Hauteur</th>
                        <th class="pb-3 pr-4">Turbidité</th>
                        <th class="pb-3">Conductivité</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach ($mesures as $m)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3 pr-4 text-slate-500 whitespace-nowrap font-mono text-xs">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 pr-4 font-mono font-semibold text-orange-500">{{ $m->temp_eau ?? '—' }} <span class="text-[10px] text-slate-400 font-normal">°C</span></td>
                        <td class="py-3 pr-4 font-mono font-semibold text-blue-500">{{ $m->debit ?? '—' }} <span class="text-[10px] text-slate-400 font-normal">m³/s</span></td>
                        <td class="py-3 pr-4 font-mono text-cyan-600">{{ $m->hauteur ?? '—' }} <span class="text-[10px] text-slate-400 font-normal">m</span></td>
                        <td class="py-3 pr-4 font-mono text-amber-600">{{ $m->turbidite ?? '—' }} <span class="text-[10px] text-slate-400 font-normal">NTU</span></td>
                        <td class="py-3 font-mono text-violet-600">{{ $m->conductivite ?? '—' }} <span class="text-[10px] text-slate-400 font-normal">µS/cm</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const data = {
        labels:       @json($graphLabels ?? []),
        temp:         @json($graphTemp ?? []),
        debit:        @json($graphDebit ?? []),
        hauteur:      @json($graphHauteur ?? []),
        turbidite:    @json($graphTurbidite ?? []),
        conductivite: @json($graphConductivite ?? [])
    };

    if (!data.labels.length) return;

    const ctx = document.getElementById('capteurChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Température (°C)',
                    data: data.temp,
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249,115,22,0.08)',
                    borderWidth: 2,
                    pointRadius: 2,
                    tension: 0.4,
                    fill: false,
                    yAxisID: 'y',
                },
                {
                    label: 'Débit (m³/s)',
                    data: data.debit,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.08)',
                    borderWidth: 2,
                    pointRadius: 2,
                    tension: 0.4,
                    fill: false,
                    yAxisID: 'y',
                },
                {
                    label: 'Hauteur (m)',
                    data: data.hauteur,
                    borderColor: '#06b6d4',
                    backgroundColor: 'rgba(6,182,212,0.08)',
                    borderWidth: 2,
                    pointRadius: 2,
                    tension: 0.4,
                    fill: false,
                    yAxisID: 'y',
                },
                {
                    label: 'Turbidité (NTU)',
                    data: data.turbidite,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245,158,11,0.08)',
                    borderWidth: 2,
                    pointRadius: 2,
                    tension: 0.4,
                    fill: false,
                    yAxisID: 'y',
                },
                {
                    label: 'Conductivité (µS/cm)',
                    data: data.conductivite,
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139,92,246,0.08)',
                    borderWidth: 2,
                    pointRadius: 2,
                    tension: 0.4,
                    fill: false,
                    yAxisID: 'yRight',
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        pointStyleWidth: 8,
                        padding: 16,
                        font: { family: "'Space Grotesk', sans-serif", size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleFont: { family: "'Space Grotesk', sans-serif", size: 12 },
                    bodyFont: { family: "'Space Mono', monospace", size: 11 },
                    padding: 12,
                    cornerRadius: 10,
                    borderColor: 'rgba(255,255,255,0.08)',
                    borderWidth: 1,
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { family: "'Space Mono', monospace", size: 10 },
                        color: '#94a3b8',
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 10,
                    }
                },
                y: {
                    type: 'linear',
                    position: 'left',
                    grid: { color: 'rgba(0,0,0,0.04)', borderDash: [4, 4] },
                    ticks: { font: { family: "'Space Mono', monospace", size: 10 }, color: '#94a3b8' },
                    title: { display: true, text: 'Valeurs', font: { size: 10, family: "'Space Mono', monospace" }, color: '#94a3b8' }
                },
                yRight: {
                    type: 'linear',
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: { font: { family: "'Space Mono', monospace", size: 10 }, color: '#8b5cf6' },
                    title: { display: true, text: 'µS/cm', font: { size: 10, family: "'Space Mono', monospace" }, color: '#8b5cf6' }
                }
            }
        }
    });
});
</script>
@endpush

@endsection
