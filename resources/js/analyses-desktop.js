import { QUALITE_CONFIG, typeLabel, qualiteBadgeHtml } from "./core/config";
import { createBaseMap, createCustomMarker } from "./core/map-utils";
import { DEFAULT_TOOLTIP, CHART_FONTS } from "./core/chart-utils";

const coursDEaux = window.__coursDEaux || [];
let activeChart = null;
let activeId = null;
let overlayMap = null;
let overlayMarker = null;

function pointLabel(pt) {
    return pt.ville
        ? pt.ville
        : `GPS (${parseFloat(pt.latitude).toFixed(3)}, ${parseFloat(pt.longitude).toFixed(3)})`;
}

window.selectCoursDEau = function (id) {
    const cd = coursDEaux.find((c) => c.id === id);
    if (!cd) return;
    activeId = id;

    document.querySelectorAll(".cours-eau-item").forEach((el) => {
        const isActive = parseInt(el.dataset.id) === id;
        el.classList.toggle("bg-blue-50/80", isActive);
        el.classList.toggle("border-[#1565c0]", isActive);
        el.classList.toggle("border-transparent", !isActive);
    });

    document.getElementById("empty-state").classList.add("hidden");
    document.getElementById("detail-panel").classList.remove("hidden");
    document.getElementById("detail-nom").textContent = cd.nom;

    document.getElementById("detail-qualite-badge").outerHTML =
        qualiteBadgeHtml(cd.qualite_globale).replace(
            'class="',
            'id="detail-qualite-badge" class="',
        );

    document.getElementById("detail-meta").innerHTML =
        `<span class="font-bold text-slate-700">${cd.total_analyses}</span> analyses sur <span class="font-bold text-slate-700">${cd.total_points}</span> points`;

    renderKpis(cd);
    renderChart(cd);
    renderTable(cd);
};

function renderKpis(cd) {
    const ordre = ["tres_bon", "bon", "passable", "mediocre", "mauvais"];
    document.getElementById("detail-kpis").innerHTML = ordre
        .map((q) => {
            const cfg = QUALITE_CONFIG[q];
            const count = cd.qualite_counts[q] || 0;
            return `
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex flex-col justify-between h-full ${count > 0 ? "" : "opacity-50 grayscale"}">
            <div class="flex items-center gap-2 mb-3"><span class="w-2.5 h-2.5 rounded-full ${cfg.dot}"></span><p class="text-[10px] font-mono text-slate-500">${cfg.label}</p></div>
            <div><p class="text-3xl font-black ${cfg.text}">${count}</p></div>
        </div>`;
        })
        .join("");
}

function renderChart(cd) {
    const qualiteOrdre = {
        tres_bon: 5,
        bon: 4,
        passable: 3,
        mediocre: 2,
        mauvais: 1,
    };
    let allAnalyses = cd.points
        .flatMap((pt) =>
            pt.analyses.map((a) => ({ ...a, pointLabel: pointLabel(pt) })),
        )
        .sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

    if (activeChart) activeChart.destroy();
    if (!allAnalyses.length) return;

    activeChart = new Chart(
        document.getElementById("qualite-chart").getContext("2d"),
        {
            type: "bar",
            data: {
                labels: allAnalyses.map((a) =>
                    new Date(a.created_at).toLocaleDateString("fr-FR", {
                        day: "2-digit",
                        month: "short",
                    }),
                ),
                datasets: [
                    {
                        data: allAnalyses.map(
                            (a) => qualiteOrdre[a.qualite] ?? 0,
                        ),
                        backgroundColor: allAnalyses.map(
                            (a) =>
                                (
                                    QUALITE_CONFIG[a.qualite] ||
                                    QUALITE_CONFIG.tres_bon
                                ).hex,
                        ),
                        borderRadius: 4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: DEFAULT_TOOLTIP,
                },
                scales: {
                    y: {
                        min: 0,
                        max: 5.5,
                        ticks: {
                            callback: (v) =>
                                [
                                    "",
                                    "Mauvais",
                                    "Médiocre",
                                    "Passable",
                                    "Bon",
                                    "Très bon",
                                ][v] || "",
                        },
                    },
                },
            },
        },
    );
}

function renderTable(cd) {
    const tbody = document.getElementById("points-tbody");
    tbody.innerHTML = cd.points
        .map((pt) => {
            if (!pt.analyses.length) return "";
            const a = pt.analyses[0];
            return `
            <tr class="hover:bg-slate-50 border-b border-slate-100">
                <td class="py-4 pl-4 pr-4">
                    <div class="text-sm font-bold text-[#222a60]">${pointLabel(pt)}</div>
                    <div class="font-mono text-xs text-slate-500 mt-2">${a.date || "—"}</div>
                </td>
                <td class="py-4 pr-4"><span class="text-[11px] font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded-md">${typeLabel(a.type)}</span></td>
                <td class="py-4 pr-4">${qualiteBadgeHtml(a.qualite)}</td>
                <td class="py-4 pr-4 text-center"><button onclick='openOverlay(${pt.id})' class="bg-blue-50 text-[#1565c0] px-3 py-1.5 rounded-lg text-xs font-bold w-full">Historique</button></td>
            </tr>`;
        })
        .join("");
}

window.openOverlay = function (pointId) {
    const cd = coursDEaux.find((c) => c.id === activeId);
    if (!cd) return;
    const pt = cd.points.find((p) => p.id === pointId);
    if (!pt) return;

    document.getElementById("overlay-title").textContent = pointLabel(pt);
    document.getElementById("overlay-subtitle").textContent = `Coordonnées : ${parseFloat(pt.latitude).toFixed(5)}, ${parseFloat(pt.longitude).toFixed(5)} · Historique (${pt.analyses.length})`;

    document.getElementById("point-overlay").classList.remove("hidden");

    setTimeout(() => {
        if (!overlayMap) {
            overlayMap = createBaseMap("overlay-map", parseFloat(pt.latitude), parseFloat(pt.longitude), 15, false);
            overlayMarker = L.marker([pt.latitude, pt.longitude], { icon: createCustomMarker('#ef4444', false, 14) }).addTo(overlayMap);
        } else {
            overlayMap.setView([pt.latitude, pt.longitude], 15);
            overlayMarker.setLatLng([pt.latitude, pt.longitude]);
            overlayMap.invalidateSize();
        }
    }, 50);

    const container = document.getElementById("overlay-content");
    container.innerHTML = pt.analyses.map((a, i) => {
        const b = a.bandelette || {};
        const p = a.photometre || {};
        const cfgQ = QUALITE_CONFIG[a.qualite] || QUALITE_CONFIG.tres_bon;

        const bandeFields = [
            ["Nitrates", b.nitrates, "mg/L"], ["Nitrites", b.nitrites, "mg/L"],
            ["Dureté totale", b.durete_totale, "mg/L"], ["Dureté carb.", b.durete_carb, "mg/L"],
            ["pH", b.ph, ""], ["Chlore", b.chlore, "mg/L"],
        ].filter(([, v]) => a.type === "bandelette" || a.type === "les_deux");

        const photoFields = [
            ["Phosphate", p.phosphate, "mg/L"], ["Nitrate", p.nitrate, "mg/L"], ["Ammoniaque", p.ammoniaque, "mg/L"],
        ].filter(([, v]) => a.type === "photometre" || a.type === "les_deux");

        const renderFields = (fields) => fields.map(([label, val, unit]) => `
            <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                <p class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1">${label}</p>
                <p class="text-[15px] font-black ${val !== null && val !== undefined && val !== "" ? "text-[#222a60]" : "text-slate-300"}">
                    ${val !== null && val !== undefined && val !== "" ? val : "—"}
                    ${val !== null && val !== undefined && val !== "" && unit ? `<span class="text-[10px] font-bold text-slate-400 ml-1">${unit}</span>` : ""}
                </p>
            </div>`).join("");

        return `
        <div class="bg-white rounded-[1.5rem] border border-slate-200 shadow-sm overflow-hidden relative">
            <div class="absolute top-0 left-0 w-2 h-full ${cfgQ.bg}"></div>
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-50 ml-2">
                <div class="flex items-center gap-4">
                    <span class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-black text-slate-500">${i + 1}</span>
                    <div>
                        <p class="text-[15px] font-bold text-slate-800">${a.date || "—"} <span class="text-slate-400 font-normal text-sm ml-1">${a.time || ""}</span></p>
                        <p class="text-[11px] text-slate-400 font-mono mt-0.5">Saisi par ${a.user || "Inconnu"}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider ${cfgQ.bg} ${cfgQ.text}">
                        <span class="w-1.5 h-1.5 rounded-full ${cfgQ.dot}"></span>${cfgQ.label}
                    </span>
                </div>
            </div>

            <div class="p-6 ml-2 space-y-6">
                ${bandeFields.length ? `<div><p class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2"><span class="w-1.5 h-4 bg-blue-500 rounded-full"></span> Bandelette JBL</p><div class="grid grid-cols-2 sm:grid-cols-3 gap-3">${renderFields(bandeFields)}</div></div>` : ""}
                ${photoFields.length ? `<div><p class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2"><span class="w-1.5 h-4 bg-indigo-500 rounded-full"></span> Photomètre</p><div class="grid grid-cols-2 sm:grid-cols-3 gap-3">${renderFields(photoFields)}</div></div>` : ""}
                ${a.note ? `<div class="bg-amber-50/50 border border-amber-100 rounded-xl p-4"><p class="text-[10px] font-mono font-bold uppercase tracking-widest text-amber-600 mb-2">Observations terrain</p><p class="text-sm text-slate-700 leading-relaxed">${a.note}</p></div>` : ""}
                ${a.image ? `<div><p class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-2">Photo</p><img src="${a.image}" alt="Photo de l'analyse" class="rounded-xl max-h-48 object-cover border border-slate-100"></div>` : ""}
            </div>
        </div>`;
    }).join("");

    document.getElementById("point-overlay").classList.remove("hidden");
    document.body.style.overflow = "hidden";
};

window.closeOverlay = function () {
    document.getElementById("point-overlay").classList.add("hidden");
    document.body.style.overflow = "";
};

// =========================================================================
// EVENT LISTENERS (Recherche et Filtres)
// =========================================================================

document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("search-cours-eau");
    const filterSelect = document.getElementById("filter-qualite");

    if (searchInput && filterSelect) {
        searchInput.addEventListener("input", filterList);
        filterSelect.addEventListener("change", filterList);
    }

    function filterList() {
        const query = searchInput.value.toLowerCase().trim();
        const qualite = filterSelect.value;
        let count = 0;

        document.querySelectorAll(".cours-eau-item").forEach((el) => {
            const matchNom = !query || el.dataset.nom.includes(query);
            const matchQualite = !qualite || el.dataset.qualite === qualite;
            const visible = matchNom && matchQualite;

            el.style.display = visible ? "" : "none";
            if (visible) count++;
        });

        const countEl = document.getElementById("cours-eau-count");
        if (countEl) countEl.textContent = `${count} cours d'eau`;
    }

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeOverlay();
    });

    if (coursDEaux.length > 0) {
        selectCoursDEau(coursDEaux[0].id);
    }
});
