<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lymnik — Suivi de la qualité des cours d'eau</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white text-gray-900 antialiased font-grotesk">
    <nav class="fixed top-0 inset-x-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="gap-2.5 group">
                <span class="font-grotesk font-bold text-sv-blue text-lg tracking-tight">Lymnik</span>
            </a>
            <div class="flex items-center gap-3">
                <a href="/code"
                    class="flex items-center gap-1.5 px-4 py-2 rounded-full border border-sv-blue/25 text-sv-blue text-sm font-medium hover:border-sv-blue/60 hover:bg-sv-blue/5 transition-all duration-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    J'ai un code
                </a>
                <a href="{{ route('login') }}"
                    class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </a>
            </div>
        </div>
    </nav>

    <section class="hero-noise relative min-h-[630px] flex items-center pt-16 overflow-hidden"
        style="background: linear-gradient(135deg, #151a3a 0%, #1e2760 30%, #222a60 60%, #2d4a8a 85%, #3a6ab0 100%);">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="absolute top-1/4 left-1/3 w-96 h-96 rounded-full opacity-15"
                style="background: radial-gradient(circle, #3a6ab0 0%, transparent 70%);"></div>
            <div class="absolute bottom-1/3 right-1/4 w-64 h-64 rounded-full opacity-10"
                style="background: radial-gradient(circle, #16987c 0%, transparent 70%);"></div>
        </div>
        <div
            class="relative z-10 max-w-7xl mx-auto px-6 w-full py-20 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="font-grotesk text-4xl sm:text-5xl lg:text-[52px] font-bold text-white leading-[1.1] mb-6">
                    Surveiller la qualité<br>
                    de nos cours d'eau,<br>
                    <span style="color: #6de8ce;">ensemble.</span>
                </h1>
                <p class="text-blue-100/75 text-lg leading-relaxed max-w-md mb-10">
                    Stations de mesure en continu, analyses participatives et données
                    cartographiées en temps réel — pour comprendre et protéger nos rivières.
                </p>
                <a href="{{ route('index_mobile') }}"
                    class="btn-lift inline-flex items-center gap-2.5 px-6 py-3.5 rounded-xl border border-white/25 bg-white/10 hover:bg-white/20 text-white text-sm font-semibold backdrop-blur-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" stroke-width="2" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35" />
                    </svg>
                    Voir la carte en direct
                </a>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none translate-y-[1px]">
            <svg class="relative block w-full h-[60px] sm:h-[80px]" viewBox="0 0 1440 80"
                xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <path d="M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z" fill="white" />
            </svg>
        </div>
    </section>

    <section class="py-24 px-6 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-14 reveal">
                <p class="text-sv-green text-xs font-mono font-semibold tracking-widest uppercase mb-3">
                    Deux plateformes, une mission
                </p>
                <h2 class="font-grotesk text-3xl sm:text-4xl font-bold text-sv-blue mb-4">
                    Un écosystème numérique pour la rivière
                </h2>
                <p class="text-gray-500 text-base max-w-xl mx-auto leading-relaxed">
                    Une application mobile pour vos relevés sur le terrain, et une plateforme web ouverte à tous avec un
                    espace spécialement conçu pour les scolaires.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 reveal">
                <div
                    class="platform-card relative overflow-hidden rounded-2xl p-8 border border-blue-100 bg-gradient-to-br from-blue-50/80 to-indigo-50/40">
                    <div class="absolute -bottom-8 -right-8 w-40 h-40 rounded-full bg-sv-blue/8 pointer-events-none">
                    </div>

                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-5 bg-sv-blue/10">
                        <svg class="w-5 h-5 text-sv-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="7" y="2" width="10" height="20" rx="2" stroke-width="2" />
                            <path d="M11 18h2" stroke-linecap="round" stroke-width="2" />
                        </svg>
                    </div>
                    <h3 class="font-grotesk text-2xl font-bold text-sv-blue mb-3">
                        Application mobile
                    </h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-6">
                        Géolocalisez vos prélèvements, saisissez vos résultats de bandelettes ou photomètre,
                        et visualisez la carte des capteurs autour de vous.
                    </p>
                    <div class="flex flex-wrap gap-2 mb-8">
                        @foreach (['Bandelettes JBL', 'Photomètre', 'Saisie simplifiée'] as $tag)
                            <span
                                class="px-3 py-1 rounded-full border border-sv-blue/20 text-sv-blue/80 text-xs font-medium bg-white/70">
                                {{ $tag }}
                            </span>
                        @endforeach
                    </div>
                    <a href="{{ route('index_mobile') }}"
                        class="btn-lift inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-semibold bg-sv-blue hover:opacity-90 transition-opacity duration-200">
                        Accéder à l'application
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
                <div
                    class="platform-card relative overflow-hidden rounded-2xl p-8 border border-sv-green/20 bg-gradient-to-br from-emerald-50/70 to-teal-50/40">
                    <div class="absolute -bottom-8 -right-8 w-40 h-40 rounded-full bg-sv-green/10 pointer-events-none">
                    </div>

                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-5 bg-sv-green/12">
                        <svg class="w-5 h-5 text-sv-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="2" />
                            <path d="M2 9h20" stroke-linecap="round" stroke-width="2" />
                        </svg>
                    </div>
                    <h3 class="font-grotesk text-2xl font-bold text-sv-blue mb-3">
                        Plateforme web
                    </h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-6">
                        Explorez la carte détaillée et les graphiques. Les enseignants y disposent d'un espace pour
                        créer des sessions, suivre les analyses de leurs élèves en direct et exporter les données.
                    </p>
                    <div class="flex flex-wrap gap-2 mb-8">
                        @foreach (['Carte détaillée', 'Espace classe', 'Graphiques & normes', 'Export données'] as $tag)
                            <span
                                class="px-3 py-1 rounded-full border border-sv-green/25 text-sv-green text-xs font-medium bg-white/70">
                                {{ $tag }}
                            </span>
                        @endforeach
                    </div>
                    <a href="{{ route('desktop.dashboard') }}"
                        class="btn-lift inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-semibold bg-sv-green hover:opacity-90 transition-opacity duration-200">
                        Accéder à la plateforme web
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 px-6 relative overflow-hidden"
        style="background: linear-gradient(180deg, #1a2050 0%, #151a3a 100%);">
        <div class="absolute inset-0 opacity-5 pointer-events-none"
            style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 28px 28px;"
            aria-hidden="true"></div>

        <div class="relative z-10 max-w-7xl mx-auto">
            <div class="text-center mb-14 reveal">
                <h2 class="font-grotesk text-3xl sm:text-4xl font-bold text-white mb-3">
                    Comment ça fonctionne ?
                </h2>
                <p class="text-blue-200/65 text-base">
                    De la mesure terrain à la carte interactive, en quelques étapes.
                </p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 reveal">
                @php
                    $steps = [
                        [
                            'num' => '01',
                            'title' => 'Collecte automatique',
                            'accent' => 'blue',
                            'desc' =>
                                'Les stations LoRa mesurent turbidité, conductivité, température, hauteur et débit en continu.',
                        ],
                        [
                            'num' => '02',
                            'title' => 'Analyses participatives',
                            'accent' => 'blue',
                            'desc' => 'Tout le monde peut saisir les mesures de qualité via l\'application mobile.',
                        ],
                        [
                            'num' => '03',
                            'title' => 'Centralisation',
                            'accent' => 'green',
                            'desc' => 'Toutes les données convergent vers une base centrale hébergée chez Infomaniak.',
                        ],
                        [
                            'num' => '04',
                            'title' => 'Visualisation',
                            'accent' => 'green',
                            'desc' => 'Carte interactive, graphiques avec normes réglementaires et export des données.',
                        ],
                    ];
                @endphp
                @foreach ($steps as $step)
                    <div class="step-card group rounded-2xl p-6 border border-white/8 bg-white/[0.03]">
                        <span
                            class="font-mono text-5xl font-bold text-white/10 mb-5 block leading-none
                                     group-hover:text-white/20 transition-colors duration-300">
                            {{ $step['num'] }}
                        </span>
                        <h3 class="font-grotesk font-semibold text-white text-base mb-2">
                            {{ $step['title'] }}
                        </h3>
                        <p class="text-blue-200/60 text-sm leading-relaxed">
                            {{ $step['desc'] }}
                        </p>
                        <div
                            class="mt-5 h-0.5 rounded-full transition-all duration-500 w-8 group-hover:w-14
                            {{ $step['accent'] === 'green'
                                ? 'bg-sv-green/40 group-hover:bg-sv-green'
                                : 'bg-blue-400/40 group-hover:bg-blue-400' }}">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <footer class="bg-white border-t border-gray-100 py-6 px-6">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="{{ url('/') }}" class="gap-2">
                <span class="font-grotesk font-bold text-sv-blue">Lymnik</span>
            </a>
            <p class="text-gray-400 text-xs font-mono text-center">
                Savoirs Vivants — 2025
            </p>
            <div class="flex items-center gap-6">
                <a href="/mentions-legales"
                    class="text-gray-400 hover:text-sv-blue text-xs transition-colors duration-200">
                    Mentions légales
                </a>
                <a href="/contact" class="text-gray-400 hover:text-sv-blue text-xs transition-colors duration-200">
                    Contact
                </a>
            </div>
        </div>
    </footer>

    <script>
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, i) => {
                if (entry.isIntersecting) {
                    setTimeout(() => entry.target.classList.add('visible'), i * 90);
                }
            });
        }, {
            threshold: 0.1
        });

        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    </script>

</body>

</html>
