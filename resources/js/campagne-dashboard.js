import { QUALITE_CONFIG, typeLabel, qualiteBadgeHtml } from "./core/config";
import { createBaseMap, createCustomMarker } from "./core/map-utils";

const campagnes = window.__campagnes || [];
let activeGroupe = null;
let overlayMap = null;
let overlayMarker = null;

document.querySelectorAll(".toggle-accordion").forEach((btn) => {
    btn.addEventListener("click", () => {
        const container = btn.nextElementSibling;
        const chevron = btn.querySelector(".chevron");
        container.classList.toggle("hidden");
        if (!container.classList.contains("hidden")) {
            chevron.classList.add("rotate-180");
        } else {
            chevron.classList.remove("rotate-180");
        }
    });
});

window.selectGroupe = function (campagneId, groupeId, campagneNom) {
    const campagne = campagnes.find((c) => c.id === campagneId);
    if (!campagne) return;

    const groupe = campagne.groupes.find((g) => g.id_groupe === groupeId);
    if (!groupe) return;

    activeGroupe = groupe;

    document.querySelectorAll(".groupe-item").forEach((el) => {
        const isMatch =
            parseInt(el.dataset.campagneId) === campagneId &&
            parseInt(el.dataset.groupeId) === groupeId;
        el.classList.toggle("bg-blue-50/80", isMatch);
        el.classList.toggle("border-[#1565c0]", isMatch);
        el.classList.toggle("border-transparent", !isMatch);
    });

    document.getElementById("empty-state").classList.add("hidden");
    document.getElementById("detail-panel").classList.remove("hidden");

    document.getElementById("sidebar-list").classList.add("max-lg:hidden");
    document
        .getElementById("analyses-detail")
        .classList.remove("max-lg:hidden");

    document.getElementById("detail-nom").textContent =
        `${campagneNom} - ${groupe.label}`;
    document.getElementById("detail-qualite-badge").innerHTML =
        qualiteBadgeHtml(groupe.qualite_globale);
    document.getElementById("detail-meta").innerHTML =
        `<span class="font-bold text-slate-700">${groupe.total_analyses}</span> analyses sur <span class="font-bold text-slate-700">${groupe.total_points}</span> points`;

    renderKpis(groupe);
    renderTable(groupe);
};

window.backToList = function () {
    document.getElementById("sidebar-list").classList.remove("max-lg:hidden");
    document.getElementById("analyses-detail").classList.add("max-lg:hidden");
};

function renderKpis(groupe) {
    const ordre = ["tres_bon", "bon", "passable", "mediocre", "mauvais"];
    document.getElementById("detail-kpis").innerHTML = ordre
        .map((q) => {
            const cfg = window.__qualiteConfig[q];
            const count = groupe.qualite_counts[q] || 0;
            return `
        <div class="bg-white rounded-2xl border border-slate-100 p-4 ${count > 0 ? "" : "opacity-50 grayscale"}">
            <div class="flex items-center gap-2 mb-2"><span class="w-2.5 h-2.5 rounded-full ${cfg.dot}"></span><p class="text-[10px] font-mono text-slate-500">${cfg.label}</p></div>
            <p class="text-2xl sm:text-3xl font-black ${cfg.text}">${count}</p>
        </div>`;
        })
        .join("");
}

function renderTable(groupe) {
    const tbody = document.getElementById("points-tbody");
    tbody.innerHTML = groupe.points
        .map((pt) => {
            if (!pt.analyses.length) return "";
            const a = pt.analyses[0];
            return `
        <tr class="hover:bg-slate-50 border-b border-slate-100">
            <td class="py-3 sm:py-4 pl-4 pr-4">
                <div class="text-sm font-bold text-[#222a60]">${pt.ville}</div>
                <div class="font-mono text-xs text-slate-500 mt-1">GPS: ${pt.latitude.toFixed(3)}, ${pt.longitude.toFixed(3)}</div>
            </td>
            <td class="py-3 sm:py-4 pr-4"><span class="text-[11px] font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded-md whitespace-nowrap">${typeLabel(a.type)}</span></td>
            <td class="py-3 sm:py-4 pr-4">${qualiteBadgeHtml(a.qualite)}</td>
            <td class="py-3 sm:py-4 pr-4 text-center"><button onclick='openOverlay(${pt.id})' class="bg-blue-50 text-[#1565c0] px-3 py-1.5 rounded-lg text-xs font-bold w-full sm:w-auto">Historique</button></td>
        </tr>`;
        })
        .join("");
}

window.openOverlay = function (pointId) {
    if (!activeGroupe) return;
    const pt = activeGroupe.points.find((p) => p.id === pointId);
    if (!pt) return;

    document.getElementById("overlay-title").textContent = pt.ville;
    document.getElementById("overlay-subtitle").textContent =
        `Historique du groupe (${pt.analyses.length} analyses)`;

    document.getElementById("point-overlay").classList.remove("hidden");

    setTimeout(() => {
        if (!overlayMap) {
            overlayMap = createBaseMap(
                "overlay-map",
                pt.latitude,
                pt.longitude,
                15,
                false,
            );
            overlayMarker = L.marker([pt.latitude, pt.longitude], {
                icon: createCustomMarker("#ef4444", false, 14),
            }).addTo(overlayMap);
        } else {
            overlayMap.setView([pt.latitude, pt.longitude], 15);
            overlayMarker.setLatLng([pt.latitude, pt.longitude]);
            overlayMap.invalidateSize();
        }
    }, 50);

    document.getElementById("overlay-content").innerHTML = pt.analyses
        .map((a, i) => {
            const b = a.bandelette || {};
            const p = a.photometre || {};
            const cfgQ =
                window.__qualiteConfig[a.qualite] ||
                window.__qualiteConfig.tres_bon;

            const bandeFields = [
                ["Nitrates", b.nitrates, "mg/L"],
                ["Nitrites", b.nitrites, "mg/L"],
                ["Dureté totale", b.durete_totale, "mg/L"],
                ["Dureté carb.", b.durete_carb, "mg/L"],
                ["pH", b.ph, ""],
                ["Chlore", b.chlore, "mg/L"],
            ].filter(
                ([, v]) => a.type === "bandelette" || a.type === "les_deux",
            );

            const photoFields = [
                ["Phosphate", p.phosphate, "mg/L"],
                ["Nitrate", p.nitrate, "mg/L"],
                ["Ammoniaque", p.ammoniaque, "mg/L"],
            ].filter(
                ([, v]) => a.type === "photometre" || a.type === "les_deux",
            );

            const renderFields = (fields) =>
                fields
                    .map(
                        ([label, val, unit]) => `
            <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                <p class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1">${label}</p>
                <p class="text-[15px] font-black ${val !== null && val !== "" ? "text-[#222a60]" : "text-slate-300"}">${val || "—"} ${val !== null && val !== "" && unit ? `<span class="text-[10px] font-bold text-slate-400 ml-1">${unit}</span>` : ""}</p>
            </div>`,
                    )
                    .join("");

            return `
        <div class="bg-white rounded-[1.5rem] border border-slate-200 shadow-sm overflow-hidden relative">
            <div class="absolute top-0 left-0 w-2 h-full ${cfgQ.bg}"></div>
            <div class="flex items-center justify-between px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-50 ml-2">
                <div class="flex items-center gap-3 sm:gap-4">
                    <span class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-black text-slate-500 shrink-0">${i + 1}</span>
                    <div class="min-w-0">
                        <p class="text-[13px] sm:text-[15px] font-bold text-slate-800 truncate">${a.date || "—"} <span class="text-slate-400 font-normal text-xs sm:text-sm ml-1">${a.time || ""}</span></p>
                        <p class="text-[10px] sm:text-[11px] text-[#1565c0] font-mono font-bold mt-0.5 truncate">Saisi par ${a.saisi_par}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 pl-2">
                    <span class="inline-flex items-center gap-1.5 px-2 py-1 sm:px-2.5 sm:py-1 rounded-md text-[9px] sm:text-[10px] font-bold uppercase tracking-wider whitespace-nowrap ${cfgQ.bg} ${cfgQ.text}"><span class="w-1.5 h-1.5 rounded-full ${cfgQ.dot}"></span><span class="hidden sm:inline">${cfgQ.label}</span></span>
                </div>
            </div>
            <div class="p-4 sm:p-6 ml-2 space-y-4 sm:space-y-6">
                ${bandeFields.length ? `<div><p class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2"><span class="w-1.5 h-4 bg-blue-500 rounded-full"></span> Bandelette JBL</p><div class="grid grid-cols-2 gap-3">${renderFields(bandeFields)}</div></div>` : ""}
                ${photoFields.length ? `<div><p class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2"><span class="w-1.5 h-4 bg-indigo-500 rounded-full"></span> Photomètre</p><div class="grid grid-cols-2 gap-3">${renderFields(photoFields)}</div></div>` : ""}
                ${a.note ? `<div class="bg-amber-50/50 border border-amber-100 rounded-xl p-4"><p class="text-[10px] font-mono font-bold uppercase tracking-widest text-amber-600 mb-2">Observations terrain</p><p class="text-sm text-slate-700 leading-relaxed">${a.note}</p></div>` : ""}
                ${a.image ? `<div><img src="${a.image}" class="rounded-xl w-full max-h-48 object-cover border border-slate-100"></div>` : ""}
            </div>
        </div>`;
        })
        .join("");
};

window.closeOverlay = function () {
    document.getElementById("point-overlay").classList.add("hidden");
};
