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
        attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
    }).addTo(map);

    fetch('CoursEau_FXX.json')
        .then(r => r.json())
        .then(data => {
            L.geoJSON(data, {
                style: { color: '#2563eb', weight: 2, opacity: 0.7 },
                onEachFeature(feature, layer) {
                    layer.on('click', async e => {
                        L.DomEvent.stopPropagation(e);
                        if (sheetOpen) { closeSheet(); return; }
                        if (!window.userAuthenticated) { showAuthToast(); return; }
                        await showCreateCard(e.latlng);
                    });
                },
            }).addTo(map);
        })
        .catch(err => console.error('Erreur GeoJSON :', err));

    if (window.mapRivers?.length) {
        const features = window.mapRivers.map(r => ({
            type: 'Feature',
            properties: { nom: r.nom, id: r.id },
            geometry: r.geometry,
        }));

        L.geoJSON({ type: 'FeatureCollection', features }, {
            style: { color: '#222a60', weight: 4, opacity: 0.95 },
            onEachFeature(feature, layer) {
                layer.bindTooltip(feature.properties.nom ?? '', {
                    sticky: true,
                    className: 'font-mono text-xs',
                });
            },
        }).addTo(map);
    }

    const userIcon = L.divIcon({
        className: 'user-location-marker',
        html: `
            <div style="position: relative; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;">
                <div style="position: absolute; width: 100%; height: 100%; background-color: #eca438; border-radius: 50%; animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;"></div>
                <div style="position: relative; width: 16px; height: 16px; background-color: #ff9900; border: 2.5px solid white; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>
            </div>
        `,
        iconSize: [20, 20],
        iconAnchor: [10, 10]
    });

    const style = document.createElement('style');
    style.innerHTML = `
        @keyframes ping {
            0% { transform: scale(1); opacity: 1; }
            75%, 100% { transform: scale(2.5); opacity: 0; }
        }
    `;
    document.head.appendChild(style);

    let userMarker = null;
    const btnLocate = document.getElementById('btn-locate');

    if (btnLocate) {
        btnLocate.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert("Votre navigateur ne supporte pas la géolocalisation.");
                return;
            }

            const originalHTML = btnLocate.innerHTML;
            btnLocate.innerHTML = `
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="animation: spin 1s linear infinite; color: #2563eb;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/>
                </svg>
            `;
            if(!document.getElementById('spin-anim')) {
                 const spinStyle = document.createElement('style');
                 spinStyle.id = 'spin-anim';
                 spinStyle.innerHTML = `@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }`;
                 document.head.appendChild(spinStyle);
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    if (userMarker) map.removeLayer(userMarker);

                    userMarker = L.marker([lat, lng], {icon: userIcon}).addTo(map);
                    userMarker.setZIndexOffset(1000);

                    map.flyTo([lat, lng], 17, { animate: true, duration: 1.5 });
                    btnLocate.innerHTML = originalHTML;
                },
                function(error) {
                    let msg = "Impossible de vous localiser.";
                    if(error.code === 1) msg = "Vous devez autoriser la localisation dans votre navigateur.";
                    else if(error.code === 2) msg = "Position indisponible (vérifiez votre GPS).";
                    else if(error.code === 3) msg = "Délai d'attente dépassé.";

                    alert(msg);
                    btnLocate.innerHTML = originalHTML;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });
    }

    const sheet   = document.getElementById('bottom-sheet');
    const nav     = document.getElementById('bottom-nav');
    let sheetOpen = false;

    function openSheet()  { sheet.classList.add('open'); nav.classList.add('hidden-nav'); sheetOpen = true; }
    function closeSheet() { sheet.classList.remove('open'); nav.classList.remove('hidden-nav'); sheetOpen = false; sheet.style.transform = ''; }

    document.getElementById('sheet-close-btn').addEventListener('click', closeSheet);

    let startY = 0, dragging = false;
    sheet.addEventListener('touchstart', e => { startY = e.touches[0].clientY; dragging = true; }, { passive: true });
    sheet.addEventListener('touchmove',  e => { if (!dragging) return; const d = e.touches[0].clientY - startY; if (d > 0) sheet.style.transform = `translateY(${d}px)`; }, { passive: true });
    sheet.addEventListener('touchend',   e => { dragging = false; const d = e.changedTouches[0].clientY - startY; sheet.style.transform = ''; if (d > 80) closeSheet(); });

    const createCard = document.getElementById('create-card');
    let tempMarker   = null;

    async function showCreateCard(latlng) {
        if (tempMarker) { tempMarker.remove(); tempMarker = null; }
        const pulseIcon = L.divIcon({
            className: '',
            html: `<div style="width:18px;height:18px;border-radius:50%;background:#1565c0;border:3px solid white;box-shadow:0 0 0 4px rgba(21,101,192,0.25);animation:pulse-ring 1.5s ease infinite;"></div>`,
            iconSize: [18, 18], iconAnchor: [9, 9],
        });
        tempMarker = L.marker(latlng, { icon: pulseIcon }).addTo(map);

        createCard.querySelector('#cc-lat').textContent  = latlng.lat.toFixed(4) + '° N';
        createCard.querySelector('#cc-lng').textContent  = latlng.lng.toFixed(4) + '° E';
        createCard.querySelector('#cc-river').textContent = 'Recherche…';
        createCard.querySelector('#cc-link').href = '#';
        createCard.classList.add('show');

        try {
            const res   = await fetch(`${window.nearestRiverUrl}?lat=${latlng.lat}&lng=${latlng.lng}`);
            const river = await res.json();
            createCard.querySelector('#cc-river').textContent = river?.nom ?? 'Position libre';
            createCard.querySelector('#cc-link').href = buildAnalyseUrl(latlng, river?.id, river?.nom);
        } catch {
            createCard.querySelector('#cc-river').textContent = 'Position libre';
            createCard.querySelector('#cc-link').href = buildAnalyseUrl(latlng, null, null);
        }
    }

    function buildAnalyseUrl(latlng, riverId, rivNom) {
        const url = new URL(window.createAnalyseUrl, window.location.origin);
        url.searchParams.set('lat', latlng.lat.toFixed(6));
        url.searchParams.set('lng', latlng.lng.toFixed(6));
        if (riverId) url.searchParams.set('cours_d_eau_id', riverId);
        if (rivNom)  url.searchParams.set('nom_cours_eau', rivNom);
        return url.toString();
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
            html: `<div style="width:16px;height:16px;border-radius:50%;background:${color};border:2.5px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.25);"></div>`,
            iconSize: [16, 16], iconAnchor: [8, 8],
        });
    }

    function typeLabel(type) {
        return { bandelette: 'Bandelette JBL', photometre: 'Photomètre', les_deux: 'Bandelette + Photomètre' }[type] ?? type;
    }

    points.forEach(p => {
        const marker = L.marker([p.latitude, p.longitude], { icon: makeMarkerIcon('#1565c0') }).addTo(map);
        marker.on('click', () => { hideCreateCard(); populateSheet(p); openSheet(); });
    });

    map.on('click', async e => {
        if (sheetOpen) { closeSheet(); return; }
        if (!window.userAuthenticated) { showAuthToast(); return; }
        await showCreateCard(e.latlng);
    });

    function populateSheet(p) {
        const a = p.analyse;
        sheet.querySelector('.sheet-coords-text').textContent = p.latitude.toFixed(4) + '° N · ' + p.longitude.toFixed(4) + '° E';
        sheet.querySelector('.sheet-river-name').textContent = p.cours_d_eau ?? 'Cours d\'eau inconnu';

        let mesuresData = {};
        try { mesuresData = typeof a.mesures === 'string' ? JSON.parse(a.mesures) : (a.mesures || {}); }
        catch (e) { console.error('Erreur lecture mesures', e); }

        const dict = {
            bandelette: {
                nitrates:      { label: 'Nitrates',    unit: 'mg/L' },
                nitrites:      { label: 'Nitrites',    unit: 'mg/L' },
                durete_totale: { label: 'Dur. Tot.',   unit: 'mg/L' },
                durete_carb:   { label: 'Dur. Carb.',  unit: 'mg/L' },
                ph:            { label: 'pH',          unit: '' },
                chlore:        { label: 'Chlore',      unit: 'mg/L' },
            },
            photometre: {
                phosphate: { label: 'Phosphate', unit: 'mg/L' },
                nitrate:   { label: 'Nitrate',   unit: 'mg/L' },
                ammonium:  { label: 'Ammonium',  unit: 'mg/L' },
            },
        };

        let casesHtml = '';
        const buildCases = typeKey => {
            if (!mesuresData[typeKey]) return;
            for (const [key, val] of Object.entries(mesuresData[typeKey])) {
                if (val !== null && val !== '') {
                    const info = dict[typeKey][key] || { label: key, unit: '' };
                    casesHtml += `
                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-2.5 text-center flex flex-col justify-center">
                            <div class="text-[9px] text-slate-400 font-mono uppercase tracking-wide mb-1 leading-none">${info.label}</div>
                            <div class="text-lg font-bold text-[#222a60] leading-none">${val}<span class="text-[9px] font-normal text-slate-400 ml-0.5">${info.unit}</span></div>
                        </div>`;
                }
            }
        };
        buildCases('bandelette');
        buildCases('photometre');
        if (!casesHtml) casesHtml = '<div class="col-span-3 text-xs text-slate-400 text-center py-2">Données non renseignées.</div>';

        sheet.querySelector('.sheet-analyse-info').innerHTML = `
            <div class="flex gap-2 mb-3">
                <div class="flex-1 bg-slate-50 border border-slate-100 rounded-lg py-2 px-3">
                    <div class="font-mono text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Date</div>
                    <div class="text-xs font-bold text-[#222a60]">${a.created_at ?? '—'}</div>
                </div>
                <div class="flex-1 bg-slate-50 border border-slate-100 rounded-lg py-2 px-3">
                    <div class="font-mono text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Type</div>
                    <div class="text-xs font-bold text-[#222a60] truncate">${typeLabel(a.type)}</div>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-2">${casesHtml}</div>
            <div class="mt-4 border-t border-slate-100 pt-4">
                <a href="${window.createAnalyseUrl}?point_id=${p.id}&lat=${p.latitude}&lng=${p.longitude}${p.cours_d_eau_id ? '&cours_d_eau_id=' + p.cours_d_eau_id : ''}"
                   class="flex items-center justify-center gap-2 bg-gradient-to-br from-[#1a7fc4] to-[#1565c0] text-white py-3.5 rounded-[14px] text-[14px] font-bold shadow-[0_4px_16px_rgba(21,101,192,0.25)] no-underline active:scale-[0.98] transition-transform">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nouvelle mesure ici
                </a>
            </div>`;
    }

    function showAuthToast() {
        if (document.getElementById('auth-toast')) return;
        const toast = document.createElement('div');
        toast.id = 'auth-toast';
        toast.innerHTML = `
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div style="flex:1;">
                    <div style="font-weight:700;font-size:13px;margin-bottom:1px;">Connexion requise</div>
                    <div style="font-size:11px;opacity:0.75;">Connectez-vous pour déposer une analyse.</div>
                </div>
                <a href="${window.loginUrl}" style="padding:7px 14px;background:white;color:#222a60;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;flex-shrink:0;">Se connecter</a>
            </div>`;
        Object.assign(toast.style, {
            position: 'absolute', bottom: '88px', left: '16px', right: '16px', zIndex: '50',
            background: 'linear-gradient(135deg,#0f1d42,#1a2a6c)', color: 'white',
            borderRadius: '14px', padding: '12px 14px', boxShadow: '0 8px 30px rgba(34,42,96,0.3)',
            fontFamily: "'Space Grotesk',sans-serif", opacity: '0', transform: 'translateY(8px)',
            transition: 'opacity 0.25s ease, transform 0.25s ease',
        });
        document.getElementById('app-shell').appendChild(toast);
        requestAnimationFrame(() => { toast.style.opacity = '1'; toast.style.transform = 'translateY(0)'; });
        setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(8px)'; setTimeout(() => toast.remove(), 280); }, 4000);
    }

    const hint = document.getElementById('tap-hint');
    const hideHint = () => { if (hint && !hint.classList.contains('fade-out')) { hint.classList.add('fade-out'); setTimeout(() => hint?.remove(), 500); } };
    setTimeout(hideHint, 3500);
    map.on('click', hideHint);

});
