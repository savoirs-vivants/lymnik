@extends('layouts.desktop')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')
@section('page-subtitle', $isAdmin ? 'Vue globale de la plateforme' : 'Vos données personnelles')

@section('content')

    {{-- Bouton lancer une campagne --}}
    <div class="flex justify-end mb-4">
        <button id="btn-lancer-campagne"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#222a60] text-white text-sm font-semibold hover:bg-[#1a2050] transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Lancer une campagne
        </button>
    </div>

    {{-- Modal création campagne --}}
    <div id="modal-campagne" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div id="modal-campagne-backdrop" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden z-10">
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                <h3 class="text-lg font-black text-[#222a60] font-grotesk">Lancer une campagne</h3>
                <button id="modal-campagne-close" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Étape 1 : formulaire --}}
            <div id="campagne-step1" class="p-6">
                <form id="form-campagne" class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nom de la campagne</label>
                        <input id="campagne-nom" type="text" required placeholder="Ex : Sortie Garonne 3ème B"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#222a60]/25 focus:border-[#222a60] transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nombre de groupes <span class="font-normal text-slate-400">(0 = mode individuel)</span></label>
                        <input id="campagne-nb-groupes" type="number" min="0" max="26" value="0" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#222a60]/25 focus:border-[#222a60] transition-all">
                    </div>
                    <p id="campagne-error" class="text-sm text-red-500 font-medium"></p>
                    <button type="submit"
                        class="w-full py-3 rounded-xl bg-[#222a60] text-white font-bold text-sm hover:bg-[#1a2050] transition-colors">
                        Créer la campagne
                    </button>
                </form>
            </div>

            {{-- Étape 2 : code généré --}}
            <div id="campagne-step2" class="hidden p-6 text-center space-y-5">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500 mb-1">Campagne créée ! Partagez ce code :</p>
                    <div class="flex items-center justify-center gap-3 mt-2">
                        <span id="campagne-code-display" class="font-mono text-3xl font-black text-[#222a60] tracking-widest"></span>
                        <button id="campagne-copy-btn"
                            class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-600 transition-colors">
                            Copier
                        </button>
                    </div>
                </div>
                <p id="campagne-groups-info" class="text-sm text-slate-500"></p>
                <button onclick="document.getElementById('modal-campagne').classList.add('hidden')"
                    class="w-full py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 sm:mb-8">

        @if ($isAdmin)
            <div class="bg-indigo-50/50 rounded-2xl border border-indigo-100 shadow-sm p-4 sm:p-5 relative overflow-hidden">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <p class="text-[11px] font-mono font-bold uppercase tracking-widest text-indigo-900/60">Utilisateurs</p>
                </div>
                <p class="text-3xl font-black text-indigo-900">{{ $totalUsers }}</p>
            </div>
        @endif

        <div class="bg-emerald-50/50 rounded-2xl border border-emerald-100 shadow-sm p-4 sm:p-5 relative overflow-hidden">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <p class="text-[11px] font-mono font-bold uppercase tracking-widest text-emerald-900/60">
                    {{ $isAdmin ? 'Analyses' : 'Mes analyses' }}</p>
            </div>
            <p class="text-3xl font-black text-emerald-700">{{ $totalAnalyses }}</p>
        </div>

        <div class="bg-blue-50/50 rounded-2xl border border-blue-100 shadow-sm p-4 sm:p-5 relative overflow-hidden">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <p class="text-[11px] font-mono font-bold uppercase tracking-widest text-blue-900/60">Points analysés</p>
            </div>
            <p class="text-3xl font-black text-blue-700">{{ $totalPoints }}</p>
        </div>

        @if ($isAdmin)
            <div class="bg-amber-50/50 rounded-2xl border border-amber-100 shadow-sm p-4 sm:p-5 relative overflow-hidden">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-amber-100 text-amber-600 rounded-lg">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                        </svg>
                    </div>
                    <p class="text-[11px] font-mono font-bold uppercase tracking-widest text-amber-900/60">Capteurs</p>
                </div>
                <p class="text-3xl font-black text-amber-600">{{ $totalCapteurs }}</p>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">

        <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-4 sm:p-6 flex flex-col items-center">
            <h2 class="text-sm font-bold text-slate-700 w-full mb-4 text-center sm:text-left">Répartition : Qualité de l'eau</h2>
            @if ($qualiteData->isEmpty())
                <p class="text-sm text-slate-400 italic my-auto">Aucune donnée de qualité disponible.</p>
            @else
                <div class="relative w-full max-w-[200px] sm:max-w-[250px] aspect-square">
                    <canvas id="qualiteChart"></canvas>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-4 sm:p-6 flex flex-col items-center">
            <h2 class="text-sm font-bold text-slate-700 w-full mb-4 text-center sm:text-left">Utilisation des méthodes d'analyse</h2>
            @if ($typeData->isEmpty())
                <p class="text-sm text-slate-400 italic my-auto">Aucune analyse enregistrée.</p>
            @else
                <div class="relative w-full h-full min-h-[200px] sm:min-h-[250px]">
                    <canvas id="typeChart"></canvas>
                </div>
            @endif
        </div>

    </div>

    <div class="grid grid-cols-1 {{ $isAdmin ? 'lg:grid-cols-2' : '' }} gap-4 sm:gap-6 mb-6 sm:mb-8">
        <div class="{{ $isAdmin ? 'lg:col-span-2' : '' }} bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-4 sm:p-6">
            <h2 class="text-sm font-bold text-slate-700 mb-4">
                {{ $isAdmin ? 'Dernières analyses' : 'Mes dernières analyses' }}
            </h2>
            @if ($dernieresAnalyses->isEmpty())
                <p class="text-sm text-slate-400 italic">Aucune analyse pour le moment.</p>
            @else
                <div class="overflow-x-auto pb-2 -mx-4 px-4 sm:mx-0 sm:px-0">
                    <table class="w-full text-sm min-w-[500px]">
                        <thead>
                            <tr class="text-left text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">
                                <th class="pb-2 pr-3 sm:pr-4 whitespace-nowrap">Date</th>
                                @if ($isAdmin)
                                    <th class="pb-2 pr-3 sm:pr-4 whitespace-nowrap">Utilisateur</th>
                                @endif
                                <th class="pb-2 pr-3 sm:pr-4 whitespace-nowrap">Cours d'eau</th>
                                <th class="pb-2 pr-3 sm:pr-4 whitespace-nowrap">Type</th>
                                <th class="pb-2 pr-3 sm:pr-4 whitespace-nowrap">Qualité</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($dernieresAnalyses as $a)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-2.5 pr-3 sm:pr-4 text-slate-500 whitespace-nowrap">
                                        {{ $a->created_at->format('d/m/Y') }}</td>
                                    @if ($isAdmin)
                                        <td class="py-2.5 pr-3 sm:pr-4 font-medium text-slate-700 whitespace-nowrap">{{ $a->user?->firstname }}
                                            {{ $a->user?->name }}</td>
                                    @endif
                                    <td class="py-2.5 pr-3 sm:pr-4 text-slate-600 whitespace-nowrap">{{ $a->point?->coursDEau?->nom ?? '—' }}</td>
                                    <td class="py-2.5 pr-3 sm:pr-4 text-slate-600 whitespace-nowrap">{{ $a->type }}</td>
                                    <td class="py-2.5 pr-3 sm:pr-4 whitespace-nowrap">
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
    </div>

    @if ($isAdmin && $dernieresMesures->isNotEmpty())
        <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(34,42,96,0.06)] p-4 sm:p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4">Dernières mesures capteurs</h2>
            <div class="overflow-x-auto pb-2 -mx-4 px-4 sm:mx-0 sm:px-0">
                <table class="w-full text-sm min-w-[700px]">
                    <thead>
                        <tr class="text-left text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100">
                            <th class="pb-2 pr-3 sm:pr-4 whitespace-nowrap">Date</th>
                            <th class="pb-2 pr-3 sm:pr-4 whitespace-nowrap">Cours d'eau</th>
                            <th class="pb-2 pr-3 sm:pr-4 whitespace-nowrap">Turbidité (NTU)</th>
                            <th class="pb-2 pr-3 sm:pr-4 whitespace-nowrap">Conductivité (µS/cm)</th>
                            <th class="pb-2 pr-3 sm:pr-4 whitespace-nowrap">Temp. eau (°C)</th>
                            <th class="pb-2 pr-3 sm:pr-4 whitespace-nowrap">Hauteur (m)</th>
                            <th class="pb-2 whitespace-nowrap">Débit (m³/s)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($dernieresMesures as $m)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2.5 pr-3 sm:pr-4 text-slate-500 whitespace-nowrap">
                                    {{ $m->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-2.5 pr-3 sm:pr-4 text-slate-600 whitespace-nowrap">{{ $m->capteur->coursDEau->nom ?? '—' }}</td>
                                <td class="py-2.5 pr-3 sm:pr-4 font-mono text-slate-700 whitespace-nowrap">{{ $m->turbidite ?? '—' }}</td>
                                <td class="py-2.5 pr-3 sm:pr-4 font-mono text-slate-700 whitespace-nowrap">{{ $m->conductivite ?? '—' }}</td>
                                <td class="py-2.5 pr-3 sm:pr-4 font-mono text-slate-700 whitespace-nowrap">{{ $m->temp_eau ?? '—' }}</td>
                                <td class="py-2.5 pr-3 sm:pr-4 font-mono text-slate-700 whitespace-nowrap">{{ $m->hauteur ?? '—' }}</td>
                                <td class="py-2.5 font-mono text-slate-700 whitespace-nowrap">{{ $m->debit ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

@endsection

<script>
    window.dashboardData = {
        qualite: @json($qualiteData),
        types: @json($typeData)
    };
</script>
