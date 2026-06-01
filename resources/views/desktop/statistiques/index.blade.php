@extends('layouts.desktop')
@section('title', 'Statistiques')
@section('page-title', 'Statistiques')
@section('page-subtitle', "Analyse comparative de la qualité de l'eau")

@section('content')

{{-- ── Barre d'actions ──────────────────────────────────────── --}}
<div class="flex items-center gap-3 flex-wrap mb-5">
    <button id="btn-open-filters" onclick="openFilters()"
        class="flex items-center gap-2 px-4 py-2 bg-[#222a60] hover:bg-[#1a2050] text-white text-[12px] font-bold rounded-xl transition-colors">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M7 8h10M10 12h4"/>
        </svg>
        Filtres
        <span id="filter-count" class="hidden px-1.5 py-0.5 bg-white text-[#222a60] text-[9px] font-black rounded-full leading-none"></span>
    </button>

    <div class="flex-1"></div>

    <button onclick="exportData('csv')"
        class="flex items-center gap-1.5 px-3 py-2 bg-white border border-slate-200 rounded-xl text-[11px] font-bold text-slate-600 hover:bg-slate-50 shadow-sm">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        CSV
    </button>
    <button onclick="exportData('xlsx')"
        class="flex items-center gap-1.5 px-3 py-2 bg-emerald-600 text-white rounded-xl text-[11px] font-bold hover:bg-emerald-700 shadow-sm">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Excel
    </button>
</div>

{{-- ── KPIs ──────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
    @foreach([
        ['id'=>'kpi-analyses','label'=>'Analyses'],
        ['id'=>'kpi-periode', 'label'=>'Période','small'=>true],
    ] as $k)
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
        <p class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1.5">{{ $k['label'] }}</p>
        <p id="{{ $k['id'] }}" class="{{ isset($k['small'])?'text-sm leading-tight':'text-2xl' }} font-black text-[#222a60]">—</p>
    </div>
    @endforeach
</div>

{{-- ── Graphiques ────────────────────────────────────────────── --}}
<div class="space-y-4">

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div>
                <h3 class="text-[13px] font-bold text-slate-800 mb-0.5">Évolution temporelle</h3>
                <p id="time-chart-subtitle" class="text-[10px] text-slate-400 font-mono">Moyenne journalière</p>
            </div>
            <button onclick="exportChartAsPng('mainTimeChart', 'evolution_temporelle')"
                class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-[11px] font-bold shrink-0 transition-colors">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                PNG
            </button>
        </div>
        <div class="relative h-56 sm:h-72">
            <canvas id="mainTimeChart"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div>
                <h3 class="text-[13px] font-bold text-slate-800" id="bar-chart-title">Comparaison des mesures</h3>
                <p id="bar-chart-subtitle" class="text-[10px] text-slate-400 font-mono mt-0.5">Valeur moyenne par groupe</p>
            </div>
            <button onclick="exportChartAsPng()"
                class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-[11px] font-bold shrink-0">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                PNG
            </button>
        </div>
        <div class="relative h-64 sm:h-80">
            <canvas id="barChart"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div>
                <h3 class="text-[13px] font-bold text-slate-800 mb-0.5">Distribution de la qualité</h3>
                <p id="qualite-chart-subtitle" class="text-[10px] text-slate-400 font-mono">Répartition des niveaux</p>
            </div>
            <button onclick="exportChartAsPng('qualiteChart', 'distribution_qualite')"
                class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-[11px] font-bold shrink-0 transition-colors">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                PNG
            </button>
        </div>
        <div class="relative h-48 sm:h-64">
            <canvas id="qualiteChart"></canvas>
        </div>
    </div>

</div>

{{-- ════════════════════════════════════════════════════════════
     DRAWER FILTRES — contrôlé 100% en JS inline style
     (aucune classe Tailwind pour le transform, zéro risque)
════════════════════════════════════════════════════════════ --}}

{{-- Backdrop --}}
<div id="filter-backdrop"
     onclick="closeFilters()"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.3); z-index:40;"></div>

{{-- Panel filtre --}}
<div id="filter-panel"
     style="display:none; position:fixed; top:0; right:0; bottom:0; width:320px; max-width:90vw;
            background:#fff; z-index:50; flex-direction:column; overflow:hidden;
            border-left:1px solid #e2e8f0; box-shadow:-4px 0 30px rgba(34,42,96,0.12);
            transform:translateX(100%); transition:transform 0.3s ease-out;">

    {{-- En-tête --}}
    <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #f1f5f9; flex-shrink:0; background:#f8fafc;">
        <span style="font-size:10px; font-family:monospace; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:#222a60;">Filtres</span>
        <div style="display:flex; align-items:center; gap:8px;">
            <button id="filter-reset" onclick="resetFilters()"
                style="font-size:10px; font-weight:600; color:#94a3b8; background:none; border:none; cursor:pointer; display:flex; align-items:center; gap:4px;">
                ↺ Réinitialiser
            </button>
            <button onclick="closeFilters()"
                style="color:#94a3b8; background:none; border:none; cursor:pointer; font-size:18px; line-height:1; padding:2px 4px;">×</button>
        </div>
    </div>

    {{-- Corps scrollable --}}
    <div style="flex:1; overflow-y:auto;">

        {{-- Cours d'eau --}}
        <div style="border-bottom:1px solid #f1f5f9;">
            <button type="button" style="width:100%; padding:12px 20px; display:flex; align-items:center; justify-content:space-between; background:none; border:none; cursor:pointer; text-align:left;" onclick="toggleSection(this)">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="font-size:9px; font-family:monospace; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:#64748b;">Cours d'eau</span>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="font-size:9px; color:#222a60; font-weight:700; cursor:pointer;" onclick="event.stopPropagation();locSelectAll()">Tout</span>
                    <span style="color:#cbd5e1; font-size:9px;">|</span>
                    <span style="font-size:9px; color:#94a3b8; font-weight:700; cursor:pointer;" onclick="event.stopPropagation();locSelectNone()">Aucun</span>
                    <svg class="section-chevron" style="width:12px;height:12px;color:#cbd5e1;transition:transform .2s;transform:rotate(180deg);" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </button>
            <div class="section-body" style="padding:0 12px 12px;">
                <p style="font-size:10px; color:#94a3b8; padding:0 8px 8px;">Aucune sélection = toutes les données.</p>
                @foreach($riversTree as $river)
                <div class="loc-tree-group">
                    <div style="display:flex; align-items:center; gap:4px;">
                        @if(count($river['villes']) > 0)
                        <button type="button" class="tree-toggle" onclick="toggleTree(this)" style="width:20px;height:20px;display:flex;align-items:center;justify-content:center;background:none;border:none;cursor:pointer;color:#cbd5e1;flex-shrink:0;">
                            <svg class="tree-chevron" style="width:10px;height:10px;transition:transform .2s;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        @else
                        <span style="width:20px;flex-shrink:0;"></span>
                        @endif
                        <label style="flex:1;display:flex;align-items:center;gap:8px;padding:6px 8px;border-radius:8px;cursor:pointer;">
                            <input type="checkbox" value="river-{{ $river['id'] }}" data-loc-type="river" class="filter-loc" style="width:14px;height:14px;accent-color:#222a60;">
                            <span style="font-size:12px;font-weight:500;color:#374151;truncate;">{{ $river['nom'] }}</span>
                        </label>
                    </div>
                    <div class="tree-children" style="display:none;padding-left:24px;margin-top:2px;">
                        @foreach($river['villes'] as $ville)
                        <div class="loc-tree-group">
                            <div style="display:flex;align-items:center;gap:4px;">
                                @if(count($ville['analyses']) > 0)
                                <button type="button" class="tree-toggle" onclick="toggleTree(this)" style="width:16px;height:16px;display:flex;align-items:center;justify-content:center;background:none;border:none;cursor:pointer;color:#cbd5e1;flex-shrink:0;">
                                    <svg class="tree-chevron" style="width:8px;height:8px;transition:transform .2s;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </button>
                                @else
                                <span style="width:16px;flex-shrink:0;"></span>
                                @endif
                                <label style="flex:1;display:flex;align-items:center;gap:8px;padding:4px 8px;border-radius:8px;cursor:pointer;">
                                    <input type="checkbox" value="ville-{{ $river['id'] }}::{{ $ville['nom'] }}" data-loc-type="ville" class="filter-loc" style="width:12px;height:12px;accent-color:#222a60;">
                                    <span style="font-size:11.5px;color:#4b5563;">{{ $ville['nom'] }}</span>
                                </label>
                            </div>
                            <div class="tree-children" style="display:none;padding-left:20px;margin-top:2px;">
                                @foreach($ville['analyses'] as $analyse)
                                <label style="display:flex;align-items:center;gap:8px;padding:3px 8px;border-radius:8px;cursor:pointer;">
                                    <input type="checkbox" value="analyse-{{ $analyse['id'] }}" data-loc-type="analyse" class="filter-loc" style="width:12px;height:12px;accent-color:#222a60;">
                                    <span style="font-size:11px;color:#6b7280;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $analyse['nom'] }}">{{ $analyse['nom'] }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
                @if(count($riversTree) === 0)
                <p style="font-size:11px;color:#94a3b8;padding:8px;">Aucun cours d'eau.</p>
                @endif
            </div>
        </div>

        {{-- Campagnes --}}
        <div style="border-bottom:1px solid #f1f5f9;">
            <button type="button" style="width:100%;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;background:none;border:none;cursor:pointer;text-align:left;" onclick="toggleSection(this)">
                <span style="font-size:9px;font-family:monospace;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#64748b;">Campagnes & Groupes</span>
                <svg class="section-chevron" style="width:12px;height:12px;color:#cbd5e1;transition:transform .2s;transform:rotate(180deg);" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="section-body" style="padding:0 12px 12px;">
                <p style="font-size:10px;color:#94a3b8;padding:0 8px 8px;">Comparer des groupes entre eux.</p>
                @forelse($campagnesTree as $campagne)
                <div class="camp-tree-group">
                    <div style="display:flex;align-items:center;gap:4px;">
                        @if(count($campagne['groupes']) > 0)
                        <button type="button" class="tree-toggle" onclick="toggleTree(this)" style="width:20px;height:20px;display:flex;align-items:center;justify-content:center;background:none;border:none;cursor:pointer;color:#cbd5e1;flex-shrink:0;">
                            <svg class="tree-chevron" style="width:10px;height:10px;transition:transform .2s;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        @else
                        <span style="width:20px;flex-shrink:0;"></span>
                        @endif
                        <label style="flex:1;display:flex;align-items:center;gap:8px;padding:6px 8px;border-radius:8px;cursor:pointer;">
                            <input type="checkbox" value="campagne-{{ $campagne['id'] }}" class="filter-source filter-campagne" style="width:14px;height:14px;accent-color:#222a60;">
                            <span style="font-size:12px;font-weight:500;color:#374151;">{{ $campagne['nom'] }}</span>
                        </label>
                    </div>
                    <div class="tree-children" style="display:none;padding-left:24px;margin-top:2px;">
                        @foreach($campagne['groupes'] as $groupe)
                        <label style="display:flex;align-items:center;gap:8px;padding:4px 8px;border-radius:8px;cursor:pointer;">
                            <input type="checkbox" value="groupe-{{ $campagne['id'] }}-{{ $groupe }}" class="filter-source filter-groupe" style="width:12px;height:12px;accent-color:#222a60;">
                            <span style="font-size:11.5px;color:#4b5563;">@if($groupe==0) Sans groupe @else Groupe {{ chr(64+$groupe) }} @endif</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @empty
                <p style="font-size:11px;color:#94a3b8;padding:8px;">Aucune campagne.</p>
                @endforelse
            </div>
        </div>

        {{-- Mesures --}}
        <div style="border-bottom:1px solid #f1f5f9;">
            <button type="button" style="width:100%;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;background:none;border:none;cursor:pointer;text-align:left;" onclick="toggleSection(this)">
                <span style="font-size:9px;font-family:monospace;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#64748b;">Mesures</span>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="font-size:9px;color:#222a60;font-weight:700;cursor:pointer;" id="mesures-all" onclick="event.stopPropagation()">Tout</span>
                    <span style="color:#cbd5e1;font-size:9px;">|</span>
                    <span style="font-size:9px;color:#94a3b8;font-weight:700;cursor:pointer;" id="mesures-none" onclick="event.stopPropagation()">Aucun</span>
                    <svg class="section-chevron" style="width:12px;height:12px;color:#cbd5e1;transition:transform .2s;transform:rotate(180deg);" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </button>
            <div class="section-body" style="padding:0 12px 12px;" id="mesures-checkboxes">
                @php $mesuresList = [
                    'nitrates'      => ['label'=>'Nitrates',        'unit'=>'mg/L','hex'=>'#3b82f6'],
                    'nitrites'      => ['label'=>'Nitrites',        'unit'=>'mg/L','hex'=>'#8b5cf6'],
                    'ph'            => ['label'=>'pH',              'unit'=>'',   'hex'=>'#14b8a6'],
                    'chlore'        => ['label'=>'Chlore',          'unit'=>'mg/L','hex'=>'#06b6d4'],
                    'durete'        => ['label'=>'Dureté totale',   'unit'=>'mg/L','hex'=>'#f59e0b'],
                    'phosphate'     => ['label'=>'Phosphate',       'unit'=>'mg/L','hex'=>'#f97316'],
                    'ammoniaque'    => ['label'=>'Ammoniaque',      'unit'=>'mg/L','hex'=>'#ef4444'],
                    'nitrate_photo' => ['label'=>'Nitrate (photo)', 'unit'=>'mg/L','hex'=>'#6366f1'],
                ]; @endphp
                @foreach($mesuresList as $key => $m)
                <label style="display:flex;align-items:center;gap:10px;padding:6px 8px;border-radius:8px;cursor:pointer;">
                    <input type="checkbox" value="{{ $key }}" class="filter-mesure" style="width:14px;height:14px;accent-color:#222a60;" checked>
                    <span style="width:8px;height:8px;border-radius:50%;background:{{ $m['hex'] }};flex-shrink:0;"></span>
                    <span style="flex:1;font-size:12px;font-weight:500;color:#374151;">{{ $m['label'] }}</span>
                    @if($m['unit'])<span style="font-size:9px;font-family:monospace;color:#94a3b8;">{{ $m['unit'] }}</span>@endif
                </label>
                @endforeach
            </div>
        </div>

        {{-- Période --}}
        <div>
            <button type="button" style="width:100%;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;background:none;border:none;cursor:pointer;text-align:left;" onclick="toggleSection(this)">
                <span style="font-size:9px;font-family:monospace;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#64748b;">Période</span>
                <svg class="section-chevron" style="width:12px;height:12px;color:#cbd5e1;transition:transform .2s;transform:rotate(180deg);" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="section-body" style="padding:0 16px 16px;">
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:4px;margin-bottom:12px;">
                    @foreach(['7'=>'7J','30'=>'30J','365'=>'1A','all'=>'Tout'] as $r=>$l)
                    <button type="button" data-range="{{ $r }}" class="filter-time-btn"
                        style="padding:6px 4px;border-radius:8px;font-size:10px;font-weight:700;border:none;cursor:pointer;transition:all .15s;{{ $r==='all'?'background:#222a60;color:#fff;':'background:#f1f5f9;color:#64748b;' }}">
                        {{ $l }}
                    </button>
                    @endforeach
                </div>
                <div style="margin-bottom:8px;">
                    <p style="font-size:9px;font-family:monospace;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Du</p>
                    <input type="date" id="date-start" style="width:100%;padding:6px 10px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;font-size:11px;font-family:monospace;color:#374151;outline:none;">
                </div>
                <div>
                    <p style="font-size:9px;font-family:monospace;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Au</p>
                    <input type="date" id="date-end" style="width:100%;padding:6px 10px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;font-size:11px;font-family:monospace;color:#374151;outline:none;">
                </div>
            </div>
        </div>

    </div>{{-- /scrollable --}}

    <div style="padding:10px 20px;border-top:1px solid #f1f5f9;background:#f8fafc;flex-shrink:0;">
        <p id="filter-summary" style="font-size:10px;font-family:monospace;color:#94a3b8;"></p>
    </div>
</div>

@endsection

@push('scripts')
<script>
    window.__RAW_DATA       = @json($analyses ?? []);
    window.__RIVERS_TREE    = @json($riversTree ?? []);
    window.__CAMPAGNES_TREE = @json($campagnesTree ?? []);
</script>
@endpush
