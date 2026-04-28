document.addEventListener("DOMContentLoaded", function () {
    const qualiteRaw = window.dashboardData.qualite || {};
    const typeRaw = window.dashboardData.types || {};

    if (Object.keys(qualiteRaw).length > 0) {
        const ctxQualite = document
            .getElementById("qualiteChart")
            .getContext("2d");

        const colorMap = {
            tres_bon: "#3b82f6",
            bon: "#10b981",
            passable: "#fbbf24",
            mediocre: "#f97316",
            mauvais: "#ef4444",
        };

        const labelsQ = Object.keys(qualiteRaw).map((k) => {
            let clean = k.replace("_", " ");
            return clean.charAt(0).toUpperCase() + clean.slice(1);
        });

        const dataQ = Object.values(qualiteRaw);
        const bgQ = Object.keys(qualiteRaw).map(
            (k) => colorMap[k.toLowerCase()] || "#94a3b8",
        );

        new Chart(ctxQualite, {
            type: "doughnut",
            data: {
                labels: labelsQ,
                datasets: [
                    {
                        data: dataQ,
                        backgroundColor: bgQ,
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
                    legend: {
                        position: "bottom",
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { family: "'Space Grotesk', sans-serif" },
                        },
                    },
                },
            },
        });
    }

    if (Object.keys(typeRaw).length > 0) {
        const ctxType = document.getElementById("typeChart").getContext("2d");

        const labelsT = Object.keys(typeRaw).map(
            (k) => k.charAt(0).toUpperCase() + k.slice(1),
        );
        const dataT = Object.values(typeRaw);

        new Chart(ctxType, {
            type: "bar",
            data: {
                labels: labelsT,
                datasets: [
                    {
                        label: "Nombre d'analyses",
                        data: dataT,
                        backgroundColor: "#4f46e5",
                        borderRadius: 6,
                        barThickness: 30,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [4, 4] },
                        ticks: {
                            stepSize: 1,
                            font: { family: "'Space Mono', monospace" },
                        },
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: {
                                family: "'Space Grotesk', sans-serif",
                                weight: "bold",
                            },
                        },
                    },
                },
            },
        });
    }
});
