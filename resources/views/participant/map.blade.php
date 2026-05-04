@extends('layouts.participant')

@section('title', 'Carte')
@section('page-title', 'Carte')
@section('page-subtitle',
    $participant['nb_groupes'] > 0
        ? 'Groupe ' . chr(64 + $participant['id_groupe']) . ' — ' . $participant['campagne_nom']
        : $participant['pseudo'] . ' — ' . $participant['campagne_nom']
)

@section('content')

<div class="relative flex-1 w-full overflow-hidden flex flex-col">

    <div id="map" class="flex-1 w-full z-0 outline-none bg-slate-200"></div>

    {{-- Contrôles --}}
    <div class="absolute top-3 left-3 right-3 md:left-4 md:top-4 md:right-auto md:w-80 z-10 space-y-2 pointer-events-none">
        <div class="bg-white/95 backdrop-blur shadow-lg rounded-2xl p-2 flex items-center border border-slate-100 pointer-events-auto relative">
            <input type="text" id="search-input" placeholder="Rechercher un lieu..."
                class="flex-1 px-3 bg-transparent outline-none text-sm font-bold text-slate-800 placeholder-slate-400">
            <div id="search-results" class="absolute top-full left-0 right-0 bg-white mt-2 rounded-xl shadow-xl hidden overflow-hidden border border-slate-100"></div>
        </div>
    </div>

    {{-- Bottom sheet : détail d'un point existant --}}
    <div id="bottom-sheet"
        class="absolute bottom-0 left-0 right-0 md:top-4 md:right-4 md:bottom-4 md:left-auto md:w-[340px]
               bg-white z-[1000] rounded-t-3xl md:rounded-3xl
               max-h-[70vh] md:max-h-none
               shadow-[0_-4px_30px_rgba(34,42,96,0.12)] md:shadow-[0_10px_50px_rgba(34,42,96,0.15)]
               border border-slate-100
               translate-y-full md:translate-x-[120%]
               [&.open]:translate-y-0 [&.open]:translate-x-0
               transition-transform duration-400 ease-out flex flex-col overflow-hidden">

        <div class="md:hidden flex justify-center pt-3 pb-1"><div class="w-10 h-1 bg-slate-200 rounded-full"></div></div>

        <div class="p-4 md:p-5 overflow-y-auto flex-1">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <p class="sheet-type text-[10px] font-black text-[#222a60] uppercase mb-0.5"></p>
                    <h2 class="sheet-river text-lg font-black text-[#222a60]"></h2>
                </div>
                <button id="sheet-close"
                    class="text-slate-300 hover:text-slate-600 transition-colors">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="sheet-content"></div>
        </div>
    </div>

    {{-- Overlay nouvelle analyse --}}
    <div id="create-card"
        class="absolute bottom-4 left-4 right-4 md:left-16 md:right-auto md:w-[400px] z-[1000]
               opacity-0 pointer-events-none [&.show]:opacity-100 [&.show]:pointer-events-auto
               transition-opacity duration-300">

        <div class="bg-white rounded-3xl shadow-[0_20px_60px_rgba(34,42,96,0.18)] border border-slate-100 p-5 flex flex-col gap-4">

            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-0.5">Nouvelle analyse</p>
                    <h3 id="create-river-name" class="text-base font-black text-[#222a60]"></h3>
                    <p id="create-coords" class="text-[11px] text-slate-400 font-mono mt-0.5"></p>
                </div>
                <button id="create-close"
                    class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-400 transition-colors">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="form-analyse" class="space-y-3">
                <input type="hidden" id="f-lat">
                <input type="hidden" id="f-lng">
                <input type="hidden" id="f-cours-eau-id">

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Type d'analyse</label>
                    <select id="f-type"
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#222a60]/20 transition-all cursor-pointer">
                        <option value="bandelette">Bandelette</option>
                        <option value="photometre">Photomètre</option>
                        <option value="les_deux">Les deux</option>
                    </select>
                </div>

                {{-- Mesures bandelette --}}
                <div id="section-bandelette" class="grid grid-cols-2 gap-2">
                    @foreach(['nitrates' => 'Nitrates (mg/L)', 'nitrites' => 'Nitrites (mg/L)', 'ph' => 'pH', 'chlore' => 'Chlore (mg/L)'] as $key => $label)
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">{{ $label }}</label>
                        <input type="number" step="0.01" name="mesures[bandelette][{{ $key }}]"
                            class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700 focus:outline-none focus:ring-1 focus:ring-[#222a60]/20 transition-all">
                    </div>
                    @endforeach
                </div>

                {{-- Mesures photomètre --}}
                <div id="section-photometre" class="grid grid-cols-2 gap-2 hidden">
                    @foreach(['phosphate' => 'Phosphate (mg/L)', 'nitrate' => 'Nitrate (mg/L)', 'ammoniaque' => 'Ammoniaque (mg/L)'] as $key => $label)
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 mb-0.5">{{ $label }}</label>
                        <input type="number" step="0.01" name="mesures[photometre][{{ $key }}]"
                            class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-700 focus:outline-none focus:ring-1 focus:ring-[#222a60]/20 transition-all">
                    </div>
                    @endforeach
                </div>

                <p id="form-error" class="text-xs text-red-500 font-medium"></p>

                <button type="submit"
                    class="w-full py-2.5 rounded-xl bg-[#222a60] text-white font-bold text-sm hover:bg-[#1a2050] transition-colors">
                    Enregistrer l'analyse
                </button>
            </form>
        </div>
    </div>

    {{-- Bouton localisation --}}
    <button id="locate-btn"
        class="absolute bottom-5 right-5 z-[1000] w-10 h-10 bg-white rounded-xl shadow-lg border border-slate-100 flex items-center justify-center text-[#222a60] hover:bg-slate-50 transition-colors">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="3"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v3m0 14v3M2 12h3m14 0h3"/>
        </svg>
    </button>

</div>

@endsection

@push('scripts')
<script>
window.__participantPoints = @json($points);
window.__participantData   = @json($participant);
</script>
<script src="{{ Vite::asset('resources/js/participant-map.js') }}"></script>
@endpush
