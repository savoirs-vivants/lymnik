export const QUALITE_CONFIG = {
    tres_bon: {
        label: "Très bon",
        bg: "bg-emerald-100",
        text: "text-emerald-700",
        dot: "bg-emerald-500",
        hex: "#10b981",
    },
    bon: {
        label: "Bon",
        bg: "bg-teal-100",
        text: "text-teal-700",
        dot: "bg-teal-500",
        hex: "#14b8a6",
    },
    passable: {
        label: "Passable",
        bg: "bg-yellow-100",
        text: "text-yellow-700",
        dot: "bg-yellow-400",
        hex: "#eab308",
    },
    mediocre: {
        label: "Médiocre",
        bg: "bg-orange-100",
        text: "text-orange-700",
        dot: "bg-orange-400",
        hex: "#f97316",
    },
    mauvais: {
        label: "Mauvais",
        bg: "bg-red-100",
        text: "text-red-700",
        dot: "bg-red-500",
        hex: "#ef4444",
    },
    non_valide: {
        label: "Invalide",
        bg: "bg-slate-100",
        text: "text-slate-500",
        dot: "bg-slate-400",
        hex: "#94a3b8",
    },
};

export const MESURES_META = {
    nitrates: { label: "Nitrates", unit: "mg/L", color: "#3b82f6" },
    nitrites: { label: "Nitrites", unit: "mg/L", color: "#8b5cf6" },
    ph: { label: "pH", unit: "", color: "#14b8a6" },
    chlore: { label: "Chlore", unit: "mg/L", color: "#06b6d4" },
    durete: { label: "Dureté totale", unit: "mg/L", color: "#f59e0b" },
    phosphate: { label: "Phosphate", unit: "mg/L", color: "#f97316" },
    ammoniaque: { label: "Ammoniaque", unit: "mg/L", color: "#ef4444" },
    nitrate_photo: { label: "Nitrate (photo)", unit: "mg/L", color: "#6366f1" },
};

export function typeLabel(type) {
    return (
        {
            bandelette: "Bandelette JBL",
            photometre: "Photomètre",
            les_deux: "Bandelette + Photomètre",
        }[type] || type
    );
}

export function qualiteBadgeHtml(q) {
    const cfg = QUALITE_CONFIG[q] || QUALITE_CONFIG.tres_bon;
    return `
        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider ${cfg.bg} ${cfg.text}">
            <span class="w-1.5 h-1.5 rounded-full ${cfg.dot}"></span>${cfg.label}
        </span>
    `;
}
