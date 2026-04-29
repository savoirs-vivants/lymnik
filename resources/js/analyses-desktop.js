// =========================================================================
// VARIABLES GLOBALES (Récupérées depuis le pont PHP/JS dans Blade)
// =========================================================================
const coursDEaux = window.__coursDEaux || [];
const qualiteConfig = window.__qualiteConfig || {};

let activeChart = null;
let activeId = null;

// =========================================================================
// HELPERS (Fonctions utilitaires)
// =========================================================================

function qualiteBadgeHtml(q) {
    const cfg = qualiteConfig[q] || qualiteConfig.tres_bon;
    return `
        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider ${cfg.bg} ${cfg.text}">
            <span class="w-1.5 h-1.5 rounded-full ${cfg.dot}"></span>${cfg.label}
        </span>
    `;
}

function printData(val) {
    if (val === undefined || val === null || val === "") {
        return '<span class="text-slate-300">—</span>';
    }
    return `<span class="font-mono font-bold text-[#222a60]">${val}</span>`;
}

function typeLabel(t) {
    return (
        {
            bandelette: "Bandelette",
            photometre: "Photomètre",
            les_deux: "Les deux",
        }[t] || t
    );
}

function pointLabel(pt) {
    if (pt.ville) return pt.ville;
    return `Point GPS (${parseFloat(pt.latitude).toFixed(3)}, ${parseFloat(pt.longitude).toFixed(3)})`;
}

// =========================================================================
// LOGIQUE D'AFFICHAGE PRINCIPALE
// =========================================================================

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

    const cfg = qualiteConfig[cd.qualite_globale] || qualiteConfig.tres_bon;
    const badge = document.getElementById("detail-qualite-badge");
    badge.className = `inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider ${cfg.bg} ${cfg.text}`;
    badge.innerHTML = `<span class="w-2 h-2 rounded-full ${cfg.dot}"></span>${cfg.label}`;

    document.getElementById("detail-meta").innerHTML =
        `<span class="font-bold text-slate-700">${cd.total_analyses}</span> analyse${cd.total_analyses > 1 ? "s" : ""} sur <span class="font-bold text-slate-700">${cd.total_points}</span> point${cd.total_points > 1 ? "s" : ""}` +
        (cd.derniere_date
            ? ` &nbsp;·&nbsp; Dernière mise à jour : ${new Date(cd.derniere_date).toLocaleDateString("fr-FR")}`
            : "");

    renderKpis(cd);
    renderChart(cd);
    renderTable(cd);
};

// =========================================================================
// SOUS-COMPOSANTS (Kpis, Graphique, Tableau)
// =========================================================================

function renderKpis(cd) {
    const ordre = ["tres_bon", "bon", "passable", "mediocre", "mauvais"];
    const container = document.getElementById("detail-kpis");

    container.innerHTML = ordre
        .map((q) => {
            const cfg = qualiteConfig[q];
            const count = cd.qualite_counts[q] || 0;
            const opacityClass = count > 0 ? "" : "opacity-50 grayscale";

            return `
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex flex-col justify-between h-full ${opacityClass} transition-all">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-2.5 h-2.5 rounded-full ${cfg.dot}"></span>
                <p class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-500">${cfg.label}</p>
            </div>
            <div>
                <p class="text-3xl font-black ${cfg.text}">${count}</p>
                <p class="text-[11px] text-slate-400 mt-1 font-mono">analyse${count > 1 ? "s" : ""}</p>
            </div>
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

    let allAnalyses = [];
    cd.points.forEach((pt) => {
        pt.analyses.forEach((a) =>
            allAnalyses.push({ ...a, pointLabel: pointLabel(pt) }),
        );
    });
    allAnalyses.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

    const labels = allAnalyses.map((a) =>
        new Date(a.created_at).toLocaleDateString("fr-FR", {
            day: "2-digit",
            month: "short",
        }),
    );
    const values = allAnalyses.map((a) => qualiteOrdre[a.qualite] ?? 0);
    const colors = allAnalyses.map(
        (a) => (qualiteConfig[a.qualite] || qualiteConfig.tres_bon).chart,
    );

    if (activeChart) {
        activeChart.destroy();
        activeChart = null;
    }

    const canvas = document.getElementById("qualite-chart");
    if (!allAnalyses.length) return;

    activeChart = new Chart(canvas.getContext("2d"), {
        type: "bar",
        data: {
            labels,
            datasets: [
                {
                    label: "Niveau Qualité",
                    data: values,
                    backgroundColor: colors,
                    borderRadius: 4,
                    barThickness: Math.min(allAnalyses.length * 5, 40),
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: "#1e293b",
                    titleFont: {
                        family: "'Space Grotesk', sans-serif",
                        size: 13,
                    },
                    bodyFont: { family: "'Space Mono', monospace", size: 12 },
                    padding: 12,
                    callbacks: {
                        label: (ctx) => {
                            const a = allAnalyses[ctx.dataIndex];
                            const cfg = qualiteConfig[a.qualite] || {};
                            return ` Résultat : ${cfg.label || a.qualite}`;
                        },
                        afterLabel: (ctx) => {
                            const a = allAnalyses[ctx.dataIndex];
                            return ` Lieu : ${a.pointLabel}`;
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { family: "'Space Mono', monospace", size: 11 },
                        color: "#64748b",
                    },
                },
                y: {
                    min: 0,
                    max: 5.5,
                    grid: { color: "#f1f5f9", borderDash: [4, 4] },
                    ticks: {
                        font: {
                            family: "'Space Mono', monospace",
                            size: 11,
                            weight: "bold",
                        },
                        color: "#94a3b8",
                        stepSize: 1,
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
    });
}

function renderTable(cd) {
    const tbody = document.getElementById("points-tbody");
    tbody.innerHTML = "";

    cd.points.forEach((pt) => {
        const analyses = pt.analyses;
        if (!analyses.length) return;

        const a = analyses[0];

        const ptLabel = pointLabel(pt);
        const coordLabel = `${parseFloat(pt.latitude).toFixed(5)}, ${parseFloat(pt.longitude).toFixed(5)}`;

        const tr = document.createElement("tr");
        tr.className =
            "hover:bg-slate-50 transition-colors group border-b border-slate-100";

        const b = a.bandelette || {};
        const p = a.photometre || {};

        const pointDisplayHTML = `
            <div class="text-sm font-bold text-[#222a60] truncate max-w-[180px]">${ptLabel}</div>
            <div class="font-mono text-[10px] text-slate-400 mt-0.5">${coordLabel}</div>
        `;

        const detailBtnHTML = `
            <button onclick='openOverlay(${pt.id})' class="flex items-center justify-center gap-2 px-3 py-1.5 rounded-lg bg-blue-50 text-[#1565c0] hover:bg-[#1565c0] hover:text-white text-xs font-bold transition-colors w-full">
                Historique ${analyses.length > 1 ? `(${analyses.length})` : ""}
            </button>
        `;

        tr.innerHTML = `
            <td class="py-4 pl-4 pr-4 align-top">
                ${pointDisplayHTML}
                <div class="font-mono text-xs text-slate-500 mt-2">${a.date || "—"} <span class="text-[10px] text-slate-400">${a.time || ""}</span></div>
            </td>
            <td class="py-4 pr-4 align-top">
                <span class="text-[11px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-1 rounded-md">${typeLabel(a.type)}</span>
            </td>
            <td class="py-4 pr-4 align-top">${qualiteBadgeHtml(a.qualite)}</td>
            <td class="py-4 pr-4 align-top text-sm">${printData(b.nitrates)}</td>
            <td class="py-4 pr-4 align-top text-sm">${printData(b.nitrites)}</td>
            <td class="py-4 pr-4 align-top text-sm">${printData(b.ph)}</td>
            <td class="py-4 pr-4 align-top text-sm">${printData(p.phosphate)}</td>
            <td class="py-4 pr-4 align-top text-sm">${printData(p.ammoniaque)}</td>
            <td class="py-4 pr-4 align-top text-center w-28">
                ${detailBtnHTML}
            </td>
        `;

        tbody.appendChild(tr);
    });
}

// =========================================================================
// OVERLAY (Historique d'un point)
// =========================================================================

window.openOverlay = function (pointId) {
    const cd = coursDEaux.find((c) => c.id === activeId);
    if (!cd) return;
    const pt = cd.points.find((p) => p.id === pointId);
    if (!pt) return;

    document.getElementById("overlay-title").textContent = pointLabel(pt);
    document.getElementById("overlay-subtitle").textContent =
        `${parseFloat(pt.latitude).toFixed(5)}, ${parseFloat(pt.longitude).toFixed(5)} · Historique complet (${pt.analyses.length} analyse${pt.analyses.length > 1 ? "s" : ""})`;

    const container = document.getElementById("overlay-content");
    container.innerHTML = pt.analyses
        .map((a, i) => {
            const b = a.bandelette || {};
            const p = a.photometre || {};
            const cfgQ = qualiteConfig[a.qualite] || qualiteConfig.tres_bon;

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
                <p class="text-[15px] font-black ${val !== null && val !== undefined && val !== "" ? "text-[#222a60]" : "text-slate-300"}">
                    ${val !== null && val !== undefined && val !== "" ? val : "—"}
                    ${val !== null && val !== undefined && val !== "" && unit ? `<span class="text-[10px] font-bold text-slate-400 ml-1">${unit}</span>` : ""}
                </p>
            </div>
        `,
                    )
                    .join("");

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
                ${
                    bandeFields.length
                        ? `
                <div>
                    <p class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-blue-500 rounded-full"></span> Bandelette JBL
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">${renderFields(bandeFields)}</div>
                </div>`
                        : ""
                }

                ${
                    photoFields.length
                        ? `
                <div>
                    <p class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-indigo-500 rounded-full"></span> Photomètre
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">${renderFields(photoFields)}</div>
                </div>`
                        : ""
                }

                ${
                    a.note
                        ? `
                <div class="bg-amber-50/50 border border-amber-100 rounded-xl p-4">
                    <p class="text-[10px] font-mono font-bold uppercase tracking-widest text-amber-600 mb-2">Observations terrain</p>
                    <p class="text-sm text-slate-700 leading-relaxed">${a.note}</p>
                </div>`
                        : ""
                }

                ${
                    a.image
                        ? `
                <div>
                    <p class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-2">Photo</p>
                    <img src="${a.image}" alt="Photo de l'analyse" class="rounded-xl max-h-48 object-cover border border-slate-100">
                </div>`
                        : ""
                }
            </div>
        </div>
        `;
        })
        .join("");

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
