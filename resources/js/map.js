import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

document.addEventListener('DOMContentLoaded', () => {

    const map = L.map('map', {
        center: [48.8153, 7.7884],
        zoom: 13,
        zoomControl: false,
        attributionControl: false,
    });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors &copy; CARTO'
    }).addTo(map);

    fetch('CoursEau_FXX.json')
        .then(response => response.json())
        .then(data => {
            L.geoJSON(data, {
                style: {
                    color: '#2563eb',
                    weight: 3,
                    opacity: 0.8
                },
                onEachFeature: function (feature, layer) {
                    layer.on('click', function (e) {

                        L.DomEvent.stopPropagation(e);

                        if (sheetOpen) { closeSheet(); return; }
                        if (!window.userAuthenticated) { showAuthToast(); return; }

                        const nomRiviere = feature.properties.nom_cours_eau || feature.properties.name || feature.properties.nom || null;

                        showCreateCard(e.latlng, null, nomRiviere);
                    });
                }
            }).addTo(map);
        })
        .catch(error => console.error("Erreur GeoJSON :", error));
    const sheet   = document.getElementById('bottom-sheet');
    const nav     = document.getElementById('bottom-nav');
    let sheetOpen = false;

    function openSheet() {
        sheet.classList.add('open');
        nav.classList.add('hidden-nav');
        sheetOpen = true;
    }
    function closeSheet() {
        sheet.classList.remove('open');
        nav.classList.remove('hidden-nav');
        sheetOpen = false;
        sheet.style.transform = '';
    }

    document.getElementById('sheet-close-btn').addEventListener('click', closeSheet);

    let startY = 0, dragging = false;
    sheet.addEventListener('touchstart', e => {
        startY   = e.touches[0].clientY;
        dragging = true;
    }, { passive: true });
    sheet.addEventListener('touchmove', e => {
        if (!dragging) return;
        const d = e.touches[0].clientY - startY;
        if (d > 0) sheet.style.transform = `translateY(${d}px)`;
    }, { passive: true });
    sheet.addEventListener('touchend', e => {
        dragging = false;
        const d = e.changedTouches[0].clientY - startY;
        sheet.style.transform = '';
        if (d > 80) closeSheet();
    });

    const createCard = document.getElementById('create-card');
    let tempMarker   = null;

    function showCreateCard(latlng, coursDEauId = null, nomRiviere = null) {
        if (tempMarker) { tempMarker.remove(); tempMarker = null; }

        const pulseIcon = L.divIcon({
            className: '',
            html: `<div style="width:18px;height:18px;border-radius:50%;background:#1565c0;border:3px solid white;box-shadow:0 0 0 4px rgba(21,101,192,0.25);animation:pulse-ring 1.5s ease infinite;"></div>`,
            iconSize: [18, 18], iconAnchor: [9, 9],
        });
        tempMarker = L.marker(latlng, { icon: pulseIcon }).addTo(map);

        const url = new URL(window.createAnalyseUrl, window.location.origin);
        url.searchParams.set('lat', latlng.lat.toFixed(6));
        url.searchParams.set('lng', latlng.lng.toFixed(6));

        if (coursDEauId) url.searchParams.set('cours_d_eau_id', coursDEauId);
        if (nomRiviere)  url.searchParams.set('nom_cours_eau', nomRiviere);

        createCard.querySelector('#cc-lat').textContent  = latlng.lat.toFixed(4) + '° N';
        createCard.querySelector('#cc-lng').textContent  = latlng.lng.toFixed(4) + '° E';
        createCard.querySelector('#cc-link').href        = url.toString();

        createCard.querySelector('#cc-river').textContent = nomRiviere
            ? nomRiviere
            : (coursDEauId ? (getNearestRiverName(latlng) ?? 'Cours d\'eau inconnu') : 'Position hors réseau');

        createCard.classList.add('show');
    }

    function hideCreateCard() {
        createCard.classList.remove('show');
        if (tempMarker) { tempMarker.remove(); tempMarker = null; }
    }

    document.getElementById('cc-cancel').addEventListener('click', hideCreateCard);

    const points = window.mapPoints ?? [];

    function makeMarkerIcon(color) {
        return L.divIcon({
            className: '',
            html: `<div style="
                width:16px;height:16px;border-radius:50%;
                background:${color};border:2.5px solid white;
                box-shadow:0 2px 6px rgba(0,0,0,0.25);
            "></div>`,
            iconSize: [16, 16],
            iconAnchor: [8, 8],
        });
    }

    function typeLabel(type) {
        return { bandelette: 'Bandelette JBL', photometre: 'Photomètre', les_deux: 'Bandelette + Photomètre' }[type] ?? type;
    }

    function markerColor(point) {
        return '#1565c0';
    }

    points.forEach(p => {
        const color  = markerColor(p);
        const marker = L.marker([p.latitude, p.longitude], { icon: makeMarkerIcon(color) }).addTo(map);

        marker.on('click', () => {
            hideCreateCard();
            populateSheet(p);
            openSheet();
        });
    });

    function populateSheet(p) {
        const a = p.analyse;
        sheet.querySelector('.sheet-coords-text').textContent =
            p.latitude.toFixed(4) + '° N · ' + p.longitude.toFixed(4) + '° E';
        sheet.querySelector('.sheet-river-name').textContent = p.cours_d_eau ?? 'Cours d\'eau';
        sheet.querySelector('.sheet-analyse-info').innerHTML =
        `<div class="sheet-meta-grid">
                   <div class="meta-item"><span class="meta-label">Par</span><span class="meta-val">${a.user_name}</span></div>
                   <div class="meta-item"><span class="meta-label">Date</span><span class="meta-val">${a.created_at ?? '—'}</span></div>
                   <div class="meta-item"><span class="meta-label">Type</span><span class="meta-val">${typeLabel(a.type)}</span></div>
               </div>`;
    const btnHtml = `
        <div class="mt-4 border-t pt-4">
            <a href="${window.createAnalyseUrl}?point_id=${p.id}&lat=${p.latitude}&lng=${p.longitude}&cours_d_eau_id=${p.cours_d_eau_id}"
               class="flex items-center justify-center gap-2 bg-sv-blue text-white py-3 rounded-xl font-bold no-underline active:scale-95 transition-transform">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M12 4v16m8-8H4"/>
                </svg>
                Nouvelle mesure ici
            </a>
        </div>
    `;

    sheet.querySelector('.sheet-analyse-info').innerHTML += btnHtml;
    }

    map.on('click', e => {
        if (sheetOpen) {
            closeSheet();
            return;
        }

        if (!window.userAuthenticated) {
            showAuthToast();
            return;
        }

        const nearestId = getNearestCoursDEauId(e.latlng);
        showCreateCard(e.latlng, nearestId);
    });

    function showAuthToast() {
        if (document.getElementById('auth-toast')) return;

        const toast = document.createElement('div');
        toast.id = 'auth-toast';
        toast.innerHTML = `
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div style="flex:1;">
                    <div style="font-weight:700;font-size:13px;margin-bottom:1px;">Connexion requise</div>
                    <div style="font-size:11px;opacity:0.75;">Connectez-vous pour déposer une analyse.</div>
                </div>
                <a href="${window.loginUrl}" style="padding:7px 14px;background:white;color:#222a60;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;flex-shrink:0;white-space:nowrap;">
                    Se connecter
                </a>
            </div>`;

        Object.assign(toast.style, {
            position: 'absolute',
            bottom: '88px',
            left: '16px',
            right: '16px',
            zIndex: '50',
            background: 'linear-gradient(135deg,#0f1d42,#1a2a6c)',
            color: 'white',
            borderRadius: '14px',
            padding: '12px 14px',
            boxShadow: '0 8px 30px rgba(34,42,96,0.3)',
            fontFamily: "'Space Grotesk',sans-serif",
            opacity: '0',
            transform: 'translateY(8px)',
            transition: 'opacity 0.25s ease, transform 0.25s ease',
        });

        document.getElementById('app-shell').appendChild(toast);

        requestAnimationFrame(() => {
            toast.style.opacity   = '1';
            toast.style.transform = 'translateY(0)';
        });

        setTimeout(() => {
            toast.style.opacity   = '0';
            toast.style.transform = 'translateY(8px)';
            setTimeout(() => toast.remove(), 280);
        }, 4000);
    }

    function getNearestCoursDEauId(latlng) {
        if (!points.length) return null;
        let nearest = null, minDist = Infinity;
        for (const p of points) {
            const d = Math.hypot(p.latitude - latlng.lat, p.longitude - latlng.lng);
            if (d < minDist) { minDist = d; nearest = p; }
        }
        return minDist < 0.05 ? nearest?.cours_d_eau_id : null;
    }

    function getNearestRiverName(latlng) {
        if (!points.length) return null;
        let nearest = null, minDist = Infinity;
        for (const p of points) {
            const d = Math.hypot(p.latitude - latlng.lat, p.longitude - latlng.lng);
            if (d < minDist) { minDist = d; nearest = p; }
        }
        return minDist < 0.05 ? nearest?.cours_d_eau : null;
    }

    const hint = document.getElementById('tap-hint');
    const hideHint = () => {
        if (hint && !hint.classList.contains('fade-out')) {
            hint.classList.add('fade-out');
            setTimeout(() => hint?.remove(), 500);
        }
    };
    setTimeout(hideHint, 3500);
    map.on('click', hideHint);

    document.querySelectorAll('.pill').forEach(pill => {
        pill.addEventListener('click', () => pill.classList.toggle('active'));
    });

    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', e => {
            e.preventDefault();
            document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
            item.classList.add('active');
        });
    });

    document.getElementById('btn-zoom-in')?.addEventListener('click',  () => map.zoomIn());
    document.getElementById('btn-zoom-out')?.addEventListener('click', () => map.zoomOut());
});
