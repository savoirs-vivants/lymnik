// Bluetooth modal open/close
const btModal = document.getElementById('bt-modal');
document.getElementById('btn-open-bt')?.addEventListener('click', () => btModal.classList.remove('translate-y-full'));
document.getElementById('btn-open-bt-desk')?.addEventListener('click', () => btModal.classList.remove('translate-y-full'));
document.getElementById('bt-close')?.addEventListener('click', () => btModal.classList.add('translate-y-full'));

// Sync recherche desktop → mobile.
// map.js écoute uniquement #search-input (dans la top-bar mobile), mais sur desktop
// la recherche visible est #search-input-desk (dans la sidebar). On proxy les events
// et on miroir les résultats via MutationObserver pour que les deux restent cohérents.
const deskSearch    = document.getElementById('search-input-desk');
const mobileSearch  = document.getElementById('search-input');
const deskResults   = document.getElementById('search-results-desk');
const mobileResults = document.getElementById('search-results');

if (deskSearch && mobileSearch) {
    deskSearch.addEventListener('input', () => {
        mobileSearch.value = deskSearch.value;
        mobileSearch.dispatchEvent(new Event('input', { bubbles: true }));
    });
    const observer = new MutationObserver(() => {
        deskResults.innerHTML = mobileResults.innerHTML;
        deskResults.classList.toggle('hidden', mobileResults.classList.contains('hidden'));
    });
    observer.observe(mobileResults, { childList: true, attributes: true, attributeFilter: ['class'] });
}

// ─── Bluetooth PCB ────────────────────────────────────────────────────────────

const zoneLog     = document.getElementById('bt-console');
const btnConnect  = document.getElementById('bt-action-connect');
const btnDownload = document.getElementById('bt-action-download');
const btnSync     = document.getElementById('bt-action-sync');
const syncStatus  = document.getElementById('bt-sync-status');

// UUIDs du module RN4871 (UART Bluetooth LE)
const RN4871_SERVICE_UUID = '49535343-fe7d-4ae5-8fa9-9fafd205e455';
const RN4871_TX_UUID      = '49535343-1e4d-4bd9-ba61-23c647249616';
const RN4871_RX_UUID      = '49535343-8841-43f4-a8d4-ecbe34729bb3';

let receiveBuffer       = "";
let writeCharacteristic = null;

function ecrireSysteme(message) {
    const temps = new Date().toLocaleTimeString();
    zoneLog.value += `\n\n[${temps}] 🔵 ${message}\n`;
    zoneLog.scrollTop = zoneLog.scrollHeight;
}

// Fonction utilitaire pour l'affichage : remplace -32768 par 0
function formatUI(val) {
    return val == -32768 ? '0' : val;
}

// Format des lignes reçues du capteur :
//   UID:005D003D393650022037374E          → identifiant unique du capteur (clé de lookup en BDD)
//   1,268435456,0,-13570,0,0,0            → id, timestamp_unix, turbidite, conductivite, temp_eau, hauteur, debit
function decodeData(line) {
    if (line.startsWith('UID:')) {
        document.getElementById('valUid').innerText = line.substring(4).trim();
        return;
    }
    const parts = line.split(',');
    if (parts.length < 3) return;

    // Utilisation de formatUI pour corriger l'affichage en direct
    if (parts[2] !== undefined) document.getElementById('valTurb').firstChild.textContent  = formatUI(parts[2]) + ' ';
    if (parts[3] !== undefined) document.getElementById('valCond').firstChild.textContent  = formatUI(parts[3]) + ' ';
    if (parts[4] !== undefined) document.getElementById('valTemp').innerText               = formatUI(parts[4]) + ' °C';
    if (parts[5] !== undefined) document.getElementById('valHaut').firstChild.textContent  = formatUI(parts[5]) + ' ';
    if (parts[6] !== undefined) document.getElementById('valDebit').firstChild.textContent = formatUI(parts[6]) + ' ';
}

// Fonction utilitaire pour la BDD : parse le float et remplace -32768 par 0
function parseSensorValue(val) {
    if (val === undefined || val === '') return null;
    const num = parseFloat(val);
    return num === -32768 ? 0 : num;
}

// Parse l'intégralité de la console au moment du sync plutôt qu'en mémoire en temps réel.
// Raison : avec 4000+ lignes, maintenir un tableau en parallèle doublerait la mémoire utilisée.
// On reparse le log texte (déjà en mémoire dans le textarea) à la demande, c'est négligeable.
// Le Set `seen` gère le distinct côté client avant l'envoi — le serveur fait aussi sa propre
// vérification sur les timestamps déjà en BDD.
function parseLog(logText) {
    const lines  = logText.split('\n');
    let uid      = null;
    const lignes = [];
    const seen   = new Set();

    for (const raw of lines) {
        const line = raw.trim();
        if (!line || line.startsWith('[') || line.startsWith('Prêt')) continue;

        if (line.startsWith('UID:')) {
            uid = line.substring(4).trim();
            continue;
        }

        const parts = line.split(',');
        if (parts.length < 3) continue;

        const ts = parseInt(parts[1]);
        if (isNaN(ts) || ts <= 0) continue;
        if (seen.has(ts)) continue;
        seen.add(ts);

        lignes.push({
            // Multiplication par 1000 pour passer des secondes aux millisecondes
            timestamp:    ts * 1000,
            turbidite:    parseSensorValue(parts[2]),
            conductivite: parseSensorValue(parts[3]),
            temp_eau:     parseSensorValue(parts[4]),
            hauteur:      parseSensorValue(parts[5]),
            debit:        parseSensorValue(parts[6]),
        });
    }

    return { uid, lignes };
}

async function sendCommandToPCB(cmd) {
    if (!writeCharacteristic) { ecrireSysteme("❌ Connectez d'abord le PCB."); return; }
    try {
        await writeCharacteristic.writeValue(new TextEncoder('utf-8').encode(cmd));
        ecrireSysteme(`👉 Commande : "${cmd}"`);
    } catch (e) { ecrireSysteme(`❌ ${e.message}`); }
}

document.getElementById('bt-action-start')?.addEventListener('click', () => sendCommandToPCB("?"));
document.getElementById('bt-action-stop')?.addEventListener('click', () => sendCommandToPCB("Q"));

btnConnect?.addEventListener('click', async () => {
    try {
        ecrireSysteme("Recherche d'une station Bluetooth...");
        const device = await navigator.bluetooth.requestDevice({
            filters: [{ namePrefix: 'Station' }, { namePrefix: 'station' }],
            optionalServices: [RN4871_SERVICE_UUID]
        });
        ecrireSysteme(`Connecté à : ${device.name}`);
        btnConnect.innerText = "Connecté ✅";
        btnConnect.classList.replace('bg-sv-blue', 'bg-[#16987c]');
        device.addEventListener('gattserverdisconnected', () => {
            ecrireSysteme("Déconnecté.");
            btnConnect.innerText = "1. Connecter le PCB";
            btnConnect.classList.replace('bg-[#16987c]', 'bg-sv-blue');
            writeCharacteristic = null;
        });
        const server   = await device.gatt.connect();
        const service  = await server.getPrimaryService(RN4871_SERVICE_UUID);
        const readChar = await service.getCharacteristic(RN4871_TX_UUID);
        writeCharacteristic = await service.getCharacteristic(RN4871_RX_UUID);
        ecrireSysteme("Écoute et écriture activées.");
        await readChar.startNotifications();
        readChar.addEventListener('characteristicvaluechanged', (event) => {
            const text = new TextDecoder('utf-8').decode(event.target.value);
            zoneLog.value += text;
            zoneLog.scrollTop = zoneLog.scrollHeight;
            receiveBuffer += text;
            let lines = receiveBuffer.split('\n');
            receiveBuffer = lines.pop();
            for (let line of lines) { line = line.trim(); if (line) decodeData(line); }
        });
    } catch (e) {
        if (e.name !== 'NotFoundError') ecrireSysteme(`ERREUR : ${e.message}`);
        else ecrireSysteme("Recherche annulée.");
    }
});

btnDownload?.addEventListener('click', () => {
    const blob = new Blob([zoneLog.value], { type: 'text/plain' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = `Log_Station_${new Date().toISOString().slice(0, 10)}.txt`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
});

btnSync?.addEventListener('click', async () => {
    const { uid, lignes } = parseLog(zoneLog.value);

    if (!uid) {
        syncStatus.className   = 'mt-2 p-3 rounded-xl text-xs font-mono bg-red-50 text-red-600';
        syncStatus.textContent = '❌ Aucun UID détecté dans les données reçues.';
        syncStatus.classList.remove('hidden');
        return;
    }
    if (lignes.length === 0) {
        syncStatus.className   = 'mt-2 p-3 rounded-xl text-xs font-mono bg-amber-50 text-amber-600';
        syncStatus.textContent = '⚠️ Aucune ligne de données à synchroniser.';
        syncStatus.classList.remove('hidden');
        return;
    }

    btnSync.disabled       = true;
    btnSync.textContent    = `Envoi de ${lignes.length} mesures…`;
    syncStatus.className   = 'mt-2 p-3 rounded-xl text-xs font-mono bg-slate-100 text-slate-600';
    syncStatus.textContent = '⏳ Synchronisation en cours…';
    syncStatus.classList.remove('hidden');

    try {
        // L'URL est injectée via window.btSyncUrl dans la blade (seul endroit avec accès à PHP)
        const resp = await fetch(window.btSyncUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ uid, lignes }),
        });

        const data = await resp.json();

        if (resp.ok) {
            syncStatus.className   = 'mt-2 p-3 rounded-xl text-xs font-mono bg-emerald-50 text-emerald-700';
            syncStatus.textContent = `✅ ${data.inseres} mesures insérées · ${data.ignores} ignorées (déjà en BDD)`;
            ecrireSysteme(`✅ Sync BDD : ${data.inseres} insérées, ${data.ignores} ignorées.`);
        } else {
            syncStatus.className   = 'mt-2 p-3 rounded-xl text-xs font-mono bg-red-50 text-red-600';
            syncStatus.textContent = `❌ ${data.error || 'Erreur serveur'}`;
        }
    } catch (e) {
        syncStatus.className   = 'mt-2 p-3 rounded-xl text-xs font-mono bg-red-50 text-red-600';
        syncStatus.textContent = `❌ Erreur réseau : ${e.message}`;
    } finally {
        btnSync.disabled  = false;
        btnSync.innerHTML = `<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg> Synchroniser avec la BDD`;
    }
});
