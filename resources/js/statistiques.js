import { QUALITE_CONFIG, MESURES_META } from "./core/config";
import { DEFAULT_TOOLTIP, CHART_FONTS } from "./core/chart-utils";

document.addEventListener("DOMContentLoaded", function () {
    if (!window.__RAW_DATA) return;

    const rawData       = window.__RAW_DATA       || [];
    const campagnesTree = window.__CAMPAGNES_TREE || [];

    const riverPalette  = ["#3b82f6","#f97316","#10b981","#8b5cf6","#06b6d4","#f59e0b","#ef4444","#ec4899","#14b8a6","#f43f5e"];
    const sourcePalette = ["#6366f1","#f59e0b","#10b981","#ef4444","#8b5cf6","#06b6d4","#f97316","#ec4899","#3b82f6","#a16207"];

    let selectedLocItems = new Set();
    let filterSources    = new Set();
    let filterDateStart  = "";
    let filterDateEnd    = "";

    const charts = {};

    // ── Helpers ─────────────────────────────────────────────────────

    function avg(arr) {
        const v = arr.map(x => parseFloat(x)).filter(x => !isNaN(x));
        return v.length ? v.reduce((a, b) => a + b, 0) / v.length : null;
    }

    function fmtDate(iso) {
        return new Date(iso + "T00:00:00").toLocaleDateString("fr-FR", { day: "2-digit", month: "short" });
    }

    function matchesLoc(item, key) {
        if (key.startsWith("analyse-"))
            return String(item.id) === key.slice(8);
        if (key.startsWith("river-"))
            return String(item.cours_d_eau_id) === key.slice(6);
        if (key.startsWith("ville-")) {
            const rest = key.slice(6), sep = rest.indexOf("::");
            return String(item.cours_d_eau_id) === rest.slice(0, sep)
                && (item.ville || "Non définie") === rest.slice(sep + 2);
        }
        return false;
    }

    function locLabel(key) {
        if (key.startsWith("analyse-")) {
            const item = rawData.find(d => String(d.id) === key.slice(8));
            return item?.analyse_nom || "Analyse #" + key.slice(8);
        }
        if (key.startsWith("river-")) {
            const item = rawData.find(d => String(d.cours_d_eau_id) === key.slice(6));
            return item?.cours_d_eau_nom || "#" + key.slice(6);
        }
        if (key.startsWith("ville-")) {
            const rest = key.slice(6);
            return rest.slice(rest.indexOf("::") + 2);
        }
        return key;
    }

    function matchesSource(item, src) {
        if (src.startsWith("campagne-"))
            return String(item.campagne_id) === src.slice(9);
        if (src.startsWith("groupe-")) {
            const rest = src.slice(7), d = rest.indexOf("-");
            return String(item.campagne_id) === rest.slice(0, d)
                && String(item.id_groupe)   === rest.slice(d + 1);
        }
        return false;
    }

    function sourceLabel(src) {
        if (src.startsWith("campagne-")) {
            const c = campagnesTree.find(x => String(x.id) === src.slice(9));
            return c ? c.nom : "Campagne #" + src.slice(9);
        }
        if (src.startsWith("groupe-")) {
            const rest = src.slice(7), d = rest.indexOf("-");
            const campId = rest.slice(0, d), gId = rest.slice(d + 1);
            const c = campagnesTree.find(x => String(x.id) === campId);
            const nom = c ? c.nom : "Campagne #" + campId;
            return +gId === 0 ? nom + " · Sans groupe" : nom + " · Groupe " + String.fromCharCode(64 + +gId);
        }
        return src;
    }

    // ── Filtrage ────────────────────────────────────────────────────

    function applyFilters() {
        return rawData.filter(item => {
            if (selectedLocItems.size > 0 && ![...selectedLocItems].some(k => matchesLoc(item, k))) return false;
            if (filterSources.size > 0    && ![...filterSources].some(s => matchesSource(item, s))) return false;
            if (filterDateStart && item.date < filterDateStart) return false;
            if (filterDateEnd   && item.date > filterDateEnd)   return false;
            return true;
        });
    }

    function computeGroups(filtered) {
        if (filterSources.size > 0) {
            return [...filterSources].map((src, i) => ({
                key: src, label: sourceLabel(src), color: sourcePalette[i % sourcePalette.length],
                items: filtered.filter(item => matchesSource(item, src)),
            })).filter(g => g.items.length > 0);
        }
        if (selectedLocItems.size > 0) {
            return [...selectedLocItems].map((key, i) => ({
                key, label: locLabel(key), color: riverPalette[i % riverPalette.length],
                items: filtered.filter(item => matchesLoc(item, key)),
            })).filter(g => g.items.length > 0);
        }
        const ids = [...new Set(filtered.map(d => d.cours_d_eau_id))];
        return ids.map((id, i) => ({
            key: "river-" + id,
            label: filtered.find(d => d.cours_d_eau_id === id)?.cours_d_eau_nom || "#" + id,
            color: riverPalette[i % riverPalette.length],
            items: filtered.filter(d => d.cours_d_eau_id === id),
        }));
    }

    // ── Init graphiques ─────────────────────────────────────────────

    const timeEl = document.getElementById("mainTimeChart");
    if (timeEl) charts.time = new Chart(timeEl, {
        type: "line", data: { labels: [], datasets: [] },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode: "index", intersect: false },
            plugins: { legend: { position: "top", labels: { usePointStyle: true, padding: 14, font: CHART_FONTS.title } }, tooltip: DEFAULT_TOOLTIP },
            scales: { x: { grid: { display: false }, ticks: { font: CHART_FONTS.body } }, y: { grid: { borderDash: [4, 4] }, beginAtZero: true } },
        },
    });

    const barEl = document.getElementById("barChart");
    if (barEl) charts.bar = new Chart(barEl, {
        type: "bar", data: { labels: [], datasets: [] },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: true, position: "top", labels: { usePointStyle: true, padding: 12, font: CHART_FONTS.title } }, tooltip: DEFAULT_TOOLTIP },
            scales: { x: { grid: { display: false } }, y: { grid: { borderDash: [4, 4] }, beginAtZero: true } },
        },
    });

    const qualEl = document.getElementById("qualiteChart");
    if (qualEl) charts.qualite = new Chart(qualEl, {
        type: "bar", data: { labels: [], datasets: [] },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: "top", labels: { usePointStyle: true, padding: 12, font: CHART_FONTS.title } },
                tooltip: DEFAULT_TOOLTIP
            },
            scales: {
                x: {
                    stacked: true,
                    grid: { display: false }
                },
                y: {
                    stacked: true,
                    grid: { borderDash: [4, 4] }
                }
            },
        },
    });

    // ── Mise à jour ──────────────────────────────────────────────────

    function updateDashboard() {
        const filtered = applyFilters();
        const mesures  = [...document.querySelectorAll(".filter-mesure:checked")].map(el => el.value);
        const groups   = computeGroups(filtered);

        // KPIs
        document.getElementById("kpi-analyses").textContent = filtered.length;
        const dates = filtered.map(d => d.date).sort();
        document.getElementById("kpi-periode").textContent  = dates.length
            ? (dates[0] === dates[dates.length-1] ? fmtDate(dates[0]) : fmtDate(dates[0]) + " → " + fmtDate(dates[dates.length-1]))
            : "—";

        // Titres
        const gl = filterSources.size > 0 ? "source/groupe"
            : selectedLocItems.size > 0 ? locLabel([...selectedLocItems][0]).length < 20 ? "sélection" : "sélection"
            : "cours d'eau";
        const bt = document.getElementById("bar-chart-title");
        const bs = document.getElementById("bar-chart-subtitle");
        if (bt) bt.textContent = "Comparaison par " + (filterSources.size > 0 ? "source/groupe" : selectedLocItems.size > 0 ? "localisation" : "cours d'eau");
        if (bs) bs.textContent = groups.length + " groupe(s) · " + mesures.length + " mesure(s)";
        const ts = document.getElementById("time-chart-subtitle");
        if (ts) ts.textContent = "Moyenne journalière · " + filtered.length + " analyse(s)";
        const qs = document.getElementById("qualite-chart-subtitle");
        if (qs) qs.textContent = "Répartition de la qualité · " + groups.length + " groupe(s)";

        // Badge compteur filtres
        const cnt = selectedLocItems.size + filterSources.size;
        const badge = document.getElementById("filter-count");
        if (badge) {
            if (cnt > 0) { badge.textContent = cnt; badge.classList.remove("hidden"); }
            else badge.classList.add("hidden");
        }

        // Résumé
        const sum = document.getElementById("filter-summary");
        if (sum) {
            const p = [];
            if (selectedLocItems.size > 0) p.push(selectedLocItems.size + " loc.");
            if (filterSources.size > 0)    p.push(filterSources.size + " source(s)");
            if (filterDateStart || filterDateEnd) p.push("Période filtrée");
            sum.textContent = p.join(" · ") || filtered.length + " analyses";
        }

        refreshTimeChart(filtered, mesures);
        refreshBarChart(groups, mesures);
        refreshQualiteChart(groups);
    }

    // ── Renderers ────────────────────────────────────────────────────

    function refreshTimeChart(data, mesures) {
        if (!charts.time) return;
        if (!data.length || !mesures.length) { charts.time.data.labels = []; charts.time.data.datasets = []; charts.time.update(); return; }
        const allDates = [...new Set(data.map(d => d.date))].sort();
        charts.time.data.labels   = allDates.map(fmtDate);
        charts.time.data.datasets = mesures.map(m => {
            const meta = MESURES_META[m] || {};
            const vals = allDates.map(dt => {
                const v = data.filter(d => d.date === dt).map(d => d[m]).filter(x => x != null);
                return v.length ? +avg(v).toFixed(4) : null;
            });
            return vals.every(v => v === null) ? null : {
                label: meta.label || m, data: vals,
                borderColor: meta.color, backgroundColor: (meta.color || "#ccc") + "18",
                borderWidth: 2, tension: 0.3, spanGaps: true,
            };
        }).filter(Boolean);
        charts.time.update();
    }

    function refreshBarChart(groups, mesures) {
        if (!charts.bar) return;
        if (!groups.length || !mesures.length) { charts.bar.data.labels = []; charts.bar.data.datasets = []; charts.bar.update(); return; }
        charts.bar.data.labels   = groups.map(g => g.label);
        charts.bar.data.datasets = mesures.map(m => {
            const meta = MESURES_META[m] || {}, color = meta.color || "#94a3b8";
            return {
                label: meta.label || m,
                data:  groups.map(g => {
                    const v = g.items.map(d => d[m]).filter(x => x !== null && x !== "");
                    const moyenne = avg(v);
                    return moyenne !== null ? +(moyenne.toFixed(3)) : null;
                }),
                backgroundColor: color + "bb", borderColor: color, borderWidth: 2, borderRadius: 6,
            };
        });
        charts.bar.options.plugins.legend.display = mesures.length > 1;
        charts.bar.update();
    }

    function refreshQualiteChart(groups) {
        if (!charts.qualite) return;
        if (!groups.length) { charts.qualite.data.labels = []; charts.qualite.data.datasets = []; charts.qualite.update(); return; }
        const ordreQ = ["tres_bon","bon","passable","mediocre","mauvais"];
        charts.qualite.data.labels   = groups.map(g => g.label);
        charts.qualite.data.datasets = ordreQ.map(q => {
            const cfg = QUALITE_CONFIG[q] || { label: q, hex: "#ccc" };
            return { label: cfg.label, data: groups.map(g => g.items.filter(d => d.qualite === q).length), backgroundColor: cfg.hex + "cc", borderColor: cfg.hex, borderWidth: 1 };
        });
        charts.qualite.update();
    }

    // ── Écouteurs ─────────────────────────────────────────────────────

    document.querySelectorAll(".filter-loc").forEach(el =>
        el.addEventListener("change", function () {
            this.checked ? selectedLocItems.add(this.value) : selectedLocItems.delete(this.value);
            updateDashboard();
        })
    );

    document.querySelectorAll(".filter-source").forEach(el =>
        el.addEventListener("change", function () {
            this.checked ? filterSources.add(this.value) : filterSources.delete(this.value);
            updateDashboard();
        })
    );

    document.querySelectorAll(".filter-mesure").forEach(el => el.addEventListener("change", updateDashboard));

    document.getElementById("mesures-all")?.addEventListener("click", () => {
        document.querySelectorAll(".filter-mesure").forEach(c => c.checked = true); updateDashboard();
    });
    document.getElementById("mesures-none")?.addEventListener("click", () => {
        document.querySelectorAll(".filter-mesure").forEach(c => c.checked = false); updateDashboard();
    });

    document.querySelectorAll(".filter-time-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            document.querySelectorAll(".filter-time-btn").forEach(b => {
                b.style.background = "#f1f5f9"; b.style.color = "#64748b";
            });
            btn.style.background = "#222a60"; btn.style.color = "#fff";
            const range = btn.dataset.range;
            if (range === "all") {
                filterDateStart = ""; filterDateEnd = "";
                document.getElementById("date-start").value = "";
                document.getElementById("date-end").value   = "";
            } else {
                const d = new Date(); d.setDate(d.getDate() - parseInt(range));
                filterDateStart = d.toISOString().split("T")[0];
                filterDateEnd   = "";
                document.getElementById("date-start").value = filterDateStart;
                document.getElementById("date-end").value   = "";
            }
            updateDashboard();
        });
    });

    document.getElementById("date-start")?.addEventListener("change", function () { filterDateStart = this.value; updateDashboard(); });
    document.getElementById("date-end")?.addEventListener("change",   function () { filterDateEnd   = this.value; updateDashboard(); });

    // ── Fonctions globales ─────────────────────────────────────────────

    window.openFilters = function () {
        const panel    = document.getElementById("filter-panel");
        const backdrop = document.getElementById("filter-backdrop");
        panel.style.display    = "flex";
        backdrop.style.display = "block";
        requestAnimationFrame(() => requestAnimationFrame(() => {
            panel.style.transform = "translateX(0)";
        }));
    };

    window.closeFilters = function () {
        const panel    = document.getElementById("filter-panel");
        const backdrop = document.getElementById("filter-backdrop");
        panel.style.transform = "translateX(100%)";
        backdrop.style.display = "none";
        panel.addEventListener("transitionend", () => { panel.style.display = "none"; }, { once: true });
    };

    window.resetFilters = function () {
        selectedLocItems.clear(); filterSources.clear();
        filterDateStart = ""; filterDateEnd = "";
        document.querySelectorAll(".filter-loc, .filter-source").forEach(c => c.checked = false);
        document.querySelectorAll(".filter-mesure").forEach(c => c.checked = true);
        document.querySelectorAll(".filter-time-btn").forEach(b => { b.style.background = "#f1f5f9"; b.style.color = "#64748b"; });
        const allBtn = document.querySelector('.filter-time-btn[data-range="all"]');
        if (allBtn) { allBtn.style.background = "#222a60"; allBtn.style.color = "#fff"; }
        document.getElementById("date-start").value = "";
        document.getElementById("date-end").value   = "";
        updateDashboard();
    };

    window.locSelectAll = function () {
        document.querySelectorAll(".filter-loc").forEach(c => { c.checked = true; selectedLocItems.add(c.value); });
        updateDashboard();
    };
    window.locSelectNone = function () {
        document.querySelectorAll(".filter-loc").forEach(c => c.checked = false);
        selectedLocItems.clear(); updateDashboard();
    };

    window.toggleTree = function (btn) {
        const group    = btn.parentElement.parentElement;
        const children = group.querySelector(":scope > .tree-children");
        const chevron  = btn.querySelector(".tree-chevron");
        if (!children) return;
        const isHidden = children.style.display === "none" || children.style.display === "";
        children.style.display = isHidden ? "block" : "none";
        if (chevron) chevron.style.transform = isHidden ? "rotate(90deg)" : "rotate(0deg)";
    };

    window.toggleSection = function (btn) {
        const body    = btn.nextElementSibling;
        const chevron = btn.querySelector(".section-chevron");
        if (!body) return;
        const isHidden = body.style.display === "none";
        body.style.display = isHidden ? "block" : "none";
        if (chevron) chevron.style.transform = isHidden ? "rotate(180deg)" : "rotate(0deg)";
    };

    window.exportChartAsPng = function (canvasId, prefixName) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        const exp = document.createElement("canvas");
        exp.width = canvas.width;
        exp.height = canvas.height;

        const ctx = exp.getContext("2d");
        ctx.fillStyle = "#ffffff";
        ctx.fillRect(0, 0, exp.width, exp.height);
        ctx.drawImage(canvas, 0, 0);

        const link = document.createElement("a");
        link.download = prefixName + "_" + new Date().toISOString().slice(0, 10) + ".png";
        link.href = exp.toDataURL("image/png", 1.0);
        link.click();
    };

    window.exportData = function (format) {
        const params = new URLSearchParams();
        params.append("format", format);
        if (filterDateStart) params.append("date_start", filterDateStart);
        if (filterDateEnd)   params.append("date_end",   filterDateEnd);
        const riverIds = selectedLocItems.size > 0
            ? [...new Set([...selectedLocItems].filter(k => k.startsWith("river-")).map(k => k.slice(6)))]
            : [...new Set(rawData.map(d => String(d.cours_d_eau_id)))];
        riverIds.forEach(id => params.append("rivers[]", id));
        window.location.href = "/statistiques/export?" + params.toString();
    };

    updateDashboard();
});
