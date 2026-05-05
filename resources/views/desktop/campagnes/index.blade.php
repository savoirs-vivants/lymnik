@extends('layouts.desktop')

@section('title', 'Mes campagnes')
@section('page-title', 'Mes campagnes')
@section('page-subtitle', 'Gérer vos sessions de terrain')

@section('content')

<div class="max-w-5xl mx-auto">

    @if ($campagnes->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
            <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <p class="text-base font-bold text-slate-600">Aucune campagne créée</p>
            <p class="text-sm text-slate-400 mt-1">Lancez votre première campagne depuis le tableau de bord.</p>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-xl bg-[#222a60] text-white text-sm font-semibold hover:bg-[#1a2050] transition-colors">
                Aller au tableau de bord
            </a>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <p class="text-sm font-bold text-slate-700">{{ $campagnes->count() }} campagne{{ $campagnes->count() > 1 ? 's' : '' }}</p>
            </div>
            <div class="divide-y divide-slate-50">
                @foreach ($campagnes as $c)
                @php
                    $expired = $c->date_fin && $c->date_fin->isPast();
                @endphp
                <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-4" data-campagne-id="{{ $c->id }}">

                    {{-- Infos --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <p class="campagne-nom-display text-base font-black text-[#222a60]">{{ $c->nom }}</p>
                            <span class="font-mono text-xs font-bold tracking-widest text-slate-400 bg-slate-100 px-2 py-0.5 rounded-lg">{{ $c->code }}</span>
                            @if($expired)
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-red-50 text-red-500 border border-red-100">Terminée</span>
                            @else
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100">Active</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4 mt-1.5 flex-wrap">
                            <span class="text-[11px] text-slate-400 font-mono">
                                {{ $c->nb_groupes > 0 ? $c->nb_groupes . ' groupe' . ($c->nb_groupes > 1 ? 's' : '') : 'Mode individuel' }}
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">
                                Fin : {{ $c->date_fin?->format('d/m/Y') ?? '—' }}
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono campagne-nb-participants">
                                {{ $c->participants_count }} participant{{ $c->participants_count > 1 ? 's' : '' }}
                            </span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 shrink-0">
                        <button onclick="showParticipants({{ $c->id }}, '{{ addslashes($c->nom) }}')"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Participants
                        </button>
                        <button onclick="openEditModal({{ $c->id }}, '{{ addslashes($c->nom) }}', {{ $c->nb_groupes }}, '{{ $c->date_fin?->format('Y-m-d') ?? '' }}')"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#222a60]/8 hover:bg-[#222a60]/15 text-[#222a60] text-xs font-semibold transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Modifier
                        </button>
                        <button onclick="deleteCampagne({{ $c->id }}, '{{ addslashes($c->nom) }}')"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-500 text-xs font-semibold transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Supprimer
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @endif

</div>

{{-- Modal Édition --}}
<div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <h3 class="text-base font-black text-[#222a60]">Modifier la campagne</h3>
            <button onclick="closeEditModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="form-edit" class="p-6 space-y-4">
            <input type="hidden" id="edit-id">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nom de la campagne</label>
                <input id="edit-nom" type="text" required maxlength="255"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#222a60]/25 focus:border-[#222a60] transition-all">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nombre de groupes <span class="font-normal text-slate-400">(0 = individuel)</span></label>
                <input id="edit-nb-groupes" type="number" min="0" max="26" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#222a60]/25 focus:border-[#222a60] transition-all">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Date de fin</label>
                <input id="edit-date-fin" type="date" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#222a60]/25 focus:border-[#222a60] transition-all">
            </div>
            <p id="edit-error" class="text-sm text-red-500 font-medium"></p>
            <button type="submit"
                class="w-full py-3 rounded-xl bg-[#222a60] text-white font-bold text-sm hover:bg-[#1a2050] transition-colors">
                Enregistrer
            </button>
        </form>
    </div>
</div>

{{-- Modal Participants --}}
<div id="modal-participants" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeParticipantsModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 overflow-hidden max-h-[80vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 shrink-0">
            <div>
                <h3 class="text-base font-black text-[#222a60]">Participants</h3>
                <p id="participants-campagne-nom" class="text-xs text-slate-400 mt-0.5"></p>
            </div>
            <button onclick="closeParticipantsModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="participants-list" class="flex-1 overflow-y-auto p-4">
            <div class="flex items-center justify-center py-8">
                <div class="flex gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-slate-300 animate-bounce" style="animation-delay:0s"></span>
                    <span class="w-2 h-2 rounded-full bg-slate-300 animate-bounce" style="animation-delay:.15s"></span>
                    <span class="w-2 h-2 rounded-full bg-slate-300 animate-bounce" style="animation-delay:.3s"></span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
window.__csrfToken = document.querySelector('meta[name="csrf-token"]').content;
</script>
<script src="{{ Vite::asset('resources/js/campagnes-gestion.js') }}"></script>
@endpush
