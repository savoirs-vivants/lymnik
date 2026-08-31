import L from "leaflet";
import "leaflet/dist/leaflet.css";

export function createBaseMap(
    elementId,
    lat = 48.5853,
    lng = 7.7512,
    zoom = 14,
    interactive = true,
) {
    const map = L.map(elementId, {
        center: [lat, lng],
        zoom: zoom,
        zoomControl: false,
        attributionControl: false,
        dragging: interactive,
        scrollWheelZoom: interactive ? "center" : false,
        doubleClickZoom: interactive,
    });

    L.tileLayer(
        "https://tile.openstreetmap.org/{z}/{x}/{y}.png",
        {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        },
    ).addTo(map);

    return map;
}

export function createCustomMarker(color, isSquare = false, size = 16) {
    const radius = isSquare ? "4px" : "50%";
    return L.divIcon({
        className: "",
        html: `<div style="width:${size}px;height:${size}px;border-radius:${radius};background:${color};border:2.5px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.25);"></div>`,
        iconSize: [size, size],
        iconAnchor: [size / 2, size / 2],
    });
}
