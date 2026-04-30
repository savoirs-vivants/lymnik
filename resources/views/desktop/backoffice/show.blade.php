@extends('layouts.desktop')

@section('title', 'Profil Utilisateur')
@section('page-title', 'Profil de ' . $user->firstname . ' ' . $user->name)
@section('page-subtitle', 'Détail de l\'activité sur la plateforme')

@section('content')

    <div class="flex flex-col sm:flex-row gap-6 mb-8">

        <div class="flex flex-col gap-4 w-full sm:w-1/3 lg:w-1/4">
            <a href="{{ route('backoffice.index') }}"
                class="flex items-center gap-2 text-sm font-bold text-slate-400 hover:text-[#222a60] transition-colors w-fit">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Retour à la liste
            </a>

            <div
                class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-6 flex flex-col items-center text-center">
                <div
                    class="w-20 h-20 rounded-full bg-[#0F143A] text-white font-black text-2xl uppercase flex items-center justify-center shadow-md mb-4">
                    {{ strtoupper(substr($user->firstname, 0, 1) . substr($user->name, 0, 1)) }}
                </div>
                <h2 class="text-xl font-black text-[#222a60]">{{ $user->firstname }} {{ $user->name }}</h2>
                <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">{{ $user->email }}</p>
                <div class="mt-5 pt-5 border-t border-slate-100 w-full">
                    <p class="text-xs text-slate-500">Inscrit le : <span
                            class="font-bold text-slate-700">{{ $user->created_at->format('d/m/Y') }}</span></p>
                </div>
            </div>
        </div>

        <div class="flex-1 flex flex-col gap-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-emerald-50/50 rounded-2xl border border-emerald-100 shadow-sm p-5 relative overflow-hidden">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-emerald-100 text-emerald-600 rounded-lg">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-[11px] font-mono font-bold uppercase tracking-widest text-emerald-900/60">Analyses
                            validées</p>
                    </div>
                    <p class="text-3xl font-black text-emerald-700">{{ $totalAnalyses }}</p>
                </div>

                <div class="bg-blue-50/50 rounded-2xl border border-blue-100 shadow-sm p-5 relative overflow-hidden">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                        </div>
                        <p class="text-[11px] font-mono font-bold uppercase tracking-widest text-blue-900/60">Points
                            explorés</p>
                    </div>
                    <p class="text-3xl font-black text-blue-700">{{ $totalPoints }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 flex-1">
                <div
                    class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-5 flex flex-col items-center justify-center">
                    <h3 class="text-xs font-bold text-slate-700 w-full mb-4 text-left uppercase tracking-widest font-mono">
                        Qualité de l'eau (Historique)</h3>
                    @if ($qualiteData->isEmpty())
                        <p class="text-sm text-slate-400 italic my-auto">Aucune donnée disponible.</p>
                    @else
                        <div class="relative w-full max-w-[180px] aspect-square">
                            <canvas id="userQualiteChart"></canvas>
                        </div>
                    @endif
                </div>

                <div
                    class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-5 flex flex-col items-center justify-center">
                    <h3 class="text-xs font-bold text-slate-700 w-full mb-4 text-left uppercase tracking-widest font-mono">
                        Méthodes utilisées</h3>
                    @if ($typeData->isEmpty())
                        <p class="text-sm text-slate-400 italic my-auto">Aucune donnée disponible.</p>
                    @else
                        <div class="relative w-full h-full min-h-[180px]">
                            <canvas id="userTypeChart"></canvas>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-6 mb-8">
        <h2 class="text-sm font-bold text-slate-700 mb-4">Dernières analyses de l'utilisateur</h2>
        @if ($dernieresAnalyses->isEmpty())
            <p class="text-sm text-slate-400 italic">Aucune analyse pour le moment.</p>
        @else
            <div class="overflow-x-auto pb-2 -mx-4 px-4 sm:mx-0 sm:px-0">
                <table class="w-full text-sm min-w-[500px]">
                    <thead>
                        <tr
                            class="text-left text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">
                            <th class="pb-2 pr-4 whitespace-nowrap">Date</th>
                            <th class="pb-2 pr-4 whitespace-nowrap">Cours d'eau</th>
                            <th class="pb-2 pr-4 whitespace-nowrap">Méthode</th>
                            <th class="pb-2 pr-4 whitespace-nowrap">Qualité</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($dernieresAnalyses as $a)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2.5 pr-4 text-slate-500 whitespace-nowrap">
                                    {{ $a->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-2.5 pr-4 text-slate-600 whitespace-nowrap">
                                    {{ $a->point?->coursDEau?->nom ?? '—' }}</td>
                                <td class="py-2.5 pr-4 text-slate-600 whitespace-nowrap">
                                    {{ ucfirst(str_replace('_', ' ', $a->type)) }}</td>
                                <td class="py-2.5 pr-4 whitespace-nowrap">
                                    @if ($a->qualite)
                                        @php
                                            $q = strtolower($a->qualite);
                                            $badgeClass = match (true) {
                                                in_array($q, ['tres_bon', 'excellente'])
                                                    => 'bg-blue-50 text-blue-700 border border-blue-100',
                                                in_array($q, ['bon', 'bonne'])
                                                    => 'bg-emerald-50 text-emerald-700 border border-emerald-100',
                                                in_array($q, ['passable', 'acceptable'])
                                                    => 'bg-amber-50 text-amber-700 border border-amber-100',
                                                in_array($q, ['mediocre'])
                                                    => 'bg-orange-50 text-orange-700 border border-orange-100',
                                                in_array($q, ['mauvais', 'mauvaise'])
                                                    => 'bg-red-50 text-red-700 border border-red-100',
                                                default => 'bg-slate-50 text-slate-600 border border-slate-100',
                                            };
                                        @endphp
                                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-md {{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $a->qualite)) }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rawQualite = @json($qualiteData);
            const rawTypes = @json($typeData);

            const fontConfig = {
                family: "'Space Grotesk', sans-serif"
            };

            if (Object.keys(rawQualite).length > 0) {
                const ctxQualite = document.getElementById('userQualiteChart');

                const qualiteConfig = {
                    'tres_bon': {
                        label: 'Très bon',
                        color: '#10b981'
                    },
                    'bon': {
                        label: 'Bon',
                        color: '#3b82f6'
                    },
                    'passable': {
                        label: 'Passable',
                        color: '#f59e0b'
                    },
                    'mediocre': {
                        label: 'Médiocre',
                        color: '#f97316'
                    },
                    'mauvais': {
                        label: 'Mauvais',
                        color: '#ef4444'
                    }
                };

                let qLabels = [];
                let qData = [];
                let qColors = [];

                ['tres_bon', 'bon', 'passable', 'mediocre', 'mauvais'].forEach(key => {
                    if (rawQualite[key] || rawQualite[key] === 0) {
                        qLabels.push(qualiteConfig[key].label);
                        qData.push(rawQualite[key]);
                        qColors.push(qualiteConfig[key].color);
                    }
                });

                new Chart(ctxQualite, {
                    type: 'doughnut',
                    data: {
                        labels: qLabels,
                        datasets: [{
                            data: qData,
                            backgroundColor: qColors,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    font: fontConfig
                                }
                            }
                        }
                    }
                });
            }

            if (Object.keys(rawTypes).length > 0) {
                const ctxType = document.getElementById('userTypeChart');

                const typeLabelsMap = {
                    'bandelette': 'Bandelette',
                    'photometre': 'Photomètre',
                    'les_deux': 'Mixte (Les deux)'
                };

                let tLabels = Object.keys(rawTypes).map(k => typeLabelsMap[k] || k);
                let tData = Object.values(rawTypes);

                new Chart(ctxType, {
                    type: 'bar',
                    data: {
                        labels: tLabels,
                        datasets: [{
                            label: 'Nombre d\'analyses',
                            data: tData,
                            backgroundColor: '#222a60',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: 'rgba(0,0,0,0.04)'
                                },
                                ticks: {
                                    font: fontConfig,
                                    stepSize: 1
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: fontConfig
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
