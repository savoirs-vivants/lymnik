document.addEventListener("DOMContentLoaded", () => {
    let lat = window.initLat || 48.5853;
    let lng = window.initLng || 7.7512;

    const mapElement = document.getElementById("mini-map");
    if (!mapElement) return;

    const miniMap = L.map("mini-map", {
        center: [lat, lng],
        zoom: 15,
        zoomControl: false,
        attributionControl: false,
        dragging: true,
        scrollWheelZoom: false,
    });

    L.tileLayer(
        "https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png",
        {
            maxZoom: 19,
        },
    ).addTo(miniMap);

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
        document.getElementById("coords-display").textContent =
            lat.toFixed(4) + "° N · " + lng.toFixed(4) + "° E";
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

    phCamera.addEventListener("click", () => {
        fileInput.click();
    });

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
            c.classList.toggle("selected", c.dataset.type === type);
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

    const riverInput   = document.querySelector('input[name="cours_d_eau_id"]');
    const riverDisplay = document.getElementById('river-display');
    const riverStatus  = document.getElementById('river-status');
    const analyseForm  = document.getElementById('analyse-form');

    let riverFetchDone = true;
    let riverFetch     = null;

    if (window.initCoursDEauId) {
        if (riverDisplay) riverDisplay.textContent = window.initNomCoursEau ?? 'Cours d\'eau associé';
        if (riverStatus)  riverStatus.textContent  = 'Trouvé';
    } else if (window.nearestRiverUrl) {
        riverFetchDone = false;
        riverFetch = fetch(`${window.nearestRiverUrl}?lat=${lat}&lng=${lng}`)
            .then(r => r.json())
            .then(river => {
                riverFetchDone = true;
                if (river?.id) {
                    if (riverInput)   riverInput.value         = river.id;
                    if (riverDisplay) riverDisplay.textContent = river.nom;
                    if (riverStatus)  riverStatus.textContent  = 'Trouvé';
                } else {
                    if (riverDisplay) riverDisplay.textContent = 'Position libre';
                    if (riverStatus)  riverStatus.textContent  = '—';
                }
            })
            .catch(() => {
                riverFetchDone = true;
                if (riverDisplay) riverDisplay.textContent = 'Position libre';
                if (riverStatus)  riverStatus.textContent  = '—';
            });
    }

    let submitted = false;
    const submitBtn = document.querySelector('[type="submit"][form="analyse-form"], #analyse-form [type="submit"]');
    const submitBar = document.getElementById('submit-bar');

    function showRiverWait() {
        if (!submitBar) return;
        let notice = document.getElementById('river-wait-notice');
        if (!notice) {
            notice = document.createElement('p');
            notice.id = 'river-wait-notice';
            notice.className = 'text-center text-xs text-[#1565c0] font-medium mt-2 mb-0';
            notice.textContent = 'Association du cours d\'eau en cours, veuillez patienter…';
            submitBar.appendChild(notice);
        }
    }

    function hideRiverWait() {
        document.getElementById('river-wait-notice')?.remove();
    }

    if (analyseForm) {
        analyseForm.addEventListener('submit', e => {
            if (submitted || riverFetchDone) return;
            e.preventDefault();
            showRiverWait();
            riverFetch.finally(() => { hideRiverWait(); submitted = true; analyseForm.submit(); });
        });
    }
});
