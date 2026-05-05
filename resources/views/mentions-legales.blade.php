<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentions Légales - Lymnik</title>

    <!-- Polices -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700;900&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap"
        rel="stylesheet">

    @vite('resources/css/app.css')

    <style>
        body {
            font-family: 'Space Grotesk', sans-serif;
        }

        .font-mono {
            font-family: 'Space Mono', monospace;
        }

        .font-grotesk {
            font-family: 'Space Grotesk', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased min-h-screen py-10 px-4 sm:px-6 flex items-center justify-center">

    <div class="max-w-3xl w-full">
        <div
            class="bg-white rounded-2xl sm:rounded-[2rem] border border-slate-100 shadow-[0_8px_30px_rgba(34,42,96,0.04)] overflow-hidden relative">

            <!-- Liseré de couleur en haut -->
            <div class="h-2 w-full bg-gradient-to-r from-[#222a60] to-[#1565c0]"></div>

            <div class="p-6 sm:p-8 lg:p-12">
                <div class="flex items-center gap-4 mb-8 border-b border-slate-50 pb-6">
                    <div
                        class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-[#1565c0] shrink-0">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black text-[#222a60] font-grotesk tracking-tight">Mentions
                            Légales</h1>
                        <p class="text-xs sm:text-sm text-slate-500 font-mono mt-1">Édition, hébergement et
                            responsabilités</p>
                    </div>
                </div>

                <div class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h2 class="text-base font-bold text-slate-800 mb-3 flex items-center gap-2">
                                <span class="w-1.5 h-4 bg-[#16987C] rounded-full"></span>
                                1. Éditeur de l'application
                            </h2>
                            <div class="text-sm text-slate-600 leading-relaxed space-y-2">
                                <p>L'application <strong>Lymnik</strong> est éditée par l’association <strong>Savoirs
                                        Vivants</strong>.</p>
                                <p><span class="text-slate-400 font-mono text-[11px] uppercase tracking-wider block">Forme
                                        juridique</span> Association de droit local</p>
                                <p><span class="text-slate-400 font-mono text-[11px] uppercase tracking-wider block">Siège
                                        social</span> 30 rue du Maire André Traband, 67500 Haguenau</p>
                                <p><span
                                        class="text-slate-400 font-mono text-[11px] uppercase tracking-wider block">Contact</span>
                                    <a href="mailto:contact@savoirsvivants.fr"
                                        class="text-[#1565c0] font-bold hover:underline">contact@savoirsvivants.fr</a>
                                </p>
                            </div>
                        </div>

                        <div>
                            <h2 class="text-base font-bold text-slate-800 mb-3 flex items-center gap-2">
                                <span class="w-1.5 h-4 bg-[#16987C] rounded-full"></span>
                                2. Responsable de publication
                            </h2>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Le responsable de la publication est <strong>l’équipe de Savoirs Vivants</strong>.
                            </p>

                            <h2 class="text-base font-bold text-slate-800 mb-3 mt-8 flex items-center gap-2">
                                <span class="w-1.5 h-4 bg-[#16987C] rounded-full"></span>
                                3. Hébergement
                            </h2>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                L'application et le site sont hébergés par <strong>Infomaniak</strong>, situé en Suisse.
                            </p>
                        </div>
                    </div>

                    <div class="bg-slate-50 border border-slate-100 p-5 sm:p-6 rounded-2xl">
                        <h2 class="text-base font-bold text-slate-800 mb-2 flex items-center gap-2">
                            4. Propriété intellectuelle
                        </h2>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Les contenus (textes, images, graphismes, logo, vidéos, documents téléchargeables,
                            questionnaires) présents sur l'application sont la propriété exclusive de <strong>Savoirs
                                Vivants</strong> ou de tiers ayant autorisé leur usage. Toute reproduction,
                            distribution, modification ou publication, même partielle, est interdite sans l’accord écrit
                            préalable de l’association.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-slate-800 mb-3 flex items-center gap-2">
                            <span class="w-1.5 h-4 bg-[#16987C] rounded-full"></span>
                            5. Responsabilité
                        </h2>
                        <p class="text-sm text-slate-600 leading-relaxed mb-3">
                            Savoirs Vivants met tout en œuvre pour assurer l’exactitude et la mise à jour des
                            informations figurant sur l'application. Cependant, l’association ne saurait être tenue
                            responsable :
                        </p>
                        <ul class="space-y-2 text-sm text-slate-600 leading-relaxed ml-1">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-slate-400 mt-0.5 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Des erreurs, omissions ou résultats obtenus par un mauvais usage des informations du
                                site.
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-slate-400 mt-0.5 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                De l’interruption ou de l’indisponibilité temporaire de l'application.
                            </li>
                        </ul>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-slate-50">
                        <div>
                            <h2 class="text-base font-bold text-slate-800 mb-3 flex items-center gap-2">
                                <span class="w-1.5 h-4 bg-[#16987C] rounded-full"></span>
                                6. Données personnelles
                            </h2>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Le traitement des données personnelles est décrit dans notre Politique de
                                confidentialité. Les données collectées sont utilisées uniquement pour répondre à votre
                                demande et pour les finalités prévues par l'application.
                            </p>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800 mb-3 flex items-center gap-2">
                                <span class="w-1.5 h-4 bg-[#16987C] rounded-full"></span>
                                7. Droit applicable
                            </h2>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                La présente application est soumise au droit français. En cas de litige, les tribunaux
                                français seront seuls compétents, sauf disposition légale contraire.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
