import { createBaseMap, createCustomMarker } from './core/map-utils.js';

document.addEventListener("DOMContentLoaded", () => {
    const initializedMaps = new Set();

    window.toggleCard = function (headerElement) {
        const card = headerElement.closest(".analyse-card");
        const isOpen = card.classList.contains("open");

        document.querySelectorAll(".analyse-card.open").forEach((c) => {
            if (c !== card) c.classList.remove("open");
        });

        card.classList.toggle("open", !isOpen);

        if (!isOpen) {
            const id = card.dataset.id;
            const mapEl = document.getElementById("map-" + id);

            if (mapEl && !initializedMaps.has(id)) {
                initializedMaps.add(id);

                const latVal = parseFloat(card.dataset.lat || 48.5853);
                const lngVal = parseFloat(card.dataset.lng || 7.7512);

                setTimeout(() => {
                    const m = createBaseMap("map-" + id, latVal, lngVal, 14, false);
                    L.marker([latVal, lngVal], { icon: createCustomMarker('#222a60', false, 16) }).addTo(m);
                    m.invalidateSize();
                }, 200);
            }
        }
    };
});
