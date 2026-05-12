import{n as e,t}from"./map-utils-Bu19uqdE.js";import{a as n,i as r,n as i}from"./config-C_cl6NgS.js";import{n as a}from"./chart-utils-DW-P0LCm.js";var o=window.__coursDEaux||[],s=null,c=null,l=null,u=null;function d(e){return e.ville?e.ville:`GPS (${parseFloat(e.latitude).toFixed(3)}, ${parseFloat(e.longitude).toFixed(3)})`}window.selectCoursDEau=function(e){let t=o.find(t=>t.id===e);if(!t)return;c=e,document.querySelectorAll(`.cours-eau-item`).forEach(t=>{let n=parseInt(t.dataset.id)===e;t.classList.toggle(`bg-blue-50/80`,n),t.classList.toggle(`border-[#1565c0]`,n),t.classList.toggle(`border-transparent`,!n)});let n=document.getElementById(`empty-state`);n&&(n.style.display=`none`),document.getElementById(`detail-panel`).classList.remove(`hidden`),document.getElementById(`detail-nom`).textContent=t.nom,document.getElementById(`detail-qualite-badge`).outerHTML=r(t.qualite_globale).replace(`class="`,`id="detail-qualite-badge" class="`),document.getElementById(`detail-meta`).innerHTML=`<span class="font-bold text-slate-700">${t.total_analyses}</span> analyses sur <span class="font-bold text-slate-700">${t.total_points}</span> points`,f(t),p(t),m(t)};function f(e){let t=[`tres_bon`,`bon`,`passable`,`mediocre`,`mauvais`];document.getElementById(`detail-kpis`).innerHTML=t.map(t=>{let n=i[t],r=e.qualite_counts[t]||0;return`
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex flex-col justify-between h-full ${r>0?``:`opacity-50 grayscale`}">
            <div class="flex items-center gap-2 mb-3"><span class="w-2.5 h-2.5 rounded-full ${n.dot}"></span><p class="text-[10px] font-mono text-slate-500">${n.label}</p></div>
            <div><p class="text-3xl font-black ${n.text}">${r}</p></div>
        </div>`}).join(``)}function p(e){let t={tres_bon:5,bon:4,passable:3,mediocre:2,mauvais:1},n=e.points.flatMap(e=>e.analyses.map(t=>({...t,pointLabel:d(e)}))).sort((e,t)=>new Date(e.created_at)-new Date(t.created_at));s&&s.destroy(),n.length&&(s=new Chart(document.getElementById(`qualite-chart`).getContext(`2d`),{type:`bar`,data:{labels:n.map(e=>new Date(e.created_at).toLocaleDateString(`fr-FR`,{day:`2-digit`,month:`short`})),datasets:[{data:n.map(e=>t[e.qualite]??0),backgroundColor:n.map(e=>(i[e.qualite]||i.tres_bon).hex),borderRadius:4}]},options:{responsive:!0,maintainAspectRatio:!1,plugins:{legend:{display:!1},tooltip:a},scales:{y:{min:0,max:5.5,ticks:{callback:e=>[``,`Mauvais`,`Médiocre`,`Passable`,`Bon`,`Très bon`][e]||``}}}}}))}function m(e){let t=document.getElementById(`points-tbody`);t.innerHTML=e.points.map(e=>{let t=e.analyses||[];if(!t.length)return``;let i=t[0];return`
        <tr class="hover:bg-slate-50 transition-colors group border-b border-slate-100">
            <td class="py-4 pl-4 pr-4 align-top">
                <div class="text-sm font-bold text-[#222a60] truncate max-w-[180px]">${e.ville||e.nom||`Point inconnu`}</div>
                <div class="font-mono text-[10px] text-slate-400 mt-0.5">${`${parseFloat(e.latitude).toFixed(5)}, ${parseFloat(e.longitude).toFixed(5)}`}</div>
                <div class="font-mono text-xs text-slate-500 mt-2">${i.date||`—`} <span class="text-[10px] text-slate-400">${i.time||``}</span></div>
            </td>
            <td class="py-4 pr-4 align-top">
                <span class="text-[11px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-1 rounded-md whitespace-nowrap">${n(i.type)}</span>
            </td>
            <td class="py-4 pr-4 align-top">${r(i.qualite)}</td>
            <td class="py-4 pr-4 align-top text-center w-28">
                <button type="button" class="btn-historique flex items-center justify-center gap-2 px-3 py-1.5 rounded-lg bg-blue-50 text-[#1565c0] hover:bg-[#1565c0] hover:text-white text-xs font-bold transition-colors w-full" data-point-id="${e.id}">
                    Historique ${t.length>1?`(${t.length})`:``}
                </button>
            </td>
        </tr>`}).join(``)}document.addEventListener(`click`,function(e){let t=e.target.closest(`.btn-historique`);if(t){e.preventDefault(),e.stopPropagation();let n=t.getAttribute(`data-point-id`);console.log(`🚨 BOUTON CLIQUÉ VIA DELEGATION ! ID du point :`,n),h(n)}});function h(n){console.log(`🟢 Lancement de ouvrirOverlayHistorique...`);let r=o.find(e=>e.id===c);if(!r)return console.error(`Cours d'eau non trouvé`);let a=r.points.find(e=>String(e.id)===String(n));if(!a)return console.error(`Point non trouvé`);let s=a.ville||a.nom||`Point inconnu`;document.getElementById(`overlay-title`).textContent=s,document.getElementById(`overlay-subtitle`).textContent=`Coordonnées : ${parseFloat(a.latitude).toFixed(5)}, ${parseFloat(a.longitude).toFixed(5)} · Historique (${a.analyses.length})`,document.getElementById(`point-overlay`).classList.remove(`hidden`),document.body.style.overflow=`hidden`,setTimeout(()=>{l?(l.setView([a.latitude,a.longitude],15),u.setLatLng([a.latitude,a.longitude]),l.invalidateSize()):(l=t(`overlay-map`,parseFloat(a.latitude),parseFloat(a.longitude),15,!1),u=L.marker([a.latitude,a.longitude],{icon:e(`#ef4444`,!1,14)}).addTo(l))},50);let d=document.getElementById(`overlay-content`);d.innerHTML=a.analyses.map((e,t)=>{let n=e.bandelette||{},r=e.photometre||{},a=i[e.qualite]||i.tres_bon,o=[[`Nitrates`,n.nitrates,`mg/L`],[`Nitrites`,n.nitrites,`mg/L`],[`Dureté totale`,n.durete_totale,`mg/L`],[`Dureté carb.`,n.durete_carb,`mg/L`],[`pH`,n.ph,``],[`Chlore`,n.chlore,`mg/L`]].filter(([,t])=>e.type===`bandelette`||e.type===`les_deux`),s=[[`Phosphate`,r.phosphate,`mg/L`],[`Nitrate`,r.nitrate,`mg/L`],[`Ammoniaque`,r.ammoniaque,`mg/L`]].filter(([,t])=>e.type===`photometre`||e.type===`les_deux`),c=e=>e.map(([e,t,n])=>`
            <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                <p class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1">${e}</p>
                <p class="text-[15px] font-black ${t!=null&&t!==``?`text-[#222a60]`:`text-slate-300`}">
                    ${t!=null&&t!==``?t:`—`}
                    ${t!=null&&t!==``&&n?`<span class="text-[10px] font-bold text-slate-400 ml-1">${n}</span>`:``}
                </p>
            </div>`).join(``);return`
    <div class="bg-white rounded-[1.5rem] border border-slate-200 shadow-sm overflow-hidden relative mb-4">
        <div class="absolute top-0 left-0 w-2 h-full ${a.bg}"></div>
        <div class="flex items-center justify-between px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-50 ml-2">
            <div class="flex items-center gap-3 sm:gap-4">
                <span class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-black text-slate-500 shrink-0">${t+1}</span>
                <div class="min-w-0">
                    <p class="text-[13px] sm:text-[15px] font-bold text-slate-800 truncate">${e.date||`—`} <span class="text-slate-400 font-normal text-xs sm:text-sm ml-1">${e.time||``}</span></p>
                    ${e.saisi_par?`<p class="text-[10px] sm:text-[11px] text-[#1565c0] font-mono font-bold mt-0.5 truncate">Saisi par ${e.saisi_par}</p>`:``}
                </div>
            </div>
            <div class="flex items-center gap-3 pl-2">
                <span class="inline-flex items-center gap-1.5 px-2 py-1 sm:px-2.5 sm:py-1 rounded-md text-[9px] sm:text-[10px] font-bold uppercase tracking-wider whitespace-nowrap ${a.bg} ${a.text}">
                    <span class="w-1.5 h-1.5 rounded-full ${a.dot}"></span>
                    <span class="hidden sm:inline">${a.label}</span>
                </span>
            </div>
        </div>
        <div class="p-4 sm:p-6 ml-2 space-y-4 sm:space-y-6">
            ${o.length?`<div><p class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2"><span class="w-1.5 h-4 bg-blue-500 rounded-full"></span> Bandelette JBL</p><div class="grid grid-cols-2 sm:grid-cols-3 gap-3">${c(o)}</div></div>`:``}
            ${s.length?`<div><p class="text-xs font-bold text-slate-700 mb-3 flex items-center gap-2"><span class="w-1.5 h-4 bg-indigo-500 rounded-full"></span> Photomètre</p><div class="grid grid-cols-2 sm:grid-cols-3 gap-3">${c(s)}</div></div>`:``}
            ${e.note?`<div class="bg-amber-50/50 border border-amber-100 rounded-xl p-4"><p class="text-[10px] font-mono font-bold uppercase tracking-widest text-amber-600 mb-2">Observations terrain</p><p class="text-sm text-slate-700 leading-relaxed">${e.note}</p></div>`:``}
            ${e.image?`<div><p class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-2">Photo</p><img src="${e.image}" alt="Photo de l'analyse" class="rounded-xl w-full max-h-48 object-cover border border-slate-100"></div>`:``}
        </div>
    </div>`}).join(``)}window.closeOverlay=function(){document.getElementById(`point-overlay`).classList.add(`hidden`),document.body.style.overflow=`auto`},document.addEventListener(`DOMContentLoaded`,()=>{let e=document.getElementById(`search-cours-eau`),t=document.getElementById(`filter-qualite`);e&&t&&(e.addEventListener(`input`,n),t.addEventListener(`change`,n));function n(){let n=e.value.toLowerCase().trim(),r=t.value,i=0;document.querySelectorAll(`.cours-eau-item`).forEach(e=>{let t=!n||e.dataset.nom.includes(n),a=!r||e.dataset.qualite===r,o=t&&a;e.style.display=o?``:`none`,o&&i++});let a=document.getElementById(`cours-eau-count`);a&&(a.textContent=`${i} cours d'eau`)}document.addEventListener(`keydown`,e=>{e.key===`Escape`&&closeOverlay()}),o.length>0&&selectCoursDEau(o[0].id)});