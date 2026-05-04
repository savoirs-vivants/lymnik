// Page de comparaison groupes/participants

const groupesData   = window.__groupesData   || [];
const groupColors   = window.__groupColors   || [];
const qualiteLabels = window.__qualiteLabels || {};

const QUALITE_NUM = { tres_bon: 5, bon: 4, passable: 3, mediocre: 2, mauvais: 1 };
const QUALITE_ORDER = ['tres_bon', 'bon', 'passable', 'mediocre', 'mauvais'];

let chartQualite  = null;
let chartParams   = null;
let chartTimeline = null;

// ─── Graphique répartition qualité par groupe (barres groupées) ───────────────
function buildQualiteChart() {
    const ctx = document.getElementById('chart-qualite-compare')?.getContext('2d');
    if (!ctx) return;

    const datasets = groupesData.map((g, i) => ({
        label:           g.label,
        data:            QUALITE_ORDER.map(q => g.analyses.qualite_counts[q] || 0),
        backgroundColor: groupColors[i % groupColors.length] + 'cc',
        borderColor:     groupColors[i % groupColors.length],
        borderWidth:     1.5,
        borderRadius:    4,
    }));

    chartQualite = new Chart(ctx, {
        type: 'bar',
        data: {
            labels:   QUALITE_ORDER.map(q => qualiteLabels[q] || q),
            datasets,
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } } },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,0.04)' } },
            },
        },
    });
}

// ─── Graphique paramètres (radar ou barres selon nb groupes) ──────────────────
let currentParam = null;

window.showParam = function (param) {
    currentParam = param;
    document.querySelectorAll('.param-btn').forEach(btn => {
        const active = btn.dataset.param === param;
        btn.classList.toggle('bg-[#222a60]', active);
        btn.classList.toggle('text-white', active);
        btn.classList.toggle('border-[#222a60]', active);
    });

    if (chartParams) { chartParams.destroy(); chartParams = null; }

    const ctx = document.getElementById('chart-params')?.getContext('2d');
    if (!ctx) return;

    const data = groupesData.map(g => g.analyses.param_means[param] ?? null);
    const seuils = {
        nitrites:   { tres_bon: 0.03, bon: 0.3, passable: 0.5, mediocre: 1.0 },
        nitrates:   { tres_bon: 2,    bon: 10,  passable: 25,  mediocre: 50 },
        nitrate:    { tres_bon: 2,    bon: 10,  passable: 25,  mediocre: 50 },
        phosphate:  { tres_bon: 0.05, bon: 0.2, passable: 0.5, mediocre: 1.0 },
        chlore:     { tres_bon: 25,   bon: 50,  passable: 100, mediocre: 250 },
        ammoniaque: { tres_bon: 0.1,  bon: 0.5, passable: 2.0, mediocre: 5.0 },
    };

    const datasets = [{
        label:           param,
        data,
        backgroundColor: groupColors.map((c, i) => c + 'cc').slice(0, groupesData.length),
        borderColor:     groupColors.slice(0, groupesData.length),
        borderWidth:     1.5,
        borderRadius:    6,
    }];

    // Lignes de seuil
    const annotations = {};
    if (seuils[param]) {
        const s = seuils[param];
        const seuilDefs = [
            { val: s.tres_bon, label: 'Très bon',  color: '#10b981' },
            { val: s.bon,      label: 'Bon',       color: '#14b8a6' },
            { val: s.passable, label: 'Passable',  color: '#eab308' },
            { val: s.mediocre, label: 'Médiocre',  color: '#f97316' },
        ];
        seuilDefs.forEach((sd, idx) => {
            annotations[`seuil_${idx}`] = {
                type: 'line',
                yMin: sd.val,
                yMax: sd.val,
                borderColor: sd.color,
                borderWidth: 1.5,
                borderDash: [4, 4],
                label: {
                    content: sd.label,
                    enabled: true,
                    position: 'end',
                    font: { size: 10 },
                    color: sd.color,
                    backgroundColor: 'transparent',
                },
            };
        });
    }

    chartParams = new Chart(ctx, {
        type: 'bar',
        data: {
            labels:   groupesData.map(g => g.label),
            datasets,
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend:      { display: false },
                annotation:  Object.keys(annotations).length ? { annotations } : undefined,
            },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } },
            },
        },
    });
};

// ─── Graphique évolution chronologique superposée ─────────────────────────────
function buildTimelineChart() {
    const ctx = document.getElementById('chart-timeline')?.getContext('2d');
    if (!ctx) return;

    const datasets = groupesData.map((g, i) => {
        const sorted = [...g.analyses.timeline].sort((a, b) => new Date(a.date) - new Date(b.date));
        return {
            label:           g.label,
            data:            sorted.map(t => ({ x: new Date(t.date), y: QUALITE_NUM[t.qualite] || 3 })),
            borderColor:     groupColors[i % groupColors.length],
            backgroundColor: groupColors[i % groupColors.length] + '18',
            pointRadius:     4,
            tension:         0.3,
            fill:            false,
        };
    });

    chartTimeline = new Chart(ctx, {
        type: 'line',
        data: { datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    type: 'time',
                    time: { unit: 'day', displayFormats: { day: 'dd/MM' } },
                    grid: { display: false },
                },
                y: {
                    min: 0, max: 6,
                    ticks: {
                        stepSize: 1,
                        callback: v => ({ 1: 'Mauvais', 2: 'Médiocre', 3: 'Passable', 4: 'Bon', 5: 'Très bon' }[v] || ''),
                    },
                    grid: { color: 'rgba(0,0,0,0.04)' },
                },
            },
            plugins: { legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } } },
        },
    });
}

// ─── Init ─────────────────────────────────────────────────────────────────────
buildQualiteChart();
buildTimelineChart();

// Activer le premier paramètre disponible
const firstParam = document.querySelector('.param-btn');
if (firstParam) {
    showParam(firstParam.dataset.param);
}
