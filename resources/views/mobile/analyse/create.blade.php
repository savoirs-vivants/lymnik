@extends('layouts.mobile')
@section('title', 'Création de point')

@section('content')
<div id="page-shell" class="flex flex-col h-[100dvh] overflow-hidden bg-slate-50 font-grotesk text-slate-900">
    <div id="page-header" class="bg-gradient-to-br from-[#0d1533] via-[#0f1d42] to-[#1a2a6c] pt-[max(48px,env(safe-area-inset-top))] pb-0 shrink-0 relative z-10">
        <div class="flex items-center justify-center px-4 pb-3 relative">
            <a href="{{ route('index_mobile') }}" aria-label="Retour"
               class="absolute left-4 w-[34px] h-[34px] rounded-full bg-white/10 flex items-center justify-center text-white cursor-pointer transition-colors active:bg-white/20 no-underline">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <div class="text-white text-[17px] font-bold text-center">Nouvelle analyse</div>
                <div class="text-white/55 text-xs text-center mt-[1px] font-mono" id="header-subtitle">
                    {{ now()->translatedFormat('d M Y') }}
                </div>
            </div>
        </div>

        <div id="step-bar" class="flex border-t border-white/10 mt-2.5">
            <div class="step-tab flex-1 text-center py-2.5 px-1 font-mono text-[9px] font-bold tracking-widest uppercase border-b-2 transition-colors duration-250 cursor-pointer text-white border-white" data-target="section-1">Localisation</div>
            <div class="step-tab flex-1 text-center py-2.5 px-1 font-mono text-[9px] font-bold tracking-widest uppercase border-b-2 transition-colors duration-250 cursor-pointer text-white/30 border-transparent [&.active]:text-white [&.active]:border-white [&.done]:text-[#16987c] [&.done]:border-[#16987c]/50" data-target="section-2">Photo</div>
            <div class="step-tab flex-1 text-center py-2.5 px-1 font-mono text-[9px] font-bold tracking-widest uppercase border-b-2 transition-colors duration-250 cursor-pointer text-white/30 border-transparent [&.active]:text-white [&.active]:border-white [&.done]:text-[#16987c] [&.done]:border-[#16987c]/50" data-target="section-3">Type</div>
            <div class="step-tab flex-1 text-center py-2.5 px-1 font-mono text-[9px] font-bold tracking-widest uppercase border-b-2 transition-colors duration-250 cursor-pointer text-white/30 border-transparent [&.active]:text-white [&.active]:border-white [&.done]:text-[#16987c] [&.done]:border-[#16987c]/50" data-target="section-4">Mesures</div>
        </div>
    </div>

    <div id="form-scroll" class="flex-1 overflow-y-auto px-4 pt-5 pb-[calc(90px+env(safe-area-inset-bottom,0px))] scroll-smooth touch-pan-y [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl py-3 px-3.5 mb-3.5">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li class="text-xs text-red-500 mb-0.5">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="analyse-form" method="POST" action="{{ route('mobile.analyse.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="cours_d_eau_id" value="{{ old('cours_d_eau_id', $coursDEauId ?? '') }}">

            @if(request('point_id'))
                <input type="hidden" name="point_id" value="{{ request('point_id') }}">
            @endif

            <input type="hidden" name="latitude" id="f-lat" value="{{ old('latitude', $lat) }}">
            <input type="hidden" name="longitude" id="f-lng" value="{{ old('longitude', $lng) }}">

            <div class="bg-white rounded-[18px] p-[18px] mb-3.5 shadow-[0_2px_12px_rgba(34,42,96,0.06)]" id="section-1">
                <div class="flex items-center gap-2.5 mb-3.5">
                    <div class="w-[26px] h-[26px] rounded-full bg-[#222a60] text-white font-mono text-[11px] font-bold flex items-center justify-center shrink-0">1</div>
                    <div class="font-mono text-[10px] font-bold tracking-[0.12em] uppercase text-slate-500">Localisation du point</div>
                </div>

                <div id="mini-map" class="w-full h-[180px] rounded-xl overflow-hidden bg-slate-200 mb-2.5 relative outline-none [&_.leaflet-container]:outline-none [&_.leaflet-control-attribution]:hidden [&_.leaflet-control-zoom]:hidden">
                    <button type="button" id="gps-btn" title="Ma position" class="absolute bottom-2.5 right-2.5 z-[500] w-9 h-9 bg-white rounded-lg flex items-center justify-center shadow-[0_2px_8px_rgba(0,0,0,0.15)] cursor-pointer text-[#222a60] outline-none border-none">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="3"/>
                            <path stroke-linecap="round" d="M12 2v3M12 19v3M2 12h3M19 12h3"/>
                            <circle cx="12" cy="12" r="7" stroke-dasharray="3 2"/>
                        </svg>
                    </button>
                </div>

                <div class="flex items-center justify-between py-2.5 px-3 bg-slate-50 rounded-lg border border-slate-100">
                    <div class="flex items-center gap-[7px]">
                        <div class="w-[7px] h-[7px] rounded-full bg-[#222a60] shrink-0"></div>
                        <div class="font-mono text-[11px] text-slate-600" id="coords-display">
                            {{ $lat ? number_format($lat, 4).'° N · '.number_format($lng, 4).'° E' : 'Aucune position' }}
                        </div>
                    </div>
                    <div class="text-xs font-semibold text-[#222a60]">{{ $lat ? 'Point placé' : '—' }}</div>
                </div>
            </div>

            <div class="bg-white rounded-[18px] p-[18px] mb-3.5 shadow-[0_2px_12px_rgba(34,42,96,0.06)]" id="section-2">
                <div class="flex items-center gap-2.5 mb-3.5">
                    <div class="w-[26px] h-[26px] rounded-full bg-[#222a60] text-white font-mono text-[11px] font-bold flex items-center justify-center shrink-0">2</div>
                    <div class="font-mono text-[10px] font-bold tracking-[0.12em] uppercase text-slate-500">Photo du prélèvement</div>
                </div>

                <input type="file" name="image" id="file-upload" class="hidden" accept="image/*">

                <div class="grid grid-cols-3 gap-2.5 mb-3">
                    <div id="photo-thumb" class="hidden aspect-square rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 items-center justify-center relative overflow-hidden">
                        <img id="photo-preview" src="" alt="Aperçu" class="w-full h-full object-cover">
                        <button type="button" id="photo-remove" class="absolute top-1.5 right-1.5 w-5 h-5 rounded-full bg-black/55 text-white text-xs flex items-center justify-center cursor-pointer border-none outline-none">✕</button>
                    </div>

                    <div id="ph-camera" class="aspect-square rounded-xl border-2 border-dashed border-slate-300 flex flex-col items-center justify-center gap-1 text-slate-400 text-[11px] font-medium cursor-pointer bg-slate-50 transition-colors active:border-[#222a60] active:bg-blue-50">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <circle cx="12" cy="13" r="3" stroke-linecap="round"/>
                        </svg>
                        Ajouter
                    </div>
                </div>

                <textarea name="note" rows="3" placeholder="Note optionnelle : conditions météo, aspect de l'eau, observations…"
                          class="w-full p-3 rounded-xl border-[1.5px] border-slate-200 bg-slate-50 font-grotesk text-[13px] text-slate-800 outline-none resize-none min-h-[80px] leading-relaxed placeholder:text-slate-400 focus:border-[#222a60] focus:bg-white transition-colors">{{ old('note') }}</textarea>
            </div>

            <div class="bg-white rounded-[18px] p-[18px] mb-3.5 shadow-[0_2px_12px_rgba(34,42,96,0.06)]" id="section-3">
                <div class="flex items-center gap-2.5 mb-3.5">
                    <div class="w-[26px] h-[26px] rounded-full bg-[#222a60] text-white font-mono text-[11px] font-bold flex items-center justify-center shrink-0">3</div>
                    <div class="font-mono text-[10px] font-bold tracking-[0.12em] uppercase text-slate-500">Type d'analyse</div>
                </div>
                <input type="hidden" name="type" id="f-type" value="{{ old('type', 'bandelette') }}">

                <div class="grid grid-cols-2 gap-2.5 mb-2.5">
                    <div class="type-card group rounded-[14px] border-[1.5px] border-slate-200 p-3.5 cursor-pointer transition-colors bg-[#fafafa] relative [&.selected]:border-[#222a60] [&.selected]:bg-blue-50 {{ old('type', 'bandelette') === 'bandelette' ? 'selected' : '' }}" data-type="bandelette">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center mb-2 bg-blue-50 text-[#222a60]">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="9" y="2" width="6" height="20" rx="2"/>
                                <path stroke-linecap="round" d="M9 8h6M9 12h6M9 16h4"/>
                            </svg>
                        </div>
                        <div class="text-[13px] font-bold text-slate-800 mb-[3px]">Bandelette JBL</div>
                        <div class="text-[10px] text-slate-400 leading-snug mb-2.5">6 paramètres : nitrates, nitrites, pH, chlore...</div>
                        <div class="w-[18px] h-[18px] rounded-full border-2 border-slate-300 flex items-center justify-center transition-colors group-[.selected]:border-[#222a60]">
                            <div class="w-2 h-2 rounded-full bg-[#222a60] opacity-0 transition-opacity group-[.selected]:opacity-100"></div>
                        </div>
                    </div>

                    <div class="type-card group rounded-[14px] border-[1.5px] border-slate-200 p-3.5 cursor-pointer transition-colors bg-[#fafafa] relative [&.selected]:border-[#222a60] [&.selected]:bg-blue-50 {{ old('type') === 'photometre' ? 'selected' : '' }}" data-type="photometre">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center mb-2 bg-indigo-50 text-indigo-700">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="4"/>
                                <path stroke-linecap="round" d="M12 2v2M12 20v2M2 12h2M20 12h2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
                            </svg>
                        </div>
                        <div class="text-[13px] font-bold text-slate-800 mb-[3px]">Photomètre</div>
                        <div class="text-[10px] text-slate-400 leading-snug mb-2.5">3 paramètres : phosphate, nitrate, ammoniac</div>
                        <div class="w-[18px] h-[18px] rounded-full border-2 border-slate-300 flex items-center justify-center transition-colors group-[.selected]:border-[#222a60]">
                            <div class="w-2 h-2 rounded-full bg-[#222a60] opacity-0 transition-opacity group-[.selected]:opacity-100"></div>
                        </div>
                    </div>
                </div>

                <div class="type-card group rounded-[14px] border-[1.5px] border-slate-200 py-3.5 px-4 cursor-pointer transition-colors bg-[#fafafa] flex items-center gap-3 [&.selected]:border-[#222a60] [&.selected]:bg-blue-50 {{ old('type') === 'les_deux' ? 'selected' : '' }}" data-type="les_deux">
                    <div class="w-[34px] h-[34px] rounded-lg bg-slate-50 border-[1.5px] border-slate-200 flex items-center justify-center text-slate-400 shrink-0">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <strong class="text-[13px] text-slate-800 block">Les deux</strong>
                        <span class="text-[11px] text-slate-400">Bandelette + Photomètre · 9 paramètres</span>
                    </div>
                    <div class="w-[18px] h-[18px] rounded-full border-2 border-slate-300 flex items-center justify-center transition-colors group-[.selected]:border-[#222a60]">
                        <div class="w-2 h-2 rounded-full bg-[#222a60] opacity-0 transition-opacity group-[.selected]:opacity-100"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[18px] p-[18px] mb-3.5 shadow-[0_2px_12px_rgba(34,42,96,0.06)]" id="section-4">
                <div class="flex items-center gap-2.5 mb-3.5">
                    <div class="w-[26px] h-[26px] rounded-full bg-[#222a60] text-white font-mono text-[11px] font-bold flex items-center justify-center shrink-0">4</div>
                    <div class="font-mono text-[10px] font-bold tracking-[0.12em] uppercase text-slate-500">Saisie des mesures</div>
                </div>

                <div id="fields-bandelette">
                    <div class="flex items-center gap-2 pb-3 mb-1 border-b border-slate-100">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 text-[#222a60] flex items-center justify-center">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="9" y="2" width="6" height="20" rx="2"/>
                            </svg>
                        </div>
                        <span class="text-sm font-bold text-[#222a60]">Bandelette JBL</span>
                    </div>
                    @php
                    $bandFields = [
                        ['key' => 'nitrates',      'label' => 'Nitrates',       'unit' => 'mg/L'],
                        ['key' => 'nitrites',      'label' => 'Nitrites',       'unit' => 'mg/L'],
                        ['key' => 'durete_totale', 'label' => 'Dureté totale',  'unit' => 'mg/L CaCO₃'],
                        ['key' => 'durete_carb',   'label' => 'Dureté carb.',   'unit' => 'mg/L CaCO₃'],
                        ['key' => 'ph',            'label' => 'pH',             'unit' => '—'],
                        ['key' => 'chlore',        'label' => 'Chlore',         'unit' => 'mg/L'],
                    ];
                    @endphp
                    @foreach($bandFields as $f)
                        <div class="flex items-center py-2.5 border-b border-slate-100 gap-2 last:border-b-0 last:pb-0">
                            <div class="flex-1 text-sm text-slate-700">{{ $f['label'] }}</div>
                            <div class="font-mono text-[10px] text-slate-400 min-w-[52px] text-right">{{ $f['unit'] }}</div>
                            <input type="number" step="any"
                                   name="mesures[bandelette][{{ $f['key'] }}]"
                                   class="w-20 py-2 px-2.5 rounded-lg border-[1.5px] border-slate-200 bg-white font-grotesk text-sm font-semibold text-[#222a60] text-center outline-none transition-colors focus:border-[#222a60] placeholder:text-slate-300 placeholder:font-normal"
                                   placeholder="—"
                                   value="{{ old('mesures.bandelette.'.$f['key']) }}">
                        </div>
                    @endforeach
                </div>

                <div id="fields-photometre" class="hidden">
                    <div class="flex items-center gap-2 pb-3 mb-1 mt-4 border-b border-slate-100">
                        <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="4"/>
                                <path stroke-linecap="round" d="M12 2v2M12 20v2M2 12h2M20 12h2"/>
                            </svg>
                        </div>
                        <span class="text-sm font-bold text-[#222a60]">Photomètre</span>
                    </div>
                    @php
                    $photoFields = [
                        ['key' => 'phosphate', 'label' => 'Phosphate', 'unit' => 'mg/L'],
                        ['key' => 'nitrate',   'label' => 'Nitrate',   'unit' => 'mg/L'],
                        ['key' => 'ammoniac',  'label' => 'Ammoniac',  'unit' => 'mg/L'],
                    ];
                    @endphp
                    @foreach($photoFields as $f)
                        <div class="flex items-center py-2.5 border-b border-slate-100 gap-2 last:border-b-0 last:pb-0">
                            <div class="flex-1 text-sm text-slate-700">{{ $f['label'] }}</div>
                            <div class="font-mono text-[10px] text-slate-400 min-w-[52px] text-right">{{ $f['unit'] }}</div>
                            <input type="number" step="any"
                                   name="mesures[photometre][{{ $f['key'] }}]"
                                   class="w-20 py-2 px-2.5 rounded-lg border-[1.5px] border-slate-200 bg-white font-grotesk text-sm font-semibold text-[#222a60] text-center outline-none transition-colors focus:border-[#222a60] placeholder:text-slate-300 placeholder:font-normal"
                                   placeholder="—"
                                   value="{{ old('mesures.photometre.'.$f['key']) }}">
                        </div>
                    @endforeach
                </div>
            </div>
        </form>
    </div>

    <div id="submit-bar" class="fixed bottom-0 inset-x-0 z-30 bg-white/95 backdrop-blur-md pt-3 px-4 pb-[calc(12px+env(safe-area-inset-bottom,0px))] border-t border-slate-100">
        <button type="submit" form="analyse-form" class="w-full p-4 rounded-[14px] bg-gradient-to-br from-[#1a7fc4] to-[#1565c0] text-white font-grotesk text-[15px] font-bold border-none outline-none flex items-center justify-center gap-2 cursor-pointer transition-all active:opacity-90 active:scale-[0.99] shadow-[0_4px_20px_rgba(21,101,192,0.3)]">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            Soumettre l'analyse
        </button>
    </div>

</div>

<script>
    window.initLat = parseFloat("{{ $lat ?? '48.5853' }}");
    window.initLng = parseFloat("{{ $lng ?? '7.7512' }}");
</script>

<script src="{{ asset('js/nouvelle-analyse.js') }}"></script>
@endsection
