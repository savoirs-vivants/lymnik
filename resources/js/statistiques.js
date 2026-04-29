document.addEventListener("DOMContentLoaded", function () {
    const rawData = window.__RAW_DATA || [];
    const allRivers = window.__COURS_DEAUX || [];

    const riverPalette = [
        "#3b82f6",
        "#f97316",
        "#10b981",
        "#8b5cf6",
        "#06b6d4",
        "#f59e0b",
        "#ef4444",
        "#ec4899",
        "#14b8a6",
        "#6366f1",
        "#84cc16",
        "#a855f7",
    ];

    const mesuresMeta = {
        nitrates: { label: "Nitrates", unit: "mg/L", color: "#3b82f6" },
        nitrites: { label: "Nitrites", unit: "mg/L", color: "#8b5cf6" },
        ph: { label: "pH", unit: "", color: "#14b8a6" },
        chlore: { label: "Chlore", unit: "mg/L", color: "#06b6d4" },
        durete: { label: "Dureté totale", unit: "mg/L", color: "#f59e0b" },
        phosphate: { label: "Phosphate", unit: "mg/L", color: "#f97316" },
        ammoniaque: { label: "Ammoniaque", unit: "mg/L", color: "#ef4444" },
        nitrate_photo: {
            label: "Nitrate (photo)",
            unit: "mg/L",
            color: "#6366f1",
        },
    };

    const qualiteConfig = {
        tres_bon: { label: "Très bon", color: "#10b981" },
        bon: { label: "Bon", color: "#14b8a6" },
        passable: { label: "Passable", color: "#eab308" },
        mediocre: { label: "Médiocre", color: "#f97316" },
        mauvais: { label: "Mauvais", color: "#ef4444" },
    };

    const chartFont = { family: "'Space Grotesk', sans-serif" };
    const monoFont = { family: "'Space Mono', monospace" };
    const tooltipBase = {
        backgroundColor: "#0f172a",
        titleFont: { ...chartFont, size: 12 },
        bodyFont: { ...monoFont, size: 11 },
        padding: 12,
        cornerRadius: 10,
        borderColor: "rgba(255,255,255,0.08)",
        borderWidth: 1,
    };

    function fmtDate(iso) {
        const d = new Date(iso + "T00:00:00");
        return d.toLocaleDateString("fr-FR", {
            day: "2-digit",
            month: "short",
        });
    }

    function avg(arr) {
        const vals = arr.filter((v) => v !== null && v !== undefined);
        return vals.length
            ? vals.reduce((a, b) => a + b, 0) / vals.length
            : null;
    }

    const charts = {};

    charts.time = new Chart(document.getElementById("mainTimeChart"), {
        type: "line",
        data: { labels: [], datasets: [] },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: "index", intersect: false },
            plugins: {
                legend: {
                    position: "top",
                    labels: {
                        usePointStyle: true,
                        pointStyleWidth: 8,
                        padding: 16,
                        font: chartFont,
                    },
                },
                tooltip: {
                    ...tooltipBase,
                    callbacks: {
                        label: (ctx) => {
                            const meta = Object.values(mesuresMeta)[0];
                            const v = ctx.parsed.y;
                            if (v === null || v === undefined) return null;
                            const dsLabel = ctx.dataset.label || "";
                            const m = Object.entries(mesuresMeta).find(
                                ([, m]) => m.label === dsLabel,
                            );
                            const unit = m ? m[1].unit : "";
                            return ` ${dsLabel} : ${v % 1 === 0 ? v : v.toFixed(3)}${unit ? " " + unit : ""}`;
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: monoFont,
                        color: "#94a3b8",
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 12,
                    },
                },
                y: {
                    grid: { color: "rgba(0,0,0,0.04)", borderDash: [4, 4] },
                    ticks: { font: monoFont, color: "#94a3b8" },
                    beginAtZero: true,
                },
            },
        },
    });

    charts.bar = new Chart(document.getElementById("barChart"), {
        type: "bar",
        data: { labels: [], datasets: [] },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: tooltipBase },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: chartFont, color: "#64748b" },
                },
                y: {
                    grid: { color: "rgba(0,0,0,0.04)", borderDash: [4, 4] },
                    ticks: { font: monoFont, color: "#94a3b8" },
                    beginAtZero: true,
                },
            },
        },
    });

    charts.qualite = new Chart(document.getElementById("qualiteChart"), {
        type: "bar",
        data: { labels: [], datasets: [] },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: "y",
            plugins: {
                legend: {
                    position: "top",
                    labels: {
                        usePointStyle: true,
                        pointStyleWidth: 8,
                        padding: 14,
                        font: chartFont,
                    },
                },
                tooltip: {
                    ...tooltipBase,
                    callbacks: {
                        label: (ctx) => {
                            const v = ctx.parsed.x;
                            return v > 0
                                ? ` ${v} analyse${v > 1 ? "s" : ""} — ${ctx.dataset.label}`
                                : null;
                        },
                    },
                },
            },
            scales: {
                x: {
                    stacked: true,
                    grid: { color: "rgba(0,0,0,0.04)", borderDash: [4, 4] },
                    ticks: { font: monoFont, color: "#94a3b8", stepSize: 1 },
                },
                y: {
                    stacked: true,
                    grid: { display: false },
                    ticks: { font: chartFont, color: "#64748b" },
                },
            },
        },
    });

    document.getElementById("mesures-all").addEventListener("click", () => {
        document
            .querySelectorAll(".filter-mesure")
            .forEach((c) => (c.checked = true));
        updateDashboard();
    });
    document.getElementById("mesures-none").addEventListener("click", () => {
        document
            .querySelectorAll(".filter-mesure")
            .forEach((c) => (c.checked = false));
        updateDashboard();
    });
    document.getElementById("rivers-all").addEventListener("click", () => {
        document
            .querySelectorAll(".filter-river")
            .forEach((c) => (c.checked = true));
        updateDashboard();
    });
    document.getElementById("rivers-none").addEventListener("click", () => {
        document
            .querySelectorAll(".filter-river")
            .forEach((c) => (c.checked = false));
        updateDashboard();
    });

    document
        .querySelectorAll(".filter-mesure, .filter-river")
        .forEach((el) => el.addEventListener("change", updateDashboard));
    document
        .getElementById("date-start")
        .addEventListener("change", updateDashboard);
    document
        .getElementById("date-end")
        .addEventListener("change", updateDashboard);
    document
        .getElementById("bar-mesure")
        .addEventListener("change", updateDashboard);

    document.querySelectorAll(".filter-time-btn").forEach((btn) => {
        btn.addEventListener("click", () => {
            document.querySelectorAll(".filter-time-btn").forEach((b) => {
                b.classList.remove("bg-[#222a60]", "text-white");
                b.classList.add(
                    "bg-slate-100",
                    "text-slate-600",
                    "hover:bg-slate-200",
                );
            });
            btn.classList.remove(
                "bg-slate-100",
                "text-slate-600",
                "hover:bg-slate-200",
            );
            btn.classList.add("bg-[#222a60]", "text-white");

            const range = btn.dataset.range;
            if (range === "all") {
                document.getElementById("date-start").value = "";
                document.getElementById("date-end").value = "";
            } else {
                const d = new Date();
                d.setDate(d.getDate() - parseInt(range));
                document.getElementById("date-start").value = d
                    .toISOString()
                    .split("T")[0];
                document.getElementById("date-end").value = "";
            }
            updateDashboard();
        });
    });

    function updateDashboard() {
        const selectedMesures = Array.from(
            document.querySelectorAll(".filter-mesure:checked"),
        ).map((el) => el.value);
        const selectedRivers = Array.from(
            document.querySelectorAll(".filter-river:checked"),
        ).map((el) => parseInt(el.value));
        const dateStart = document.getElementById("date-start").value;
        const dateEnd = document.getElementById("date-end").value;

        const filtered = rawData.filter((item) => {
            if (!selectedRivers.includes(item.cours_d_eau_id)) return false;
            if (dateStart && item.date < dateStart) return false;
            if (dateEnd && item.date > dateEnd) return false;
            return true;
        });

        updateKpis(filtered, selectedRivers);
        refreshTimeChart(filtered, selectedMesures);
        refreshBarChart(filtered, selectedRivers);
        refreshQualiteChart(filtered, selectedRivers);
        updateSummary(filtered, selectedMesures, selectedRivers);
    }

    function updateKpis(data, rivers) {
        document.getElementById("kpi-analyses").textContent = data.length;
        document.getElementById("kpi-rivers").textContent = new Set(
            data.map((d) => d.cours_d_eau_id),
        ).size;

        const dates = data.map((d) => d.date).sort();
        if (dates.length) {
            const fmt = (iso) =>
                new Date(iso + "T00:00:00").toLocaleDateString("fr-FR", {
                    day: "2-digit",
                    month: "short",
                    year: "2-digit",
                });
            const from = fmt(dates[0]);
            const to = fmt(dates[dates.length - 1]);
            document.getElementById("kpi-periode").textContent =
                dates[0] !== dates[dates.length - 1] ? `${from} → ${to}` : from;
        } else {
            document.getElementById("kpi-periode").textContent = "—";
        }
    }

    function refreshTimeChart(data, mesures) {
        if (!data.length || !mesures.length) {
            charts.time.data.labels = [];
            charts.time.data.datasets = [];
            charts.time.update();
            return;
        }

        const allDates = [...new Set(data.map((d) => d.date))].sort();
        const labels = allDates.map(fmtDate);
        const datasets = [];

        mesures.forEach((mesure) => {
            const meta = mesuresMeta[mesure] || {};
            const color = meta.color || "#3b82f6";

            const values = allDates.map((dt) => {
                const dayVals = data
                    .filter((d) => d.date === dt)
                    .map((d) => d[mesure])
                    .filter((v) => v !== null && v !== undefined);
                return dayVals.length ? +avg(dayVals).toFixed(4) : null;
            });

            if (values.every((v) => v === null)) return;

            datasets.push({
                label: meta.label || mesure,
                data: values,
                borderColor: color,
                backgroundColor: color + "18",
                borderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 5,
                tension: 0.3,
                spanGaps: true,
            });
        });

        charts.time.data.labels = labels;
        charts.time.data.datasets = datasets;
        charts.time.update();
    }

    function refreshBarChart(data, rivers) {
        const mesure = document.getElementById("bar-mesure").value;
        const meta = mesuresMeta[mesure] || {};
        const labels = [],
            values = [],
            colors = [];

        rivers.forEach((riverId, idx) => {
            const rData = data.filter((d) => d.cours_d_eau_id === riverId);
            if (!rData.length) return;
            const vals = rData
                .map((d) => d[mesure])
                .filter((v) => v !== null && v !== undefined);
            if (!vals.length) {
                labels.push(rData[0].cours_d_eau_nom);
                values.push(null);
            } else {
                labels.push(rData[0].cours_d_eau_nom);
                values.push(+avg(vals).toFixed(3));
            }
            colors.push(riverPalette[idx % riverPalette.length]);
        });

        charts.bar.data.labels = labels;
        charts.bar.data.datasets = [
            {
                label: meta.label || mesure,
                data: values,
                backgroundColor: colors.map((c) => c + "cc"),
                borderColor: colors,
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            },
        ];
        charts.bar.options.plugins.tooltip.callbacks = {
            label: (ctx) => {
                const v = ctx.parsed.y;
                if (v === null || v === undefined)
                    return ` ${meta.label || mesure} : aucune donnée`;
                return ` ${meta.label || mesure} : ${v}${meta.unit ? " " + meta.unit : ""}`;
            },
        };
        charts.bar.update();
    }

    function refreshQualiteChart(data, rivers) {
        const ordreQ = ["tres_bon", "bon", "passable", "mediocre", "mauvais"];
        const riverIds = rivers.filter((id) =>
            data.some((d) => d.cours_d_eau_id === id),
        );
        const labels = riverIds.map((id) => {
            const found = data.find((d) => d.cours_d_eau_id === id);
            return found ? found.cours_d_eau_nom : `#${id}`;
        });

        charts.qualite.data.labels = labels;
        charts.qualite.data.datasets = ordreQ.map((q) => ({
            label: qualiteConfig[q].label,
            data: riverIds.map(
                (id) =>
                    data.filter(
                        (d) => d.cours_d_eau_id === id && d.qualite === q,
                    ).length,
            ),
            backgroundColor: qualiteConfig[q].color + "cc",
            borderColor: qualiteConfig[q].color,
            borderWidth: 1,
            borderRadius: 4,
        }));
        charts.qualite.update();
    }

    function updateSummary(data, mesures, rivers) {
        document.getElementById("filter-summary").textContent =
            `${data.length} analyse${data.length > 1 ? "s" : ""} · ${rivers.length} cours d'eau · ${mesures.length} mesure${mesures.length > 1 ? "s" : ""}`;
    }

    updateDashboard();
});
