document.addEventListener('DOMContentLoaded', function () {
    const chartDataEl = document.getElementById('chart-data');
    const canvasEl = document.getElementById('capteurChart');

    if (!chartDataEl || !canvasEl) return;

    try {
        const data = {
            labels:       JSON.parse(chartDataEl.dataset.labels || '[]'),
            temp:         JSON.parse(chartDataEl.dataset.temp || '[]'),
            debit:        JSON.parse(chartDataEl.dataset.debit || '[]'),
            hauteur:      JSON.parse(chartDataEl.dataset.hauteur || '[]'),
            turbidite:    JSON.parse(chartDataEl.dataset.turbidite || '[]'),
            conductivite: JSON.parse(chartDataEl.dataset.conductivite || '[]')
        };

        if (!data.labels.length) return;

        const ctx = canvasEl.getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Température (°C)',
                        data: data.temp,
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249,115,22,0.08)',
                        borderWidth: 2,
                        pointRadius: 2,
                        tension: 0.4,
                        fill: false,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Débit (L/min)',
                        data: data.debit,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,0.08)',
                        borderWidth: 2,
                        pointRadius: 2,
                        tension: 0.4,
                        fill: false,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Hauteur (cm)',
                        data: data.hauteur,
                        borderColor: '#06b6d4',
                        backgroundColor: 'rgba(6,182,212,0.08)',
                        borderWidth: 2,
                        pointRadius: 2,
                        tension: 0.4,
                        fill: false,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Turbidité (NTU)',
                        data: data.turbidite,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245,158,11,0.08)',
                        borderWidth: 2,
                        pointRadius: 2,
                        tension: 0.4,
                        fill: false,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Conductivité (µS/cm)',
                        data: data.conductivite,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139,92,246,0.08)',
                        borderWidth: 2,
                        pointRadius: 2,
                        tension: 0.4,
                        fill: false,
                        yAxisID: 'yRight',
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyleWidth: 8,
                            padding: 16,
                            font: { family: "'Space Grotesk', sans-serif", size: 12 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family: "'Space Grotesk', sans-serif", size: 12 },
                        bodyFont: { family: "'Space Mono', monospace", size: 11 },
                        padding: 12,
                        cornerRadius: 10,
                        borderColor: 'rgba(255,255,255,0.08)',
                        borderWidth: 1,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: "'Space Mono', monospace", size: 10 },
                            color: '#94a3b8',
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 10,
                        }
                    },
                    y: {
                        type: 'linear',
                        position: 'left',
                        grid: { color: 'rgba(0,0,0,0.04)', borderDash: [4, 4] },
                        ticks: { font: { family: "'Space Mono', monospace", size: 10 }, color: '#94a3b8' },
                        title: { display: true, text: 'Valeurs', font: { size: 10, family: "'Space Mono', monospace" }, color: '#94a3b8' }
                    },
                    yRight: {
                        type: 'linear',
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: { font: { family: "'Space Mono', monospace", size: 10 }, color: '#8b5cf6' },
                        title: { display: true, text: 'µS/cm', font: { size: 10, family: "'Space Mono', monospace" }, color: '#8b5cf6' }
                    }
                }
            }
        });
    } catch (e) {
        console.error("Erreur de parsing des données du graphique capteur", e);
    }
});
