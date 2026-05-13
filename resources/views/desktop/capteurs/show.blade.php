@extends('layouts.desktop')

@php
    $ids = array_filter([$capteur->devEUI, $capteur->UID]);
    $titreCapteur = !empty($ids) ? implode(' / ', $ids) : ($capteur->coursDEau->nom ?? 'Cours d\'eau non associé');
@endphp

@section('title', 'Capteur #' . $capteur->id)
@section('page-title', 'Capteur #' . $capteur->id)
@section('page-subtitle', $titreCapteur)

@section('content')

@php
    $kpis = [
        ['key' => 'temp_eau',     'label' => 'Température',  'unit' => '°C',    'color' => 'text-orange-500', 'bg' => 'bg-orange-50',  'border' => 'border-orange-100'],
        ['key' => 'debit',        'label' => 'Débit',        'unit' => 'L/min', 'color' => 'text-blue-500',   'bg' => 'bg-blue-50',    'border' => 'border-blue-100'],
        ['key' => 'hauteur',      'label' => 'Hauteur',      'unit' => 'cm',    'color' => 'text-cyan-500',   'bg' => 'bg-cyan-50',    'border' => 'border-cyan-100'],
        ['key' => 'turbidite',    'label' => 'Turbidité',    'unit' => 'NTU',   'color' => 'text-amber-500',  'bg' => 'bg-amber-50',   'border' => 'border-amber-100'],
        ['key' => 'conductivite', 'label' => 'Conductivité', 'unit' => 'µS/cm', 'color' => 'text-violet-500', 'bg' => 'bg-violet-50',  'border' => 'border-violet-100'],
    ];
    $derniere = $mesures->first();
@endphp

<div id="chart-data" class="hidden"
     data-capteur-id="{{ $capteur->id }}"
     data-chart-url="{{ route('capteurs.chart-data', $capteur->id) }}">
</div>

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

<div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-4 sm:p-5 mb-6">
    <div class="flex flex-wrap items-center gap-4 sm:gap-6">
        <div>
            <p class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1">Identifiant</p>
            <p class="text-sm font-semibold text-slate-800">{{ $titreCapteur }}</p>
        </div>
        @if($capteur->coursDEau)
        <div>
            <p class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1">Cours d'eau</p>
            <p class="text-sm font-semibold text-blue-600">{{ $capteur->coursDEau->nom }}</p>
        </div>
        @endif
        <div>
            <p class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1">Coordonnées</p>
            <p class="font-mono text-xs sm:text-sm text-slate-600">{{ $capteur->lat }}, {{ $capteur->long }}</p>
        </div>
        <div>
            <p class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1">Mesures (15 dern.)</p>
            <p class="text-sm font-semibold text-slate-800">{{ $mesures->count() }}</p>
        </div>
        @if ($derniere)
        <div>
            <p class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1">Dernière mesure</p>
            <p class="text-sm font-semibold text-slate-800">{{ $derniere->created_at->diffForHumans() }}</p>
        </div>
        @endif
    </div>
    <div class="mt-4 pt-4 border-t border-slate-100">
        <a href="{{ route('capteurs.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors no-underline">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-6 mb-6">
    <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
        <div>
            <h2 class="text-sm font-semibold text-slate-700">Évolution des paramètres</h2>
            <span id="chart-count" class="text-[10px] font-mono text-slate-400"></span>
        </div>

        <div class="flex flex-wrap items-end gap-3">
            {{-- Filtre temporel --}}
            <div>
                <p class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1.5">Période</p>
                <div class="flex items-center gap-1 bg-slate-100 rounded-xl p-1">
                    @foreach(['1m' => '1 mois', '6m' => '6 mois', '1a' => '1 an', 'custom' => 'Custom'] as $val => $label)
                    <button data-period="{{ $val }}"
                        class="chart-period-btn px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-all
                               {{ $val === '1m' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Plage custom (cachée par défaut) --}}
            <div id="custom-range" class="hidden">
                <p class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1.5">Plage personnalisée</p>
                <div class="flex items-center gap-2">
                    <input type="date" id="chart-from" class="text-xs border border-slate-200 rounded-lg px-2 py-1.5 text-slate-600 font-mono">
                    <span class="text-slate-400 text-xs">→</span>
                    <input type="date" id="chart-to" class="text-xs border border-slate-200 rounded-lg px-2 py-1.5 text-slate-600 font-mono">
                    <button id="chart-custom-apply" class="px-3 py-1.5 bg-sv-blue text-white text-[11px] font-semibold rounded-lg hover:opacity-90 transition-opacity">Appliquer</button>
                </div>
            </div>

            {{-- Limite du nombre de mesures affichées --}}
            <div>
                <p class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1.5">Max. mesures affichées</p>
                <div class="flex items-center gap-2">
                    <input type="number" id="chart-limit" value="50" min="5" max="2000" step="5"
                        class="w-24 text-xs border border-slate-200 rounded-lg px-2 py-1.5 text-slate-700 font-mono text-center">
                    <button id="chart-limit-apply" class="px-3 py-1.5 bg-slate-700 text-white text-[11px] font-semibold rounded-lg hover:opacity-90 transition-opacity">Appliquer</button>
                </div>
            </div>
        </div>
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
                    @foreach ($tableMesures as $m)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3 pr-4 text-slate-500 whitespace-nowrap font-mono text-xs">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 pr-4 font-mono font-semibold text-orange-500">{{ $m->temp_eau ?? '—' }} <span class="text-[10px] text-slate-400 font-normal">°C</span></td>
                        <td class="py-3 pr-4 font-mono font-semibold text-blue-500">{{ $m->debit ?? '—' }} <span class="text-[10px] text-slate-400 font-normal">L/min</span></td>
                        <td class="py-3 pr-4 font-mono text-cyan-600">{{ $m->hauteur ?? '—' }} <span class="text-[10px] text-slate-400 font-normal">cm</span></td>
                        <td class="py-3 pr-4 font-mono text-amber-600">{{ $m->turbidite ?? '—' }} <span class="text-[10px] text-slate-400 font-normal">NTU</span></td>
                        <td class="py-3 font-mono text-violet-600">{{ $m->conductivite ?? '—' }} <span class="text-[10px] text-slate-400 font-normal">µS/cm</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
