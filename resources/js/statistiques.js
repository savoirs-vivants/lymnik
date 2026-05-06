import { QUALITE_CONFIG, MESURES_META } from "./core/config";
import { DEFAULT_TOOLTIP, CHART_FONTS } from "./core/chart-utils";

document.addEventListener("DOMContentLoaded", function () {
    const rawData = window.__RAW_DATA || [];
    const riverPalette = [
        "#3b82f6",
        "#f97316",
        "#10b981",
        "#8b5cf6",
        "#06b6d4",
        "#f59e0b",
        "#ef4444",
        "#ec4899",
    ];

    const charts = {};

    function fmtDate(iso) {
        return new Date(iso + "T00:00:00").toLocaleDateString("fr-FR", {
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

    // Init Charts
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
                        padding: 16,
                        font: CHART_FONTS.title,
                    },
                },
                tooltip: DEFAULT_TOOLTIP,
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: CHART_FONTS.body },
                },
                y: { grid: { borderDash: [4, 4] }, beginAtZero: true },
            },
        },
    });

    charts.bar = new Chart(document.getElementById("barChart"), {
        type: "bar",
        data: { labels: [], datasets: [] },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: DEFAULT_TOOLTIP },
            scales: {
                x: { grid: { display: false } },
                y: { grid: { borderDash: [4, 4] }, beginAtZero: true },
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
            plugins: { legend: { position: "top" }, tooltip: DEFAULT_TOOLTIP },
            scales: {
                x: { stacked: true },
                y: { stacked: true, grid: { display: false } },
            },
        },
    });

    function updateDashboard() {
        const selectedMesures = Array.from(
            document.querySelectorAll(".filter-mesure:checked"),
        ).map((el) => el.value);
        const selectedRivers = Array.from(
            document.querySelectorAll(".filter-river:checked"),
        ).map((el) => String(el.value));
        const dateStart = document.getElementById("date-start").value;
        const dateEnd = document.getElementById("date-end").value;

        const filtered = rawData.filter((item) => {
            if (!selectedRivers.includes(String(item.cours_d_eau_id)))
                return false;
            if (dateStart && item.date < dateStart) return false;
            if (dateEnd && item.date > dateEnd) return false;
            return true;
        });

        refreshTimeChart(filtered, selectedMesures);
        refreshBarChart(filtered, selectedRivers);
        refreshQualiteChart(filtered, selectedRivers);
        document.getElementById("kpi-analyses").textContent = filtered.length;
        document.getElementById("kpi-rivers").textContent = new Set(
            filtered.map((d) => d.cours_d_eau_id),
        ).size;

        const dates = filtered.map((d) => d.date).sort();
        if (dates.length) {
            const from = fmtDate(dates[0]);
            const to = fmtDate(dates[dates.length - 1]);
            document.getElementById("kpi-periode").textContent =
                dates[0] !== dates[dates.length - 1] ? `${from} → ${to}` : from;
        } else {
            document.getElementById("kpi-periode").textContent = "—";
        }
    }

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

    document
        .querySelectorAll(
            ".filter-mesure, .filter-river, #date-start, #date-end, #bar-mesure",
        )
        .forEach((el) => el.addEventListener("change", updateDashboard));
    updateDashboard();

    function refreshTimeChart(data, mesures) {
        if (!data.length || !mesures.length) {
            charts.time.data.labels = [];
            charts.time.data.datasets = [];
            charts.time.update();
            return;
        }
        const allDates = [...new Set(data.map((d) => d.date))].sort();
        charts.time.data.labels = allDates.map(fmtDate);
        charts.time.data.datasets = mesures
            .map((mesure) => {
                const meta = MESURES_META[mesure] || {};
                const values = allDates.map((dt) => {
                    const dayVals = data
                        .filter((d) => d.date === dt)
                        .map((d) => d[mesure])
                        .filter((v) => v !== null && v !== undefined);
                    return dayVals.length ? +avg(dayVals).toFixed(4) : null;
                });
                return values.every((v) => v === null)
                    ? null
                    : {
                          label: meta.label || mesure,
                          data: values,
                          borderColor: meta.color,
                          backgroundColor: meta.color + "18",
                          borderWidth: 2,
                          tension: 0.3,
                          spanGaps: true,
                      };
            })
            .filter(Boolean);
        charts.time.update();
    }

    function refreshBarChart(data, rivers) {
        const mesure = document.getElementById("bar-mesure").value;
        const meta = MESURES_META[mesure] || {};
        const labels = [],
            values = [],
            colors = [];

        rivers.forEach((riverId, idx) => {
            const rData = data.filter(
                (d) => String(d.cours_d_eau_id) === String(riverId),
            );
            if (!rData.length) return;
            const vals = rData
                .map((d) => d[mesure])
                .filter((v) => v !== null && v !== undefined);
            labels.push(rData[0].cours_d_eau_nom);
            values.push(vals.length ? +avg(vals).toFixed(3) : null);
            colors.push(riverPalette[idx % riverPalette.length]);
        });

        charts.bar.data.labels = labels;
        charts.bar.data.datasets = [
            {
                data: values,
                backgroundColor: colors.map((c) => c + "cc"),
                borderColor: colors,
                borderWidth: 2,
                borderRadius: 8,
            },
        ];
        charts.bar.update();
    }

    function refreshQualiteChart(data, rivers) {
        const ordreQ = ["tres_bon", "bon", "passable", "mediocre", "mauvais"];
        const riverIds = rivers.filter((id) =>
            data.some((d) => String(d.cours_d_eau_id) === String(id)),
        );

        charts.qualite.data.labels = riverIds.map(
            (id) =>
                data.find((d) => String(d.cours_d_eau_id) === String(id))
                    ?.cours_d_eau_nom || `#${id}`,
        );
        charts.qualite.data.datasets = ordreQ.map((q) => ({
            label: QUALITE_CONFIG[q].label,
            data: riverIds.map(
                (id) =>
                    data.filter(
                        (d) =>
                            String(d.cours_d_eau_id) === String(id) &&
                            d.qualite === q,
                    ).length,
            ),
            backgroundColor: QUALITE_CONFIG[q].hex + "cc",
            borderColor: QUALITE_CONFIG[q].hex,
            borderWidth: 1,
        }));
        charts.qualite.update();
    }

    window.exportData = function (format) {
        const dateStart = document.getElementById("date-start").value;
        const dateEnd = document.getElementById("date-end").value;
        const selectedRivers = Array.from(
            document.querySelectorAll(".filter-river:checked"),
        ).map((el) => el.value);

        const params = new URLSearchParams();
        params.append("format", format);

        if (dateStart) params.append("date_start", dateStart);
        if (dateEnd) params.append("date_end", dateEnd);

        selectedRivers.forEach((id) => params.append("rivers[]", id));

        window.location.href = "/statistiques/export?" + params.toString();
    };
});
