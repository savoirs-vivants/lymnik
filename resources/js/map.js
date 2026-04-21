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

    const sheet   = document.getElementById('bottom-sheet');
    const nav     = document.getElementById('bottom-nav');
    const hint    = document.getElementById('tap-hint');
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
    }

    marker.on('click', () => {
        openSheet();
    });

    document.getElementById('sheet-close-btn').addEventListener('click', closeSheet);

    const hideHint = () => {
        if(hint && !hint.classList.contains('fade-out')) {
            hint.classList.add('fade-out');
            setTimeout(() => hint.remove(), 500);
        }
    };
    setTimeout(hideHint, 3500);
    map.on('click', hideHint);

    let startY = 0, dragging = false;

    sheet.addEventListener('touchstart', e => {
        startY   = e.touches[0].clientY;
        dragging = true;
    }, { passive: true });

    sheet.addEventListener('touchmove', e => {
        if (!dragging) return;
        const delta = e.touches[0].clientY - startY;
        if (delta > 0) sheet.style.transform = `translateY(${delta}px)`;
    }, { passive: true });

    sheet.addEventListener('touchend', e => {
        dragging = false;
        const delta = e.changedTouches[0].clientY - startY;
        sheet.style.transform = '';
        if (delta > 80) closeSheet();
    });

    document.querySelectorAll('.pill').forEach(pill => {
        pill.addEventListener('click', () => {
            pill.classList.toggle('active');
        });
    });

    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', e => {
            e.preventDefault();
            document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
            item.classList.add('active');
        });
    });

});
