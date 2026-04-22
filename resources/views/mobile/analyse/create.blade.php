@extends('layouts.mobile')
@section('title', 'Création de point')

@section('content')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    *, *::before, *::after { box-sizing: border-box; }
    html, body { height: 100%; overflow: hidden; background: #f1f5f9; }
    body { font-family: 'Space Grotesk', sans-serif; }

    /* ── Layout ── */
    #page-shell {
        display: flex;
        flex-direction: column;
        height: 100dvh;
        overflow: hidden;
    }

    /* ── Header ── */
    #page-header {
        background: linear-gradient(160deg, #0d1533 0%, #0f1d42 50%, #1a2a6c 100%);
        padding-top: max(48px, env(safe-area-inset-top));
        padding-bottom: 0;
        flex-shrink: 0;
        position: relative;
        z-index: 10;
    }
    .header-inner {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 16px 12px;
        position: relative;
    }
    .back-btn {
        position: absolute;
        left: 16px;
        width: 34px; height: 34px;
        border-radius: 50%;
        background: rgba(255,255,255,0.12);
        border: none; outline: none;
        display: flex; align-items: center; justify-content: center;
        color: white; cursor: pointer;
        transition: background 0.15s;
        text-decoration: none;
    }
    .back-btn:active { background: rgba(255,255,255,0.2); }
    .header-title { color: white; font-size: 17px; font-weight: 700; text-align: center; }
    .header-sub   { color: rgba(255,255,255,0.55); font-size: 12px; text-align: center; margin-top: 1px; font-family: 'Space Mono', monospace; }

    /* ── Step progress ── */
    #step-bar {
        display: flex;
        border-top: 1px solid rgba(255,255,255,0.08);
        margin-top: 10px;
    }
    .step-tab {
        flex: 1;
        text-align: center;
        padding: 10px 4px;
        font-family: 'Space Mono', monospace;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.3);
        border-bottom: 2px solid transparent;
        transition: color 0.25s, border-color 0.25s;
        cursor: pointer;
    }
    .step-tab.active {
        color: white;
        border-bottom-color: white;
    }
    .step-tab.done {
        color: rgba(22,152,124,0.9);
        border-bottom-color: rgba(22,152,124,0.5);
    }

    /* ── Scrollable content ── */
    #form-scroll {
        flex: 1;
        overflow-y: auto;
        padding: 20px 16px calc(90px + env(safe-area-inset-bottom, 0px));
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }
    #form-scroll::-webkit-scrollbar { display: none; }

    /* ── Section card ── */
    .section-card {
        background: white;
        border-radius: 18px;
        padding: 18px;
        margin-bottom: 14px;
        box-shadow: 0 2px 12px rgba(34,42,96,0.06);
    }
    .section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
    }
    .section-num {
        width: 26px; height: 26px;
        border-radius: 50%;
        background: #222a60;
        color: white;
        font-family: 'Space Mono', monospace;
        font-size: 11px;
        font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .section-num.done { background: #16987c; }
    .section-title {
        font-family: 'Space Mono', monospace;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #64748b;
    }

    /* ── Mini map ── */
    #mini-map {
        width: 100%;
        height: 180px;
        border-radius: 12px;
        overflow: hidden;
        background: #e2e8f0;
        margin-bottom: 10px;
        position: relative;
    }
    .leaflet-container { outline: none !important; }
    .leaflet-control-attribution, .leaflet-control-zoom { display: none !important; }

    /* GPS button on mini-map */
    #gps-btn {
        position: absolute;
        bottom: 10px; right: 10px;
        z-index: 500;
        width: 36px; height: 36px;
        background: white;
        border: none; outline: none;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        cursor: pointer;
        color: #222a60;
    }

    .coords-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #f1f5f9;
    }
    .coords-left { display: flex; align-items: center; gap: 7px; }
    .coords-dot  { width: 7px; height: 7px; border-radius: 50%; background: #222a60; flex-shrink: 0; }
    .coords-text { font-family: 'Space Mono', monospace; font-size: 11px; color: #475569; }
    .coords-status { font-size: 12px; font-weight: 600; color: #222a60; }

    /* ── Select ── */
    .field-select {
        width: 100%;
        padding: 11px 14px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        font-family: 'Space Grotesk', sans-serif;
        font-size: 14px;
        color: #1e293b;
        outline: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
    }
    .field-select:focus { border-color: #222a60; background-color: white; }

    /* ── Photo picker ── */
    .photo-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 12px;
    }
    .photo-thumb {
        aspect-ratio: 1;
        border-radius: 12px;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        display: flex; align-items: center; justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .photo-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .photo-remove {
        position: absolute;
        top: 6px; right: 6px;
        width: 20px; height: 20px;
        border-radius: 50%;
        background: rgba(0,0,0,0.55);
        color: white;
        font-size: 12px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        border: none; outline: none;
    }
    .photo-placeholder {
        aspect-ratio: 1;
        border-radius: 12px;
        border: 2px dashed #cbd5e1;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 4px;
        color: #94a3b8;
        font-size: 11px;
        font-weight: 500;
        cursor: pointer;
        background: #f8fafc;
        transition: border-color 0.15s, background 0.15s;
    }
    .photo-placeholder:active { border-color: #222a60; background: #eff6ff; }

    /* Hidden real file input */
    .file-input-hidden { display: none; }

    /* Textarea */
    .field-textarea {
        width: 100%;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        font-family: 'Space Grotesk', sans-serif;
        font-size: 13px;
        color: #1e293b;
        outline: none;
        resize: none;
        min-height: 80px;
        line-height: 1.5;
    }
    .field-textarea::placeholder { color: #94a3b8; }
    .field-textarea:focus { border-color: #222a60; background: white; }

    /* ── Type cards ── */
    .type-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px; }
    .type-card {
        border-radius: 14px;
        border: 1.5px solid #e2e8f0;
        padding: 14px;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        position: relative;
        background: #fafafa;
    }
    .type-card.selected { border-color: #222a60; background: #eff6ff; }
    .type-card-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 8px;
    }
    .type-card-icon.blue  { background: #eff6ff; color: #222a60; }
    .type-card-icon.indigo { background: #f5f3ff; color: #6d28d9; }
    .type-card-name { font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 3px; }
    .type-card-desc { font-size: 10px; color: #94a3b8; line-height: 1.4; margin-bottom: 10px; }
    .type-radio {
        width: 18px; height: 18px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: flex; align-items: center; justify-content: center;
        transition: border-color 0.2s;
    }
    .type-radio .dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: #222a60;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .type-card.selected .type-radio { border-color: #222a60; }
    .type-card.selected .type-radio .dot { opacity: 1; }

    .type-card-full {
        border-radius: 14px;
        border: 1.5px solid #e2e8f0;
        padding: 14px 16px;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        background: #fafafa;
        display: flex; align-items: center; gap: 12px;
    }
    .type-card-full.selected { border-color: #222a60; background: #eff6ff; }
    .type-card-full .star-icon {
        width: 34px; height: 34px;
        border-radius: 10px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        display: flex; align-items: center; justify-content: center;
        color: #94a3b8; flex-shrink: 0;
    }
    .type-card-full .info { flex: 1; }
    .type-card-full .info strong { font-size: 13px; color: #1e293b; display: block; }
    .type-card-full .info span   { font-size: 11px; color: #94a3b8; }

    /* ── Mesures inputs ── */
    .mesure-row {
        display: flex;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
        gap: 8px;
    }
    .mesure-row:last-child { border-bottom: none; padding-bottom: 0; }
    .mesure-label { flex: 1; font-size: 14px; color: #334155; }
    .mesure-unit  { font-family: 'Space Mono', monospace; font-size: 10px; color: #94a3b8; min-width: 52px; text-align: right; }
    .mesure-input {
        width: 80px;
        padding: 8px 10px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        background: white;
        font-family: 'Space Grotesk', sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: #222a60;
        text-align: center;
        outline: none;
        transition: border-color 0.15s;
    }
    .mesure-input:focus { border-color: #222a60; }
    .mesure-input::placeholder { color: #cbd5e1; font-weight: 400; }

    .mesures-section-title {
        display: flex; align-items: center; gap: 8px;
        padding-bottom: 12px; margin-bottom: 4px;
        border-bottom: 1px solid #f1f5f9;
    }
    .mesures-section-title .icon {
        width: 28px; height: 28px; border-radius: 8px;
        background: #eff6ff; color: #222a60;
        display: flex; align-items: center; justify-content: center;
    }
    .mesures-section-title span { font-size: 14px; font-weight: 700; color: #222a60; }

    /* ── Error messages ── */
    .error-banner {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 14px;
    }
    .error-banner li { font-size: 12px; color: #ef4444; margin-bottom: 2px; }

    /* ── Fixed bottom button ── */
    #submit-bar {
        position: fixed;
        bottom: 0; left: 0; right: 0;
        z-index: 30;
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(12px);
        padding: 12px 16px calc(12px + env(safe-area-inset-bottom, 0px));
        border-top: 1px solid #f1f5f9;
    }
    .submit-btn {
        width: 100%;
        padding: 16px;
        border-radius: 14px;
        background: linear-gradient(135deg, #1a7fc4 0%, #1565c0 100%);
        color: white;
        font-family: 'Space Grotesk', sans-serif;
        font-size: 15px;
        font-weight: 700;
        border: none; outline: none;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        cursor: pointer;
        transition: opacity 0.2s, transform 0.15s;
        box-shadow: 0 4px 20px rgba(21,101,192,0.3);
    }
    .submit-btn:active { opacity: 0.9; transform: scale(0.99); }
</style>
@endpush

<div id="page-shell">

    <div id="page-header">
        <div class="header-inner">
            <a href="{{ route('index_mobile') }}" class="back-btn" aria-label="Retour">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <div class="header-title">Nouvelle analyse</div>
                <div class="header-sub" id="header-subtitle">
                </div>
            </div>
        </div>

        {{-- Step progress bar --}}
        <div id="step-bar">
            <div class="step-tab active" data-target="section-1">Localisation</div>
            <div class="step-tab"        data-target="section-2">Photo</div>
            <div class="step-tab"        data-target="section-3">Type</div>
            <div class="step-tab"        data-target="section-4">Mesures</div>
        </div>
    </div>

    <div id="form-scroll">

        @if ($errors->any())
            <div class="error-banner">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="analyse-form"
              method="POST"
              action="{{ route('mobile.analyse.store') }}"
              enctype="multipart/form-data">
            @csrf
@if(request('point_id'))
        <input type="hidden" name="point_id" value="{{ request('point_id') }}">
    @endif
            <input type="hidden" name="latitude"  id="f-lat" value="{{ old('latitude',  $lat) }}">
            <input type="hidden" name="longitude" id="f-lng" value="{{ old('longitude', $lng) }}">

            <div class="section-card" id="section-1">
                <div class="section-header">
                    <div class="section-num" id="num-1">1</div>
                    <div class="section-title">Localisation du point</div>
                </div>

                <div id="mini-map">
                    <button type="button" id="gps-btn" title="Ma position">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="3"/>
                            <path stroke-linecap="round" d="M12 2v3M12 19v3M2 12h3M19 12h3"/>
                            <circle cx="12" cy="12" r="7" stroke-dasharray="3 2"/>
                        </svg>
                    </button>
                </div>

                <div class="coords-row">
                    <div class="coords-left">
                        <div class="coords-dot"></div>
                        <div class="coords-text" id="coords-display">
                            {{ $lat ? number_format($lat, 4).'° N · '.number_format($lng, 4).'° E' : 'Aucune position' }}
                        </div>
                    </div>
                    <div class="coords-status">{{ $lat ? 'Point placé' : '—' }}</div>
                </div>
            </div>

            <div class="section-card" id="section-2">
                <div class="section-header">
                    <div class="section-num" id="num-2">2</div>
                    <div class="section-title">Photo du prélèvement</div>
                </div>

                <input type="file" name="image" id="file-camera"  class="file-input-hidden" accept="image/*" capture="environment">
                <input type="file" name="image" id="file-gallery" class="file-input-hidden" accept="image/*">

                <div class="photo-grid">
                    <div class="photo-thumb" id="photo-thumb" style="display:none;">
                        <img id="photo-preview" src="" alt="Aperçu">
                        <button type="button" class="photo-remove" id="photo-remove">✕</button>
                    </div>
                    <div class="photo-placeholder" id="ph-camera" onclick="document.getElementById('file-camera').click()">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <circle cx="12" cy="13" r="3" stroke-linecap="round"/>
                        </svg>
                        Ajouter
                    </div>
                </div>

                <textarea name="note" class="field-textarea"
                          placeholder="Note optionnelle : conditions météo, aspect de l'eau, observations…"
                          rows="3">{{ old('note') }}</textarea>
            </div>

            <div class="section-card" id="section-3">
                <div class="section-header">
                    <div class="section-num" id="num-3">3</div>
                    <div class="section-title">Type d'analyse</div>
                </div>
                <input type="hidden" name="type" id="f-type" value="{{ old('type', 'bandelette') }}">

                <div class="type-grid">
                    <div class="type-card {{ old('type', 'bandelette') === 'bandelette' ? 'selected' : '' }}"
                         data-type="bandelette">
                        <div class="type-card-icon blue">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="9" y="2" width="6" height="20" rx="2"/>
                                <path stroke-linecap="round" d="M9 8h6M9 12h6M9 16h4"/>
                            </svg>
                        </div>
                        <div class="type-card-name">Bandelette JBL</div>
                        <div class="type-card-desc">6 paramètres : nitrates, nitrites, pH, chlore, dureté</div>
                        <div class="type-radio"><div class="dot"></div></div>
                    </div>

                    <div class="type-card {{ old('type') === 'photometre' ? 'selected' : '' }}"
                         data-type="photometre">
                        <div class="type-card-icon indigo">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="4"/>
                                <path stroke-linecap="round" d="M12 2v2M12 20v2M2 12h2M20 12h2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
                            </svg>
                        </div>
                        <div class="type-card-name">Photomètre</div>
                        <div class="type-card-desc">3 paramètres : phosphate, nitrate, ammonium</div>
                        <div class="type-radio"><div class="dot"></div></div>
                    </div>
                </div>

                <div class="type-card-full {{ old('type') === 'les_deux' ? 'selected' : '' }}"
                     data-type="les_deux">
                    <div class="star-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <div class="info">
                        <strong>Les deux</strong>
                        <span>Bandelette + Photomètre · 9 paramètres</span>
                    </div>
                    <div class="type-radio"><div class="dot"></div></div>
                </div>
            </div>

            <div class="section-card" id="section-4">
                <div class="section-header">
                    <div class="section-num" id="num-4">4</div>
                    <div class="section-title">Saisie des mesures</div>
                </div>

                <div id="fields-bandelette">
                    <div class="mesures-section-title">
                        <div class="icon">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="9" y="2" width="6" height="20" rx="2"/>
                            </svg>
                        </div>
                        <span>Bandelette JBL</span>
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
                        <div class="mesure-row">
                            <div class="mesure-label">{{ $f['label'] }}</div>
                            <div class="mesure-unit">{{ $f['unit'] }}</div>
                            <input type="number" step="any"
                                   name="mesures[bandelette][{{ $f['key'] }}]"
                                   class="mesure-input"
                                   placeholder="—"
                                   value="{{ old('mesures.bandelette.'.$f['key']) }}">
                        </div>
                    @endforeach
                </div>

                <div id="fields-photometre" style="display:none;">
                    <div class="mesures-section-title" style="margin-top:16px;">
                        <div class="icon" style="background:#f5f3ff;color:#6d28d9;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="4"/>
                                <path stroke-linecap="round" d="M12 2v2M12 20v2M2 12h2M20 12h2"/>
                            </svg>
                        </div>
                        <span>Photomètre</span>
                    </div>
                    @php
                    $photoFields = [
                        ['key' => 'phosphate', 'label' => 'Phosphate', 'unit' => 'mg/L'],
                        ['key' => 'nitrate',   'label' => 'Nitrate',   'unit' => 'mg/L'],
                        ['key' => 'ammonium',  'label' => 'Ammonium',  'unit' => 'mg/L'],
                    ];
                    @endphp
                    @foreach($photoFields as $f)
                        <div class="mesure-row">
                            <div class="mesure-label">{{ $f['label'] }}</div>
                            <div class="mesure-unit">{{ $f['unit'] }}</div>
                            <input type="number" step="any"
                                   name="mesures[photometre][{{ $f['key'] }}]"
                                   class="mesure-input"
                                   placeholder="—"
                                   value="{{ old('mesures.photometre.'.$f['key']) }}">
                        </div>
                    @endforeach
                </div>
            </div>

        </form>
    </div>

    <div id="submit-bar">
        <button type="submit" form="analyse-form" class="submit-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            Soumettre l'analyse
        </button>
    </div>

</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {

    let lat = parseFloat('{{ $lat ?? 48.5853 }}') || 48.5853;
    let lng = parseFloat('{{ $lng ?? 7.7512  }}') || 7.7512;

    const miniMap = L.map('mini-map', {
        center: [lat, lng],
        zoom: 15,
        zoomControl: false,
        attributionControl: false,
        dragging: true,
        scrollWheelZoom: false,
    });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
    }).addTo(miniMap);

    const markerIcon = L.divIcon({
        className: '',
        html: `<div style="width:22px;height:22px;border-radius:50%;background:white;border:3px solid #222a60;box-shadow:0 2px 8px rgba(34,42,96,0.3);"></div>`,
        iconSize: [22, 22],
        iconAnchor: [11, 11],
    });

    const marker = L.marker([lat, lng], { icon: markerIcon, draggable: true }).addTo(miniMap);

    function updateCoords(newLat, newLng) {
        lat = newLat; lng = newLng;
        document.getElementById('f-lat').value = lat.toFixed(6);
        document.getElementById('f-lng').value = lng.toFixed(6);
        document.getElementById('coords-display').textContent =
            lat.toFixed(4) + '° N · ' + lng.toFixed(4) + '° E';
    }

    marker.on('dragend', e => {
        const pos = e.target.getLatLng();
        updateCoords(pos.lat, pos.lng);
    });

    miniMap.on('click', e => {
        marker.setLatLng(e.latlng);
        updateCoords(e.latlng.lat, e.latlng.lng);
    });

    document.getElementById('gps-btn').addEventListener('click', () => {
        if (!navigator.geolocation) return;
        navigator.geolocation.getCurrentPosition(pos => {
            const ll = [pos.coords.latitude, pos.coords.longitude];
            marker.setLatLng(ll);
            miniMap.setView(ll, 16);
            updateCoords(ll[0], ll[1]);
        });
    });

    function handleFileChange(input) {
        input.addEventListener('change', () => {
            const file = input.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('photo-preview').src = e.target.result;
                document.getElementById('photo-thumb').style.display = 'flex';
                document.getElementById('ph-camera').style.display   = 'none';
            };
            reader.readAsDataURL(file);
        });
    }
    handleFileChange(document.getElementById('file-camera'));

    document.getElementById('photo-remove').addEventListener('click', () => {
        document.getElementById('photo-thumb').style.display = 'none';
        document.getElementById('ph-camera').style.display   = 'flex';
        document.getElementById('file-camera').value  = '';
    });

    const typeHidden = document.getElementById('f-type');
    const bandFields  = document.getElementById('fields-bandelette');
    const photoFields = document.getElementById('fields-photometre');

    function selectType(type) {
        typeHidden.value = type;
        document.querySelectorAll('.type-card, .type-card-full').forEach(c => {
            c.classList.toggle('selected', c.dataset.type === type);
        });
        bandFields.style.display  = ['bandelette', 'les_deux'].includes(type) ? 'block' : 'none';
        photoFields.style.display = ['photometre',  'les_deux'].includes(type) ? 'block' : 'none';
    }

    document.querySelectorAll('[data-type]').forEach(card => {
        card.addEventListener('click', () => selectType(card.dataset.type));
    });

    selectType(typeHidden.value || 'bandelette');

    const tabs     = document.querySelectorAll('.step-tab');
    const sections = ['section-1','section-2','section-3','section-4'].map(id => document.getElementById(id));
    const scroll   = document.getElementById('form-scroll');

    function updateStepBar() {
        const scrollTop = scroll.scrollTop + 80;
        let active = 0;
        sections.forEach((s, i) => {
            if (s && s.offsetTop <= scrollTop) active = i;
        });
        tabs.forEach((tab, i) => {
            tab.classList.toggle('active', i === active);
            tab.classList.toggle('done', i < active);
        });
    }
    scroll.addEventListener('scroll', updateStepBar, { passive: true });

    tabs.forEach((tab, i) => {
        tab.addEventListener('click', () => {
            if (sections[i]) {
                scroll.scrollTo({ top: sections[i].offsetTop - 20, behavior: 'smooth' });
            }
        });
    });

    setTimeout(() => miniMap.invalidateSize(), 150);
});
</script>
@endpush

@endsection
