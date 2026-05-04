// Participant analyses page

const coursDEaux    = window.__coursDEaux    || [];
const qualiteConfig = window.__qualiteConfig || {};

const QUALITE_SCORE = { tres_bon: 0, bon: 1, passable: 2, mediocre: 3, mauvais: 4 };
const QUALITE_NUM   = { tres_bon: 5, bon: 4, passable: 3, mediocre: 2, mauvais: 1 };

let currentChart = null;

// Recherche
document.getElementById('search-cours-eau')?.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.cours-eau-item').forEach(el => {
        el.style.display = el.dataset.nom.includes(q) ? '' : 'none';
    });
});

window.selectCoursDEau = function (id) {
    const cd = coursDEaux.find(c => c.id === id);
    if (!cd) return;

    // Active state
    document.querySelectorAll('.cours-eau-item').forEach(el => {
        el.classList.toggle('border-[#222a60]', parseInt(el.dataset.id) === id);
        el.classList.toggle('bg-blue-50/50',    parseInt(el.dataset.id) === id);
    });

    const cfg = qualiteConfig[cd.qualite_globale] || qualiteConfig.tres_bon;

    // Show panel
    document.getElementById('empty-state').classList.add('hidden');
    const panel = document.getElementById('detail-panel');
    panel.classList.remove('hidden');
    document.getElementById('analyses-detail').classList.remove('hidden');

    // Header
    document.getElementById('detail-nom').textContent = cd.nom;
    const badge = document.getElementById('detail-qualite-badge');
    badge.className = `inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider ${cfg.bg} ${cfg.text}`;
    badge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full ${cfg.dot}"></span>${cfg.label}`;
    document.getElementById('detail-meta').textContent =
        `${cd.total_analyses} analyse${cd.total_analyses > 1 ? 's' : ''} · ${cd.total_points} point${cd.total_points > 1 ? 's' : ''}`;

    // Chart
    buildChart(cd);

    // Tableau
    buildTable(cd);
};

function buildChart(cd) {
    if (currentChart) { currentChart.destroy(); currentChart = null; }

    const allAnalyses = cd.points.flatMap(pt => pt.analyses).sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
    if (!allAnalyses.length) return;

    const data = allAnalyses.map(a => ({ x: new Date(a.created_at), y: QUALITE_NUM[a.qualite] || 3 }));
    const colors = allAnalyses.map(a => qualiteConfig[a.qualite]?.chart || '#94a3b8');

    const ctx = document.getElementById('qualite-chart').getContext('2d');
    currentChart = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: [{
                data,
                borderColor: '#222a60',
                backgroundColor: 'rgba(34,42,96,0.06)',
                pointBackgroundColor: colors,
                pointRadius: 5,
                tension: 0.3,
                fill: true,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { type: 'time', time: { unit: 'day', displayFormats: { day: 'dd/MM' } }, grid: { display: false } },
                y: {
                    min: 0, max: 6,
                    ticks: {
                        stepSize: 1,
                        callback: v => ({ 1: 'Mauvais', 2: 'Médiocre', 3: 'Passable', 4: 'Bon', 5: 'Très bon' }[v] || ''),
                    },
                    grid: { color: 'rgba(0,0,0,0.04)' },
                },
            },
            plugins: { legend: { display: false } },
        },
    });
}

function buildTable(cd) {
    const tbody = document.getElementById('points-tbody');
    tbody.innerHTML = '';

    const analyses = cd.points.flatMap(pt =>
        pt.analyses.map(a => ({ ...a, ville: pt.ville }))
    ).sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

    if (!analyses.length) {
        tbody.innerHTML = '<tr><td colspan="3" class="py-6 text-center text-sm text-slate-400 italic">Aucune analyse</td></tr>';
        return;
    }

    analyses.forEach(a => {
        const cfg = qualiteConfig[a.qualite] || qualiteConfig.tres_bon;
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50/50';
        tr.innerHTML = `
            <td class="py-2.5 pl-3 pr-4 text-slate-500 text-sm whitespace-nowrap">${a.date} <span class="text-slate-300">${a.time}</span></td>
            <td class="py-2.5 pr-4 text-slate-600 text-sm capitalize">${a.type?.replace('_', ' ') || '—'}</td>
            <td class="py-2.5 pr-4">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider ${cfg.bg} ${cfg.text}">
                    <span class="w-1.5 h-1.5 rounded-full ${cfg.dot}"></span>${cfg.label}
                </span>
            </td>
        `;
        tbody.appendChild(tr);
    });
}
