(function () {
    const cfg = window.__HEADER_CONFIG || {};

    let analyses   = [];
    let current    = 0;
    let invalidMap = null;

    const qualiteConfig = {
        tres_bon: { label: 'Très bon',  bg: 'bg-emerald-100', text: 'text-emerald-700', dot: 'bg-emerald-500' },
        bon:      { label: 'Bon',       bg: 'bg-teal-100',    text: 'text-teal-700',    dot: 'bg-teal-500'    },
        passable: { label: 'Passable',  bg: 'bg-yellow-100',  text: 'text-yellow-700',  dot: 'bg-yellow-400'  },
        mediocre: { label: 'Médiocre',  bg: 'bg-orange-100',  text: 'text-orange-700',  dot: 'bg-orange-400'  },
        mauvais:  { label: 'Mauvais',   bg: 'bg-red-100',     text: 'text-red-700',     dot: 'bg-red-500'     },
    };

    window.openInvalidesOverlay = function () {
        document.getElementById('invalides-overlay').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setLoading(true);

        fetch(cfg.invalidesUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                analyses = data;
                current  = 0;
                setLoading(false);
                analyses.length ? showCard(0) : showEmpty();
            });
    };

    window.closeInvalidesOverlay = function () {
        document.getElementById('invalides-overlay').classList.add('hidden');
        document.body.style.overflow = '';
        destroyMap();
    };

    window.validerCurrent = function () {
        const a = analyses[current];
        if (!a) return;

        const btn = document.getElementById('btn-valider');
        btn.disabled = true;
        btn.innerHTML = '…';

        fetch(`/analyse/${a.id}/valider`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': cfg.csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then(r => r.json())
            .then(res => {
                analyses.splice(current, 1);
                updateBadge(res.remaining);
                destroyMap();
                if (!analyses.length) {
                    showEmpty();
                } else {
                    if (current >= analyses.length) current = 0;
                    showCard(current);
                }
            });
    };

    window.skipCurrent = function () {
        if (!analyses.length) return;
        current = (current + 1) % analyses.length;
        destroyMap();
        showCard(current);
    };

    function setLoading(on) {
        document.getElementById('invalides-loading').classList.toggle('hidden', !on);
        document.getElementById('invalides-content').classList.toggle('hidden', on);
        document.getElementById('invalides-empty').classList.toggle('hidden', on);
        if (on) {
            document.getElementById('invalides-content').classList.add('hidden');
            document.getElementById('invalides-empty').classList.add('hidden');
        }
    }

    function showCard(idx) {
        const a = analyses[idx];
        document.getElementById('invalides-content').classList.remove('hidden');
        document.getElementById('invalides-empty').classList.add('hidden');

        const btn = document.getElementById('btn-valider');
        btn.disabled = false;
        btn.innerHTML = `
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            Valider cette analyse`;

        document.getElementById('invalides-counter').textContent = `${idx + 1} / ${analyses.length}`;
        document.getElementById('inv-cours-eau').textContent = a.cours_d_eau;
        document.getElementById('inv-meta').textContent =
            `${a.date}${a.time ? ' · ' + a.time : ''}${a.user ? ' · ' + a.user : ''}`;

        const cfg2 = qualiteConfig[a.qualite] || qualiteConfig.passable;
        const badge = document.getElementById('inv-qualite-badge');
        badge.className = `shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold ${cfg2.bg} ${cfg2.text}`;
        badge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full ${cfg2.dot}"></span>${cfg2.label}`;

        document.getElementById('inv-mesures').innerHTML = a.mesures.length
            ? a.mesures.map(m => `
                <div class="bg-slate-50 rounded-xl px-3 py-2.5">
                    <p class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1">${m.label}</p>
                    <p class="text-sm font-bold text-slate-700">${m.value}<span class="text-[10px] font-normal text-slate-400 ml-0.5">${m.unit}</span></p>
                </div>`).join('')
            : '<p class="text-sm text-slate-400 italic col-span-3">Aucune mesure enregistrée.</p>';

        const noteBlock = document.getElementById('inv-note-block');
        if (a.note) {
            noteBlock.classList.remove('hidden');
            document.getElementById('inv-note').textContent = a.note;
        } else {
            noteBlock.classList.add('hidden');
        }

        const photoBlock = document.getElementById('inv-photo-block');
        if (a.image) {
            photoBlock.classList.remove('hidden');
            document.getElementById('inv-photo').src = a.image;
        } else {
            photoBlock.classList.add('hidden');
        }

        setTimeout(() => initMap(a), 80);
    }

    function initMap(a) {
        if (!a.latitude || !a.longitude || typeof L === 'undefined') return;
        const el = document.getElementById('invalides-map');
        destroyMap();
        invalidMap = L.map(el, { zoomControl: false, attributionControl: false })
            .setView([a.latitude, a.longitude], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(invalidMap);
        L.control.zoom({ position: 'bottomright' }).addTo(invalidMap);
        L.circleMarker([a.latitude, a.longitude], {
            radius: 8, color: '#ef4444', fillColor: '#ef4444', fillOpacity: 0.9, weight: 2,
        }).addTo(invalidMap);
        invalidMap.invalidateSize();
    }

    function destroyMap() {
        if (invalidMap) { invalidMap.remove(); invalidMap = null; }
    }

    function showEmpty() {
        document.getElementById('invalides-content').classList.add('hidden');
        document.getElementById('invalides-empty').classList.remove('hidden');
    }

    function updateBadge(remaining) {
        const btn  = document.getElementById('btn-invalides');
        const span = document.getElementById('invalides-count');
        if (!btn) return;
        if (remaining === 0) {
            btn.classList.add('hidden');
        } else {
            btn.classList.remove('hidden');
            if (span) span.textContent = `${remaining} invalide${remaining > 1 ? 's' : ''}`;
        }
    }

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeInvalidesOverlay(); });
})();
