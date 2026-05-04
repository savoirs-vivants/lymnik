import { QUALITE_CONFIG } from "./core/config";
import { DEFAULT_TOOLTIP, CHART_FONTS } from "./core/chart-utils";

document.addEventListener("DOMContentLoaded", function () {
    const qualiteRaw = window.dashboardData.qualite || {};
    const typeRaw = window.dashboardData.types || {};

    if (Object.keys(qualiteRaw).length > 0) {
        new Chart(document.getElementById("qualiteChart").getContext("2d"), {
            type: "doughnut",
            data: {
                labels: Object.keys(qualiteRaw).map((k) => k.replace("_", " ")),
                datasets: [
                    {
                        data: Object.values(qualiteRaw),
                        backgroundColor: Object.keys(qualiteRaw).map(
                            (k) =>
                                QUALITE_CONFIG[k.toLowerCase()]?.hex ||
                                "#94a3b8",
                        ),
                        borderWidth: 0,
                        hoverOffset: 4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "70%",
                plugins: {
                    tooltip: DEFAULT_TOOLTIP,
                    legend: {
                        position: "bottom",
                        labels: {
                            usePointStyle: true,
                            font: CHART_FONTS.title,
                        },
                    },
                },
            },
        });
    }

    if (Object.keys(typeRaw).length > 0) {
        new Chart(document.getElementById("typeChart").getContext("2d"), {
            type: "bar",
            data: {
                labels: Object.keys(typeRaw),
                datasets: [
                    {
                        label: "Analyses",
                        data: Object.values(typeRaw),
                        backgroundColor: "#4f46e5",
                        borderRadius: 6,
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
                    x: { grid: { display: false } },
                    y: { beginAtZero: true },
                },
            },
        });
    }
});
