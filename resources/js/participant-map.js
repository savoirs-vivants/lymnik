// Carte participant

const QUALITE_COLORS = {
    tres_bon: '#3b82f6',
    bon:      '#16987c',
    passable: '#eab308',
    mediocre: '#f97316',
    mauvais:  '#ef4444',
};

const QUALITE_LABELS = {
    tres_bon: 'Très bon',
    bon:      'Bon',
    passable: 'Passable',
    mediocre: 'Médiocre',
    mauvais:  'Mauvais',
};

const map = L.map('map', { zoomControl: false }).setView([46.5, 2.5], 6);

L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    attribution: '© CartoDB',
    maxZoom: 19,
}).addTo(map);

L.control.zoom({ position: 'bottomright' }).addTo(map);

const bottomSheet  = document.getElementById('bottom-sheet');
const createCard   = document.getElementById('create-card');
let clickMarker    = null;

// ─── Points existants ─────────────────────────────────────────────────────────
const points = window.__participantPoints || [];

points.forEach(pt => {
    const bestQ = bestQualite(pt.analyses);
    const color = QUALITE_COLORS[bestQ] || '#94a3b8';

    const icon = L.divIcon({
        className: '',
        html: `<div style="width:14px;height:14px;border-radius:50%;background:${color};border:2.5px solid white;box-shadow:0 1px 4px rgba(0,0,0,.25)"></div>`,
        iconSize: [14, 14],
        iconAnchor: [7, 7],
    });

    const marker = L.marker([pt.latitude, pt.longitude], { icon }).addTo(map);
    marker.on('click', () => showPointSheet(pt));
});

function bestQualite(analyses) {
    const order = ['mauvais', 'mediocre', 'passable', 'bon', 'tres_bon'];
    const found = analyses.map(a => a.qualite).filter(Boolean);
    return found.reduce((worst, q) => order.indexOf(q) < order.indexOf(worst) ? q : worst, 'tres_bon') || 'tres_bon';
}

function showPointSheet(pt) {
    closeCreateCard();
    const best = bestQualite(pt.analyses);
    const color = QUALITE_COLORS[best];
    const label = QUALITE_LABELS[best];

    document.querySelector('.sheet-type').textContent = `${pt.analyses.length} analyse${pt.analyses.length > 1 ? 's' : ''}`;
    document.querySelector('.sheet-river').textContent = pt.cours_eau || 'Cours d\'eau inconnu';

    const content = document.getElementById('sheet-content');
    content.innerHTML = `
        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg text-xs font-bold uppercase mb-3" style="background:${color}15;color:${color}">
            <span class="w-2 h-2 rounded-full" style="background:${color}"></span>${label}
        </div>
        <div class="space-y-2">
            ${pt.analyses.slice(0, 5).map(a => `
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <div>
                        <p class="text-xs font-semibold text-slate-700">${a.date}</p>
                        <p class="text-[10px] text-slate-400 capitalize">${(a.type || '').replace('_', ' ')}</p>
                    </div>
                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-md" style="background:${QUALITE_COLORS[a.qualite]}15;color:${QUALITE_COLORS[a.qualite]}">
                        ${QUALITE_LABELS[a.qualite] || a.qualite}
                    </span>
                </div>
            `).join('')}
        </div>
    `;

    bottomSheet.classList.add('open');
}

document.getElementById('sheet-close')?.addEventListener('click', () => bottomSheet.classList.remove('open'));

// ─── Clic sur la carte → nouvelle analyse ─────────────────────────────────────
map.on('click', async (e) => {
    bottomSheet.classList.remove('open');
    const { lat, lng } = e.latlng;

    if (clickMarker) { map.removeLayer(clickMarker); clickMarker = null; }

    clickMarker = L.marker([lat, lng], {
        icon: L.divIcon({
            className: '',
            html: '<div style="width:16px;height:16px;border-radius:50%;background:#222a60;border:3px solid white;box-shadow:0 2px 8px rgba(34,42,96,.4)"></div>',
            iconSize: [16, 16], iconAnchor: [8, 8],
        }),
    }).addTo(map);

    document.getElementById('f-lat').value = lat;
    document.getElementById('f-lng').value = lng;
    document.getElementById('create-coords').textContent = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
    document.getElementById('form-error').textContent = '';

    // Récupération du cours d'eau le plus proche
    try {
        const res  = await fetch(`/mobile/cours-d-eau/nearest?lat=${lat}&lng=${lng}`);
        const json = await res.json();
        document.getElementById('create-river-name').textContent = json.nom || 'Cours d\'eau inconnu';
        document.getElementById('f-cours-eau-id').value = json.id || '';
    } catch {
        document.getElementById('create-river-name').textContent = 'Cours d\'eau inconnu';
    }

    createCard.classList.add('show');
});

function closeCreateCard() {
    createCard.classList.remove('show');
    if (clickMarker) { map.removeLayer(clickMarker); clickMarker = null; }
    document.getElementById('form-analyse')?.reset();
}

document.getElementById('create-close')?.addEventListener('click', closeCreateCard);

// ─── Type d'analyse : afficher/masquer les sections ───────────────────────────
document.getElementById('f-type')?.addEventListener('change', function () {
    const t = this.value;
    document.getElementById('section-bandelette').classList.toggle('hidden', t === 'photometre');
    document.getElementById('section-photometre').classList.toggle('hidden', t === 'bandelette');
});

// ─── Soumission de l'analyse ──────────────────────────────────────────────────
document.getElementById('form-analyse')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = e.target.querySelector('[type=submit]');
    btn.disabled = true;
    btn.textContent = 'Enregistrement…';

    const form   = e.target;
    const type   = document.getElementById('f-type').value;
    const data   = {
        latitude:      parseFloat(document.getElementById('f-lat').value),
        longitude:     parseFloat(document.getElementById('f-lng').value),
        cours_d_eau_id: document.getElementById('f-cours-eau-id').value || null,
        type,
        mesures:       {},
    };

    if (['bandelette', 'les_deux'].includes(type)) {
        data.mesures.bandelette = {};
        form.querySelectorAll('[name^="mesures[bandelette]"]').forEach(input => {
            const key = input.name.match(/\[([^\]]+)\]$/)?.[1];
            if (key && input.value !== '') data.mesures.bandelette[key] = parseFloat(input.value);
        });
    }
    if (['photometre', 'les_deux'].includes(type)) {
        data.mesures.photometre = {};
        form.querySelectorAll('[name^="mesures[photometre]"]').forEach(input => {
            const key = input.name.match(/\[([^\]]+)\]$/)?.[1];
            if (key && input.value !== '') data.mesures.photometre[key] = parseFloat(input.value);
        });
    }

    try {
        const res  = await fetch('/session/analyse', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(data),
        });

        const json = await res.json();

        if (!res.ok) {
            document.getElementById('form-error').textContent = json.message || 'Erreur lors de l\'enregistrement.';
            btn.disabled = false;
            btn.textContent = 'Enregistrer l\'analyse';
            return;
        }

        // Ajouter le marqueur avec la qualité
        const color = QUALITE_COLORS[json.analyse.qualite] || '#94a3b8';
        if (clickMarker) { map.removeLayer(clickMarker); clickMarker = null; }

        L.marker([json.analyse.lat, json.analyse.lng], {
            icon: L.divIcon({
                className: '',
                html: `<div style="width:14px;height:14px;border-radius:50%;background:${color};border:2.5px solid white;box-shadow:0 1px 4px rgba(0,0,0,.25)"></div>`,
                iconSize: [14, 14], iconAnchor: [7, 7],
            }),
        }).addTo(map);

        createCard.classList.remove('show');
        form.reset();

        // Toast succès
        showToast('Analyse enregistrée !', 'success');

        btn.disabled = false;
        btn.textContent = 'Enregistrer l\'analyse';
    } catch {
        document.getElementById('form-error').textContent = 'Erreur réseau. Réessayez.';
        btn.disabled = false;
        btn.textContent = 'Enregistrer l\'analyse';
    }
});

// ─── Localisation ─────────────────────────────────────────────────────────────
document.getElementById('locate-btn')?.addEventListener('click', () => {
    if (!navigator.geolocation) return;
    navigator.geolocation.getCurrentPosition(pos => {
        map.setView([pos.coords.latitude, pos.coords.longitude], 15);
    });
});

// ─── Recherche de lieu ────────────────────────────────────────────────────────
let searchTimeout;
document.getElementById('search-input')?.addEventListener('input', function () {
    clearTimeout(searchTimeout);
    const q = this.value.trim();
    const results = document.getElementById('search-results');
    if (!q) { results.classList.add('hidden'); return; }

    searchTimeout = setTimeout(async () => {
        try {
            const res  = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&limit=5`);
            const json = await res.json();
            if (!json.length) { results.classList.add('hidden'); return; }
            results.innerHTML = json.map(r => `
                <button class="block w-full text-left px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 border-b border-slate-50 last:border-0"
                    data-lat="${r.lat}" data-lng="${r.lon}">
                    ${r.display_name}
                </button>
            `).join('');
            results.classList.remove('hidden');
            results.querySelectorAll('button').forEach(btn => {
                btn.addEventListener('click', () => {
                    map.setView([parseFloat(btn.dataset.lat), parseFloat(btn.dataset.lng)], 13);
                    results.classList.add('hidden');
                });
            });
        } catch {}
    }, 400);
});

// ─── Toast ────────────────────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
    const t = document.createElement('div');
    t.className = `fixed bottom-20 left-1/2 -translate-x-1/2 z-[2000] px-5 py-3 rounded-2xl shadow-xl text-sm font-bold
        ${type === 'success' ? 'bg-emerald-600 text-white' : 'bg-red-500 text-white'}
        transition-opacity duration-300`;
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 2500);
}
