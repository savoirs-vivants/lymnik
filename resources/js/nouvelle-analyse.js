import { createBaseMap } from "./core/map-utils.js";

document.addEventListener("DOMContentLoaded", () => {
    let lat = window.initLat || 48.5853;
    let lng = window.initLng || 7.7512;

    const mapElement = document.getElementById("mini-map");
    if (!mapElement) return;

    const miniMap = createBaseMap("mini-map", lat, lng, 15, true);
    if (miniMap.zoomControl) miniMap.removeControl(miniMap.zoomControl); // Enlève les boutons + et -

    const markerIcon = L.divIcon({
        className: "",
        html: `<div style="width:22px;height:22px;border-radius:50%;background:white;border:3px solid #222a60;box-shadow:0 2px 8px rgba(34,42,96,0.3);"></div>`,
        iconSize: [22, 22],
        iconAnchor: [11, 11],
    });

    const marker = L.marker([lat, lng], {
        icon: markerIcon,
        draggable: true,
    }).addTo(miniMap);

    function updateCoords(newLat, newLng) {
        lat = newLat;
        lng = newLng;
        document.getElementById("f-lat").value = lat.toFixed(6);
        document.getElementById("f-lng").value = lng.toFixed(6);
        document.getElementById("coords-display").textContent = "Définie sur la carte";
    }

    marker.on("dragend", (e) => {
        const pos = e.target.getLatLng();
        updateCoords(pos.lat, pos.lng);
    });

    miniMap.on("click", (e) => {
        marker.setLatLng(e.latlng);
        updateCoords(e.latlng.lat, e.latlng.lng);
    });

    document.getElementById("gps-btn").addEventListener("click", () => {
        if (!navigator.geolocation) return;
        navigator.geolocation.getCurrentPosition((pos) => {
            const ll = [pos.coords.latitude, pos.coords.longitude];
            marker.setLatLng(ll);
            miniMap.setView(ll, 16);
            updateCoords(ll[0], ll[1]);
        });
    });

    const fileInput = document.getElementById("file-upload");
    const phCamera = document.getElementById("ph-camera");
    const photoThumb = document.getElementById("photo-thumb");

    fileInput.addEventListener("change", () => {
        const file = fileInput.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            document.getElementById("photo-preview").src = e.target.result;
            photoThumb.classList.remove("hidden");
            photoThumb.classList.add("flex");
            phCamera.classList.add("hidden");
        };
        reader.readAsDataURL(file);
    });

    document.getElementById("photo-remove").addEventListener("click", () => {
        photoThumb.classList.add("hidden");
        photoThumb.classList.remove("flex");
        phCamera.classList.remove("hidden");
        fileInput.value = "";
    });

    const typeHidden = document.getElementById("f-type");
    const bandFields = document.getElementById("fields-bandelette");
    const photoFields = document.getElementById("fields-photometre");

    function selectType(type) {
        typeHidden.value = type;
        document.querySelectorAll(".type-card").forEach((c) => {
            const isSelected = c.dataset.type === type;
            c.setAttribute("aria-checked", isSelected ? "true" : "false");
            c.classList.toggle("selected", isSelected);

            if (isSelected) {
                c.classList.add("border-[#222a60]", "bg-blue-50/40");
                c.classList.remove(
                    "border-slate-200",
                    "bg-slate-50",
                    "hover:border-slate-300",
                    "hover:bg-white",
                );
            } else {
                c.classList.remove("border-[#222a60]", "bg-blue-50/40");
                c.classList.add(
                    "border-slate-200",
                    "bg-slate-50",
                    "hover:border-slate-300",
                    "hover:bg-white",
                );
            }

            const radioWrapper = c.querySelector(
                ".w-4.h-4.rounded-full.border-2",
            );
            if (radioWrapper) {
                if (isSelected) {
                    radioWrapper.classList.add("border-[#222a60]");
                    radioWrapper.classList.remove("border-slate-300");
                } else {
                    radioWrapper.classList.remove("border-[#222a60]");
                    radioWrapper.classList.add("border-slate-300");
                }

                const dot = radioWrapper.querySelector("div");
                if (dot) {
                    dot.classList.toggle("opacity-100", isSelected);
                    dot.classList.toggle("opacity-0", !isSelected);
                }
            }
        });

        if (["bandelette", "les_deux"].includes(type)) {
            bandFields.classList.remove("hidden");
        } else {
            bandFields.classList.add("hidden");
        }

        if (["photometre", "les_deux"].includes(type)) {
            photoFields.classList.remove("hidden");
        } else {
            photoFields.classList.add("hidden");
        }
    }

    document.querySelectorAll("[data-type]").forEach((card) => {
        card.addEventListener("click", () => selectType(card.dataset.type));
    });

    selectType(typeHidden.value || "bandelette");

    const tabs = document.querySelectorAll(".step-tab");
    const sections = ["section-1", "section-2", "section-3", "section-4"].map(
        (id) => document.getElementById(id),
    );
    const scroll = document.getElementById("form-scroll");

    function updateStepBar() {
        const scrollTop = scroll.scrollTop + 80;
        let active = 0;
        sections.forEach((s, i) => {
            if (s && s.offsetTop <= scrollTop) active = i;
        });
        tabs.forEach((tab, i) => {
            tab.classList.toggle("active", i === active);
            tab.classList.toggle("done", i < active);
        });
    }
    scroll.addEventListener("scroll", updateStepBar, { passive: true });

    tabs.forEach((tab, i) => {
        tab.addEventListener("click", () => {
            if (sections[i]) {
                scroll.scrollTo({
                    top: sections[i].offsetTop - 20,
                    behavior: "smooth",
                });
            }
        });
    });

    setTimeout(() => miniMap.invalidateSize(), 150);

    const riverInput = document.querySelector('input[name="cours_d_eau_id"]');
    const riverDisplay = document.getElementById("river-display");
    const riverStatus = document.getElementById("river-status");
    const riverSearchBlock = document.getElementById("river-search-block");
    const riverSearchInput = document.getElementById("river-search-input");
    const riverSearchRes = document.getElementById("river-search-results");
    const analyseForm = document.getElementById("analyse-form");

    let riverFetchDone = true;
    let riverFetch = null;

    function showRiverSearch() {
        if (riverSearchBlock) riverSearchBlock.classList.remove("hidden");
    }

    function hideRiverSearch() {
        if (riverSearchBlock) riverSearchBlock.classList.add("hidden");
    }

    function setRiver(id, nom) {
        if (riverInput) riverInput.value = id;
        if (riverDisplay) riverDisplay.textContent = nom;
        if (riverStatus) riverStatus.textContent = "Trouvé";
        hideRiverSearch();
    }

    if (window.initCoursDEauId) {
        if (riverDisplay)
            riverDisplay.textContent =
                window.initNomCoursEau ?? "Cours d'eau associé";
        if (riverStatus) riverStatus.textContent = "Trouvé";
    } else if (window.nearestRiverUrl) {
        riverFetchDone = false;
        riverFetch = fetch(`${window.nearestRiverUrl}?lat=${lat}&lng=${lng}`)
            .then((r) => r.json())
            .then((river) => {
                riverFetchDone = true;
                if (river?.id) {
                    setRiver(river.id, river.nom);
                } else {
                    if (riverDisplay)
                        riverDisplay.textContent = "Non trouvé automatiquement";
                    if (riverStatus) riverStatus.textContent = "—";
                    showRiverSearch();
                }
            })
            .catch(() => {
                riverFetchDone = true;
                if (riverDisplay)
                    riverDisplay.textContent = "Non trouvé automatiquement";
                if (riverStatus) riverStatus.textContent = "—";
                showRiverSearch();
            });
    }

    // ─── Recherche manuelle cours d'eau ──────────────────────────────────────
    let riverSearchTimeout = null;

    riverSearchInput?.addEventListener("input", () => {
        const q = riverSearchInput.value.trim();
        clearTimeout(riverSearchTimeout);
        if (riverSearchRes) riverSearchRes.classList.add("hidden");

        if (q.length < 2) return;

        riverSearchTimeout = setTimeout(async () => {
            try {
                const res = await fetch(
                    `${window.searchRiverUrl}?q=${encodeURIComponent(q)}`,
                );
                const data = await res.json();

                if (!riverSearchRes) return;

                if (!data.length) {
                    riverSearchRes.innerHTML =
                        '<div class="px-4 py-3 text-xs text-slate-400 italic">Aucun cours d\'eau trouvé</div>';
                    riverSearchRes.classList.remove("hidden");
                    return;
                }

                riverSearchRes.innerHTML = data
                    .map(
                        (r) => `
                    <button type="button" data-id="${r.id}" data-nom="${r.nom}"
                        class="river-result w-full text-left px-4 py-2.5 hover:bg-blue-50 flex items-center gap-3 border-b border-slate-50 last:border-0 transition-colors">
                        <svg class="w-3.5 h-3.5 text-[#16987c] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        <div>
                            <p class="text-[12px] font-bold text-slate-800">${r.nom}</p>
                            ${r.type_cours ? `<p class="text-[10px] text-slate-400 font-mono">${r.type_cours}</p>` : ""}
                        </div>
                    </button>
                `,
                    )
                    .join("");
                riverSearchRes.classList.remove("hidden");
            } catch {
                /* silencieux */
            }
        }, 300);
    });

    riverSearchRes?.addEventListener("mousedown", (e) => {
        const btn = e.target.closest(".river-result");
        if (!btn) return;
        e.preventDefault();
        setRiver(btn.dataset.id, btn.dataset.nom);
        riverSearchInput.value = "";
        riverSearchRes.classList.add("hidden");
    });

    document.addEventListener("click", (e) => {
        if (!riverSearchBlock?.contains(e.target)) {
            riverSearchRes?.classList.add("hidden");
        }
    });

    // City background search (API Adresse Gouv)
    const villeInput = document.getElementById("f-ville");
    const villeDisplay = document.getElementById("ville-display");
    const villeStatus = document.getElementById("ville-status");

    let villeFetchDone = true;
    let villeFetch = null;

    if (villeInput && !villeInput.value) {
        villeFetchDone = false;

        villeFetch = fetch(
            `https://api-adresse.data.gouv.fr/reverse/?lon=${lng}&lat=${lat}`,
        )
            .then((r) => r.json())
            .then((data) => {
                villeFetchDone = true;

                if (data && data.features && data.features.length > 0) {
                    const ville = data.features[0].properties.city;

                    if (ville) {
                        villeInput.value = ville;
                        if (villeDisplay) villeDisplay.textContent = ville;
                        if (villeStatus) villeStatus.textContent = "Trouvé";
                        return;
                    }
                }

                if (villeDisplay)
                    villeDisplay.textContent = "Ville non trouvée";
                if (villeStatus) villeStatus.textContent = "—";
            })
            .catch((err) => {
                console.error("Erreur géocodage inverse :", err);
                villeFetchDone = true;
                if (villeDisplay)
                    villeDisplay.textContent = "Ville non trouvée";
                if (villeStatus) villeStatus.textContent = "—";
            });
    } else if (villeInput?.value) {
        if (villeDisplay) villeDisplay.textContent = villeInput.value;
        if (villeStatus) villeStatus.textContent = "Trouvé";
    }

    let submitted = false;
    const submitBar = document.getElementById("submit-bar");

    function showRiverWait() {
        if (!submitBar) return;
        let notice = document.getElementById("river-wait-notice");
        if (!notice) {
            notice = document.createElement("p");
            notice.id = "river-wait-notice";
            notice.className =
                "text-center text-xs text-[#1565c0] font-medium mt-2 mb-0";
            notice.textContent =
                "Association du cours d'eau en cours, veuillez patienter…";
            submitBar.appendChild(notice);
        }
    }

    function hideRiverWait() {
        document.getElementById("river-wait-notice")?.remove();
    }

    if (analyseForm) {
        analyseForm.addEventListener("submit", (e) => {
            const currentLat = document.getElementById("f-lat").value;
            const currentLng = document.getElementById("f-lng").value;

            if (currentLat && currentLng) {
                localStorage.setItem(
                    "lymnik_map_center",
                    JSON.stringify({
                        lat: parseFloat(currentLat),
                        lng: parseFloat(currentLng),
                    }),
                );
                localStorage.setItem("lymnik_map_zoom", 16);
            }
            if (submitted || (riverFetchDone && villeFetchDone)) return;
            e.preventDefault();
            showRiverWait();
            Promise.allSettled([riverFetch, villeFetch].filter(Boolean)).then(
                () => {
                    hideRiverWait();
                    submitted = true;
                    analyseForm.submit();
                },
            );
        });
    }

    const btnAnalyse = document.getElementById("btn-mode-analyse");
    const btnCapteur = document.getElementById("btn-mode-capteur");
    const sectionsAnalyse = document.getElementById("sections-analyse");
    const sectionCapteur = document.getElementById("section-capteur");
    const form = document.getElementById("analyse-form");
    const headerTitle = document.getElementById("header-title");
    const submitText = document.getElementById("submit-text");
    const submitIcon = document.getElementById("submit-icon");

    if (btnAnalyse && btnCapteur) {
        btnAnalyse.addEventListener("click", () => {
            btnAnalyse.className =
                "flex-1 py-2.5 text-[13px] font-bold rounded-lg bg-white text-[#222a60] shadow-sm transition-all";
            btnCapteur.className =
                "flex-1 py-2.5 text-[13px] font-bold rounded-lg text-slate-500 hover:text-slate-700 transition-all";
            sectionsAnalyse.classList.remove("hidden");
            sectionCapteur.classList.add("hidden");
            form.action = window.analyseStoreUrl;
            headerTitle.textContent = "Nouvelle analyse";
            submitText.textContent = "Enregistrer la mesure";
            submitIcon.innerHTML =
                '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />';
        });

        btnCapteur.addEventListener("click", () => {
            btnCapteur.className =
                "flex-1 py-2.5 text-[13px] font-bold rounded-lg bg-white text-[#222a60] shadow-sm transition-all";
            btnAnalyse.className =
                "flex-1 py-2.5 text-[13px] font-bold rounded-lg text-slate-500 hover:text-slate-700 transition-all";
            sectionsAnalyse.classList.add("hidden");
            sectionCapteur.classList.remove("hidden");
            form.action = window.capteursStoreUrl;
            headerTitle.textContent = "Nouveau capteur";
            submitText.textContent = "Installer le capteur";
            submitIcon.innerHTML =
                '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />';
        });
    }

    const capTypeBtns = document.querySelectorAll(".cap-type-btn");
    const fTypeCapteur = document.getElementById("f-type-capteur");
    const blockDevEUI = document.getElementById("block-deveui");
    const blockUID = document.getElementById("block-uid");

    capTypeBtns.forEach((btn) => {
        btn.addEventListener("click", () => {
            // Reset
            capTypeBtns.forEach(
                (b) =>
                    (b.className =
                        "cap-type-btn py-2 text-xs font-bold rounded-xl border-2 border-slate-200 bg-slate-50 text-slate-500 transition-colors"),
            );
            // Active
            btn.className =
                "cap-type-btn py-2 text-xs font-bold rounded-xl border-2 border-[#222a60] bg-blue-50/40 text-[#222a60] transition-colors";

            const type = btn.dataset.capteurType;
            fTypeCapteur.value = type;

            if (type === "lora") {
                blockDevEUI.classList.remove("hidden");
                blockUID.classList.add("hidden");
            } else if (type === "bluetooth") {
                blockDevEUI.classList.add("hidden");
                blockUID.classList.remove("hidden");
            } else {
                blockDevEUI.classList.remove("hidden");
                blockUID.classList.remove("hidden");
            }
        });
    });
});
