<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politique de Confidentialité - Lymnik</title>

    <!-- Polices -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700;900&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

    <!-- CSS (Si tu es sous Laravel Vite) -->
    @vite('resources/css/app.css')

    <style>
        body { font-family: 'Space Grotesk', sans-serif; }
        .font-mono { font-family: 'Space Mono', monospace; }
        .font-grotesk { font-family: 'Space Grotesk', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen py-10 px-4 sm:px-6 flex items-center justify-center">

    <div class="max-w-3xl w-full">
        <div class="bg-white rounded-2xl sm:rounded-[2rem] border border-slate-100 shadow-[0_8px_30px_rgba(34,42,96,0.04)] overflow-hidden relative">

            <!-- Liseré de couleur en haut -->
            <div class="h-2 w-full bg-gradient-to-r from-[#16987C] to-[#10b981]"></div>

            <div class="p-6 sm:p-8 lg:p-12">
                <div class="flex items-center gap-4 mb-8 border-b border-slate-50 pb-6">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-[#16987C] shrink-0">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black text-[#222a60] font-grotesk tracking-tight">Confidentialité</h1>
                        <p class="text-xs sm:text-sm text-slate-500 font-mono mt-1">Protection de vos données personnelles (RGPD) - Plateforme Lymnik</p>
                    </div>
                </div>

                <div class="mb-8">
                    <p class="text-sm text-slate-600 leading-relaxed p-5 bg-slate-50 rounded-xl border border-slate-100">
                        <strong>Traitement des Données :</strong> L'association Savoirs Vivants s'engage à ce que les traitements de données effectués sur la plateforme Lymnik de suivi de la qualité des cours d'eau soient conformes au Règlement Général sur la Protection des Données (RGPD).
                    </p>
                </div>

                <div class="space-y-8">
                    <div>
                        <h2 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <span class="w-1.5 h-4 bg-[#222a60] rounded-full"></span>
                            1. Données collectées et Finalités
                        </h2>
                        <p class="text-sm text-slate-600 leading-relaxed mb-4">
                            Les données collectées servent exclusivement au fonctionnement de la plateforme et se divisent en catégories liées à la surveillance participative de la qualité de l'eau :
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="border border-slate-100 rounded-xl p-4 shadow-sm hover:border-slate-200 transition-colors">
                                <h3 class="text-[13px] font-bold text-[#222a60] mb-2">Comptes utilisateurs</h3>
                                <p class="text-[13px] text-slate-500 leading-relaxed">Identité (prénom, nom), email. Permet l'accès sécurisé et la gestion des campagnes.</p>
                            </div>
                            <div class="border border-slate-100 rounded-xl p-4 shadow-sm hover:border-slate-200 transition-colors">
                                <h3 class="text-[13px] font-bold text-[#222a60] mb-2">Sessions participantes</h3>
                                <p class="text-[13px] text-slate-500 leading-relaxed">Données de sessions élèves, analyses effectuées. Suivi pédagogique et scientifique des mesures en rivières.</p>
                            </div>
                            <div class="border border-slate-100 rounded-xl p-4 shadow-sm hover:border-slate-200 transition-colors">
                                <h3 class="text-[13px] font-bold text-[#222a60] mb-2">Données scientifiques</h3>
                                <p class="text-[13px] text-slate-500 leading-relaxed">Mesures de qualité d'eau (pH, nitrates, etc.), coordonnées géographiques (points de mesure), données capteurs Bbox. Cartographie et statistiques qualité des cours d'eau.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h2 class="text-base font-bold text-slate-800 mb-3 flex items-center gap-2">
                                <span class="w-1.5 h-4 bg-[#222a60] rounded-full"></span>
                                2. Base légale
                            </h2>
                            <ul class="space-y-2 text-sm text-slate-600 leading-relaxed ml-1">
                                <li class="flex items-start gap-2">
                                    <span class="text-[#16987C] font-bold mt-0.5">•</span>
                                    <strong>Exécution du contrat</strong> (création/compte et utilisation plateforme).
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-[#16987C] font-bold mt-0.5">•</span>
                                    <strong>Intérêt légitime</strong> (statistiques agrégées, amélioration service).
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-[#16987C] font-bold mt-0.5">•</span>
                                    <strong>Consentement</strong> (partage données personnelles au-delà du strict nécessaire).
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800 mb-3 flex items-center gap-2">
                                <span class="w-1.5 h-4 bg-[#222a60] rounded-full"></span>
                                3. Conservation
                            </h2>
                            <ul class="space-y-2 text-sm text-slate-600 leading-relaxed ml-1">
                                <li><strong>Comptes utilisateurs :</strong> Tant que le compte est actif, ou jusqu'à demande de suppression.</li>
                                <li><strong>Données sessions/analyses :</strong> Durée de la campagne + 5 ans (suivi scientifique).</li>
                                <li><strong>Données scientifiques agrégées :</strong> Conservation indéfinie pour recherche et cartographie.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h2 class="text-base font-bold text-slate-800 mb-3 flex items-center gap-2">
                                <span class="w-1.5 h-4 bg-[#222a60] rounded-full"></span>
                                4. Hébergement et Sécurité
                            </h2>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                La plateforme est hébergée par <strong>Infomaniak</strong> (Suisse). Tous les échanges sont sécurisés par HTTPS. Laravel intègre protections CSRF, validation données, et hachage mots de passe.
                            </p>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800 mb-3 flex items-center gap-2">
                                <span class="w-1.5 h-4 bg-[#222a60] rounded-full"></span>
                                5. Cookies
                            </h2>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Lymnik utilise <strong>uniquement des cookies strictement nécessaires</strong> (sessions utilisateur, protection CSRF). Aucun cookie de suivi, publicité ou analyse tiers n'est déposé.
                            </p>
                        </div>
                    </div>

                    <div class="bg-blue-50/50 border border-blue-100 p-6 sm:p-8 rounded-2xl">
                        <h2 class="text-base font-bold text-[#222a60] mb-3 flex items-center gap-2">
                            6. Vos droits RGPD
                        </h2>
                        <p class="text-sm text-slate-600 leading-relaxed mb-5">
                            Accès, rectification, suppression (« droit à l'oubli »), opposition, limitation, portabilité de vos données. Les données scientifiques agrégées/anonymisées ne relèvent pas du droit à suppression.
                        </p>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <p class="text-sm text-slate-600 font-medium">Exercer vos droits :</p>
                            <a href="mailto:contact@savoirsvivants.fr" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#222a60] hover:bg-[#1a204d] text-white text-sm font-bold rounded-xl transition-colors shadow-sm">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                contact@savoirsvivants.fr
                            </a>
                        </div>
                        <p class="font-mono text-[10px] text-slate-400 mt-6 uppercase tracking-wide mb-0.5">
                            Dernière mise à jour : Octobre 2024
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>

</body>
</html>

