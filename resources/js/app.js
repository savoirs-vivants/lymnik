document.addEventListener("DOMContentLoaded", () => {
    const mobileMenuBtn = document.getElementById("mobile-menu-btn");
    const closeMenuBtn = document.getElementById("close-menu-btn");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("mobile-menu-overlay");

    function openMenu() {
        sidebar.classList.remove("-translate-x-full");
        overlay.classList.remove("hidden");
        setTimeout(() => {
            overlay.classList.remove("opacity-0");
        }, 10);
        document.body.classList.add("overflow-hidden");
    }

    function closeMenu() {
        sidebar.classList.add("-translate-x-full");
        overlay.classList.add("opacity-0");
        setTimeout(() => {
            overlay.classList.add("hidden");
        }, 300);
        document.body.classList.remove("overflow-hidden");
    }

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener("click", openMenu);
    }

    if (closeMenuBtn) {
        closeMenuBtn.addEventListener("click", closeMenu);
    }

    if (overlay) {
        overlay.addEventListener("click", closeMenu);
    }
});

window.showAnalysesList = function () {
    document.getElementById("analyses-aside").classList.remove("hidden");
    const detail = document.getElementById("analyses-detail");
    detail.classList.add("hidden");
    detail.classList.remove("flex");
    const nav = document.getElementById("mobile-analyses-nav");
    nav.classList.add("hidden");
    nav.classList.remove("flex");
};

document.addEventListener("DOMContentLoaded", function () {
    const _orig = window.selectCoursDEau;
    window.selectCoursDEau = function (id) {
        _orig(id);
        if (window.innerWidth < 1024) {
            document.getElementById("analyses-aside").classList.add("hidden");
            const detail = document.getElementById("analyses-detail");
            detail.classList.remove("hidden");
            detail.classList.add("flex");
            const nav = document.getElementById("mobile-analyses-nav");
            nav.classList.remove("hidden");
            nav.classList.add("flex");
            const cd = (window.__coursDEaux || []).find((c) => c.id === id);
            if (cd)
                document.getElementById("mobile-detail-title").textContent =
                    cd.nom;
        }
    };
});
