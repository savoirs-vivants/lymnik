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
                    const m = L.map(mapEl, {
                        center: [latVal, lngVal],
                        zoom: 14,
                        zoomControl: false,
                        attributionControl: false,
                        dragging: false,
                        scrollWheelZoom: false,
                        doubleClickZoom: false,
                    });

                    L.tileLayer(
                        "https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png",
                        {
                            maxZoom: 19,
                        },
                    ).addTo(m);

                    if (L.divIcon) {
                        L.marker([latVal, lngVal], {
                            icon: L.divIcon({
                                className: "",
                                html: `<div style="width:16px;height:16px;border-radius:50%;background:#222a60;border:3px solid white;box-shadow:0 2px 8px rgba(34,42,96,0.3);"></div>`,
                                iconSize: [16, 16],
                                iconAnchor: [8, 8],
                            }),
                        }).addTo(m);
                    }

                    m.invalidateSize();
                }, 200);
            }
        }
    };
});
