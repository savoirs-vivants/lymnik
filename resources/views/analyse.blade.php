@extends('layouts.mobile')
@section('title', 'Nouvelle analyse')

@section('content')
    <div id="page-shell" class="flex flex-col h-[100dvh] w-full bg-slate-50 font-grotesk text-slate-900">

        {{-- HEADER --}}
        <header
            class="bg-gradient-to-br from-[#0d1533] via-[#0f1d42] to-[#1a2a6c]
                   pt-[max(20px,env(safe-area-inset-top))] pb-0 shrink-0 z-10 shadow-sm">
            <div class="max-w-2xl mx-auto w-full px-4 flex items-center justify-center relative pb-3">
                <a href="{{ old('redirect_to', request('redirect_to', '/mobile')) }}" aria-label="Retour à la carte"
                    class="absolute left-4 w-10 h-10 rounded-full bg-white/10 flex items-center justify-center
                      text-white transition-colors hover:bg-white/20 focus-visible:ring-2 focus-visible:ring-white/60
                      focus-visible:outline-none no-underline">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="text-center">
                    <p class="text-white text-[17px] font-bold leading-tight">Nouvelle analyse</p>
                    <p class="text-white/55 text-[11px] mt-0.5 font-mono tracking-wide">
                        {{ now()->translatedFormat('d M Y') }}
                    </p>
                </div>
            </div>
        </header>

        {{-- SCROLL AREA --}}
        <div id="form-scroll"
            class="flex-1 overflow-y-auto overscroll-contain touch-pan-y scroll-smooth
                [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]
                px-4 pt-5 pb-28">

            <div class="max-w-2xl mx-auto w-full flex flex-col gap-4">

                {{-- Erreurs --}}
                @if ($errors->any())
                    <div role="alert" class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex gap-3">
                        <svg width="16" height="16" class="shrink-0 mt-0.5 text-red-500" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <ul class="flex flex-col gap-0.5">
                            @foreach ($errors->all() as $err)
                                <li class="text-[12px] font-medium text-red-600">{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="analyse-form" method="POST" action="{{ route('analyse.store') }}" enctype="multipart/form-data"
                    novalidate>
                    @csrf
                    <input type="hidden" name="cours_d_eau_id" value="{{ old('cours_d_eau_id', $coursDEauId ?? '') }}">
                    <input type="hidden" name="ville" id="f-ville" value="{{ old('ville') }}">
                    <input type="hidden" name="latitude" id="f-lat" value="{{ old('latitude', $lat) }}">
                    <input type="hidden" name="longitude" id="f-lng" value="{{ old('longitude', $lng) }}">
                    @if (request('point_id'))
                        <input type="hidden" name="point_id" value="{{ request('point_id') }}">
                    @endif
                    <input type="hidden" name="redirect_to"
                        value="{{ old('redirect_to', request('redirect_to', route('mobile'))) }}">

                    {{-- SECTION 1 : Localisation --}}
                    <section aria-labelledby="section-1-title"
                        class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_10px_rgba(34,42,96,0.04)] overflow-hidden mb-4">
                        <div class="flex items-center gap-2.5 px-5 pt-4 pb-3 border-b border-slate-100">
                            <span
                                class="w-6 h-6 rounded-full bg-[#222a60] text-white font-mono text-[11px] font-bold
                                     flex items-center justify-center shrink-0"
                                aria-hidden="true">1</span>
                            <h2 id="section-1-title"
                                class="font-mono text-[10px] font-bold tracking-widest uppercase text-slate-500">
                                Localisation du point
                            </h2>
                        </div>

                        <div class="px-5 pb-5 pt-4 flex flex-col gap-3">
                            <div id="mini-map"
                                class="w-full h-[180px] sm:h-[220px] rounded-xl overflow-hidden bg-slate-200 relative
                                    border border-slate-100"
                                role="img" aria-label="Mini-carte de localisation">
                                <button type="button" id="gps-btn" title="Utiliser ma position GPS"
                                    aria-label="Centrer sur ma position"
                                    class="absolute bottom-3 right-3 z-[500] w-10 h-10 bg-white rounded-xl
                                           flex items-center justify-center shadow-md text-[#222a60]
                                           hover:bg-slate-50 border border-slate-100
                                           focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#222a60]/40
                                           transition-colors cursor-pointer">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24" aria-hidden="true">
                                        <circle cx="12" cy="12" r="3" />
                                        <path stroke-linecap="round" d="M12 2v3M12 19v3M2 12h3M19 12h3" />
                                        <circle cx="12" cy="12" r="7" stroke-dasharray="3 2" />
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div
                                    class="flex items-center gap-2.5 py-2.5 px-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <div class="w-2 h-2 rounded-full bg-[#222a60] shrink-0" aria-hidden="true"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-mono text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">
                                            Coordonnées</p>
                                        <p class="font-mono text-[11px] text-slate-700 font-medium truncate"
                                            id="coords-display">
                                            {{ $lat ? number_format($lat, 4) . '° N · ' . number_format($lng, 4) . '° E' : 'Non défini' }}
                                        </p>
                                    </div>
                                    <span class="text-[11px] font-bold text-[#222a60] shrink-0">
                                        {{ $lat ? 'Placé' : '—' }}
                                    </span>
                                </div>
                                <div
                                    class="flex items-center gap-2.5 py-2.5 px-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <div class="w-2 h-2 rounded-full bg-[#16987c] shrink-0" aria-hidden="true"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-mono text-[10px] text-slate-400 uppercase tracking-wide mb-0.5">
                                            Cours d'eau</p>
                                        <p class="font-mono text-[11px] text-slate-700 font-medium truncate"
                                            id="river-display">
                                            @if ($coursDEauId && $nomCoursEau)
                                                {{ $nomCoursEau }}
                                            @else
                                                Recherche…
                                            @endif
                                        </p>
                                    </div>
                                    <span class="text-[11px] font-bold text-[#16987c] shrink-0" id="river-status">
                                        {{ $coursDEauId ? 'Trouvé' : '' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- SECTION 2 : Photo & Notes --}}
                    <section aria-labelledby="section-2-title"
                        class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_10px_rgba(34,42,96,0.04)] overflow-hidden mb-4">
                        <div class="flex items-center gap-2.5 px-5 pt-4 pb-3 border-b border-slate-100">
                            <span
                                class="w-6 h-6 rounded-full bg-[#222a60] text-white font-mono text-[11px] font-bold
                                     flex items-center justify-center shrink-0"
                                aria-hidden="true">2</span>
                            <h2 id="section-2-title"
                                class="font-mono text-[10px] font-bold tracking-widest uppercase text-slate-500">
                                Photo &amp; notes
                            </h2>
                        </div>

                        <div class="px-5 pb-5 pt-4 flex gap-4">
                            {{-- Zone photo --}}
                            <div class="shrink-0">
                                <input type="file" name="image" id="file-upload" class="sr-only" accept="image/*"
                                    aria-label="Ajouter une photo">
                                <label for="file-upload" id="ph-camera"
                                    class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl border-2 border-dashed border-slate-300
                                          hover:border-[#222a60] hover:bg-blue-50 flex flex-col items-center justify-center
                                          gap-1 text-slate-500 cursor-pointer bg-slate-50 transition-colors
                                          focus-within:ring-2 focus-within:ring-[#222a60]/30 select-none">
                                    <svg width="20" height="20" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <circle cx="12" cy="13" r="3" />
                                    </svg>
                                    <span class="text-[10px] font-semibold">Photo</span>
                                </label>
                                <div id="photo-thumb"
                                    class="hidden w-20 h-20 sm:w-24 sm:h-24 rounded-xl bg-slate-100
                                        items-center justify-center relative overflow-hidden border border-slate-200">
                                    <img id="photo-preview" src="" alt="Aperçu photo"
                                        class="w-full h-full object-cover">
                                    <button type="button" id="photo-remove" aria-label="Supprimer la photo"
                                        class="absolute top-1 right-1 w-6 h-6 rounded-full bg-black/65 text-white
                                               text-xs flex items-center justify-center cursor-pointer hover:bg-black
                                               focus-visible:ring-2 focus-visible:ring-white focus-visible:outline-none">
                                        ✕
                                    </button>
                                </div>
                            </div>

                            {{-- Notes --}}
                            <div class="flex-1 min-w-0 flex flex-col">
                                <label for="note-textarea" class="sr-only">Notes (météo, aspect visuel…)</label>
                                <textarea id="note-textarea" name="note" placeholder="Notes : météo, aspect visuel, odeur…" rows="4"
                                    class="flex-1 w-full p-3 rounded-xl border border-slate-200 bg-slate-50
                                             font-grotesk text-[13px] text-slate-800 resize-none
                                             placeholder:text-slate-400
                                             focus:border-[#222a60] focus:bg-white focus:ring-2 focus:ring-[#222a60]/15
                                             focus:outline-none transition-all">{{ old('note') }}</textarea>
                            </div>
                        </div>
                    </section>

                    {{-- SECTION 3 : Type d'analyse --}}
                    <section aria-labelledby="section-3-title"
                        class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_10px_rgba(34,42,96,0.04)] overflow-hidden mb-4">
                        <div class="flex items-center gap-2.5 px-5 pt-4 pb-3 border-b border-slate-100">
                            <span
                                class="w-6 h-6 rounded-full bg-[#222a60] text-white font-mono text-[11px] font-bold
                                     flex items-center justify-center shrink-0"
                                aria-hidden="true">3</span>
                            <h2 id="section-3-title"
                                class="font-mono text-[10px] font-bold tracking-widest uppercase text-slate-500">
                                Type d'analyse
                            </h2>
                        </div>

                        <div class="px-5 pb-5 pt-4" role="radiogroup" aria-labelledby="section-3-title">
                            <input type="hidden" name="type" id="f-type"
                                value="{{ old('type', 'bandelette') }}">

                            <div class="grid grid-cols-2 gap-3 mb-3">
                                @foreach ([['value' => 'bandelette', 'label' => 'Bandelette', 'sub' => '6 paramètres', 'color' => 'bg-blue-50 text-[#222a60]', 'icon' => '<rect x="9" y="2" width="6" height="20" rx="2"/><path stroke-linecap="round" d="M9 8h6M9 12h6M9 16h4"/>'], ['value' => 'photometre', 'label' => 'Photomètre', 'sub' => '3 paramètres', 'color' => 'bg-indigo-50 text-indigo-700', 'icon' => '<circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2M12 20v2M2 12h2M20 12h2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>']] as $card)
                                    @php $sel = old('type', 'bandelette') === $card['value']; @endphp
                                    <button type="button" role="radio" aria-checked="{{ $sel ? 'true' : 'false' }}"
                                        data-type="{{ $card['value'] }}"
                                        class="type-card relative flex flex-col items-start p-4 rounded-xl border-2 cursor-pointer
                                               text-left transition-all focus-visible:outline-none focus-visible:ring-2
                                               focus-visible:ring-[#222a60]/40
                                               {{ $sel ? 'border-[#222a60] bg-blue-50/40' : 'border-slate-200 bg-slate-50 hover:border-slate-300 hover:bg-white' }}">
                                        <div
                                            class="w-9 h-9 rounded-xl flex items-center justify-center mb-3 {{ $card['color'] }}">
                                            <svg width="16" height="16" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                                {!! $card['icon'] !!}
                                            </svg>
                                        </div>
                                        <span
                                            class="text-[13px] font-bold text-slate-800 block">{{ $card['label'] }}</span>
                                        <span class="text-[10px] text-slate-500 mt-0.5">{{ $card['sub'] }}</span>
                                        <div
                                            class="absolute top-3.5 right-3.5 w-4 h-4 rounded-full border-2 flex items-center justify-center
                                                {{ $sel ? 'border-[#222a60]' : 'border-slate-300' }}">
                                            <div
                                                class="w-2 h-2 rounded-full bg-[#222a60] transition-opacity {{ $sel ? 'opacity-100' : 'opacity-0' }}">
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>

                            @php $selLesDeux = old('type') === 'les_deux'; @endphp
                            <button type="button" role="radio" aria-checked="{{ $selLesDeux ? 'true' : 'false' }}"
                                data-type="les_deux"
                                class="type-card w-full flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer
                                       text-left transition-all focus-visible:outline-none focus-visible:ring-2
                                       focus-visible:ring-[#222a60]/40
                                       {{ $selLesDeux ? 'border-[#222a60] bg-blue-50/40' : 'border-slate-200 bg-slate-50 hover:border-slate-300 hover:bg-white' }}">
                                <div
                                    class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center shrink-0">
                                    <svg width="16" height="16" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <span class="text-[13px] font-bold text-slate-800 block">Les deux</span>
                                    <span class="text-[10px] text-slate-500">9 paramètres complets</span>
                                </div>
                                <div
                                    class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0
                                        {{ $selLesDeux ? 'border-[#222a60]' : 'border-slate-300' }}">
                                    <div
                                        class="w-2 h-2 rounded-full bg-[#222a60] transition-opacity {{ $selLesDeux ? 'opacity-100' : 'opacity-0' }}">
                                    </div>
                                </div>
                            </button>
                        </div>
                    </section>

                    {{-- SECTION 4 : Mesures --}}
                    <section aria-labelledby="section-4-title"
                        class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_10px_rgba(34,42,96,0.04)] overflow-hidden">
                        <div class="flex items-center gap-2.5 px-5 pt-4 pb-3 border-b border-slate-100">
                            <span
                                class="w-6 h-6 rounded-full bg-[#222a60] text-white font-mono text-[11px] font-bold
                                     flex items-center justify-center shrink-0"
                                aria-hidden="true">4</span>
                            <h2 id="section-4-title"
                                class="font-mono text-[10px] font-bold tracking-widest uppercase text-slate-500">
                                Saisie des mesures
                            </h2>
                        </div>

                        <div class="px-5 pb-5 pt-4 flex flex-col gap-6">

                            {{-- Bandelette --}}
                            <fieldset id="fields-bandelette">
                                <legend class="flex items-center gap-2.5 pb-3 mb-1 border-b border-slate-100 w-full">
                                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-[#222a60] flex items-center justify-center shrink-0"
                                        aria-hidden="true">
                                        <svg width="13" height="13" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <rect x="9" y="2" width="6" height="20" rx="2" />
                                        </svg>
                                    </div>
                                    <span class="text-[13px] font-bold text-[#222a60]">Bandelette JBL</span>
                                </legend>

                                @php
                                    $bandFields = [
                                        ['key' => 'nitrates', 'label' => 'Nitrates', 'unit' => 'mg/L'],
                                        ['key' => 'nitrites', 'label' => 'Nitrites', 'unit' => 'mg/L'],
                                        ['key' => 'durete_totale', 'label' => 'Dureté totale', 'unit' => 'mg/L CaCO₃'],
                                        ['key' => 'durete_carb', 'label' => 'Dureté carb.', 'unit' => 'mg/L CaCO₃'],
                                        ['key' => 'ph', 'label' => 'pH', 'unit' => '—'],
                                        ['key' => 'chlore', 'label' => 'Chlore', 'unit' => 'mg/L'],
                                    ];
                                @endphp

                                <div class="flex flex-col divide-y divide-slate-50">
                                    @foreach ($bandFields as $f)
                                        @php $inputId = 'band-' . $f['key']; @endphp
                                        <div class="flex items-center gap-3 py-2.5 first:pt-3">
                                            <label for="{{ $inputId }}"
                                                class="flex-1 text-[13px] font-medium text-slate-700 cursor-pointer">
                                                {{ $f['label'] }}
                                            </label>
                                            <span
                                                class="font-mono text-[10px] text-slate-400 text-right shrink-0 hidden sm:block">
                                                {{ $f['unit'] }}
                                            </span>
                                            <input id="{{ $inputId }}" type="number" step="any"
                                                min="0" name="mesures[bandelette][{{ $f['key'] }}]"
                                                aria-label="{{ $f['label'] }} en {{ $f['unit'] }}"
                                                class="w-24 h-10 px-3 rounded-xl border border-slate-200 bg-slate-50
                                                      font-grotesk text-[13px] font-bold text-[#222a60] text-center
                                                      focus:border-[#222a60] focus:bg-white focus:ring-2 focus:ring-[#222a60]/15
                                                      focus:outline-none placeholder:text-slate-300 placeholder:font-normal
                                                      transition-all"
                                                placeholder="—" value="{{ old('mesures.bandelette.' . $f['key']) }}">
                                        </div>
                                    @endforeach
                                </div>
                            </fieldset>

                            {{-- Photomètre --}}
                            <fieldset id="fields-photometre" class="hidden">
                                <legend class="flex items-center gap-2.5 pb-3 mb-1 border-b border-slate-100 w-full">
                                    <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center shrink-0"
                                        aria-hidden="true">
                                        <svg width="13" height="13" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="4" />
                                            <path stroke-linecap="round" d="M12 2v2M12 20v2M2 12h2M20 12h2" />
                                        </svg>
                                    </div>
                                    <span class="text-[13px] font-bold text-[#222a60]">Photomètre</span>
                                </legend>

                                @php
                                    $photoFields = [
                                        ['key' => 'phosphate', 'label' => 'Phosphate', 'unit' => 'mg/L'],
                                        ['key' => 'nitrate', 'label' => 'Nitrate', 'unit' => 'mg/L'],
                                        ['key' => 'ammoniaque', 'label' => 'Ammoniaque', 'unit' => 'mg/L'],
                                    ];
                                @endphp

                                <div class="flex flex-col divide-y divide-slate-50">
                                    @foreach ($photoFields as $f)
                                        @php $inputId = 'photo-' . $f['key']; @endphp
                                        <div class="flex items-center gap-3 py-2.5 first:pt-3">
                                            <label for="{{ $inputId }}"
                                                class="flex-1 text-[13px] font-medium text-slate-700 cursor-pointer">
                                                {{ $f['label'] }}
                                            </label>
                                            <span
                                                class="font-mono text-[10px] text-slate-400 text-right shrink-0 hidden sm:block">
                                                {{ $f['unit'] }}
                                            </span>
                                            <input id="{{ $inputId }}" type="number" step="any"
                                                min="0" name="mesures[photometre][{{ $f['key'] }}]"
                                                aria-label="{{ $f['label'] }} en {{ $f['unit'] }}"
                                                class="w-24 h-10 px-3 rounded-xl border border-slate-200 bg-slate-50
                                                      font-grotesk text-[13px] font-bold text-[#222a60] text-center
                                                      focus:border-[#222a60] focus:bg-white focus:ring-2 focus:ring-[#222a60]/15
                                                      focus:outline-none placeholder:text-slate-300 placeholder:font-normal
                                                      transition-all"
                                                placeholder="—" value="{{ old('mesures.photometre.' . $f['key']) }}">
                                        </div>
                                    @endforeach
                                </div>
                            </fieldset>

                        </div>
                    </section>

                </form>
            </div>
        </div>

        {{-- BARRE DE SOUMISSION --}}
        <div
            class="shrink-0 bg-white/95 backdrop-blur-md border-t border-slate-100
                pt-3 pb-[calc(12px+env(safe-area-inset-bottom,0px))]
                shadow-[0_-4px_20px_rgba(34,42,96,0.06)]">
            <div class="max-w-2xl mx-auto px-4">
                <button type="submit" form="analyse-form"
                    class="w-full h-12 rounded-xl bg-gradient-to-r from-[#1565c0] to-[#1a7fc4]
                           hover:from-[#1251a3] hover:to-[#1565c0]
                           text-white font-bold text-[14px] border-none cursor-pointer
                           flex items-center justify-center gap-2
                           shadow-[0_4px_14px_rgba(21,101,192,0.35)]
                           active:scale-[0.99] transition-all focus-visible:outline-none
                           focus-visible:ring-2 focus-visible:ring-[#1565c0]/50 focus-visible:ring-offset-2">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Enregistrer la mesure
                </button>
            </div>
        </div>

    </div>

    <script>
        window.initLat = parseFloat("{{ $lat ?? '48.5853' }}");
        window.initLng = parseFloat("{{ $lng ?? '7.7512' }}");
        window.initCoursDEauId = {{ $coursDEauId ? (int) $coursDEauId : 'null' }};
        window.initNomCoursEau = @json($nomCoursEau ?? null);
        window.nearestRiverUrl = "{{ route('cours-d-eau.nearest') }}";
    </script>

    <script src="{{ asset('js/nouvelle-analyse.js') }}"></script>
@endsection
