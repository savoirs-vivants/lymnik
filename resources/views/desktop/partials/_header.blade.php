<header class="flex-shrink-0 h-16 bg-white border-b border-slate-100 flex items-center px-8 gap-4">

    <div class="flex-1 min-w-0">
        <h1 class="text-[15px] font-bold text-[#222a60] truncate">@yield('page-title', 'Backoffice')</h1>
        @hasSection('page-subtitle')
            <p class="text-[11px] text-slate-400 font-grotesk truncate mt-0.5">@yield('page-subtitle')</p>
        @endif
    </div>

    @if($invalidAnalysesCount > 0)
    <button id="btn-invalides" onclick="openInvalidesOverlay()"
        class="relative flex items-center gap-2 px-3 py-1.5 rounded-xl bg-red-50 border border-red-100 hover:bg-red-100 transition-colors group">
        <svg width="14" height="14" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <span id="invalides-count" class="text-xs font-bold text-red-600">{{ $invalidAnalysesCount }} invalide{{ $invalidAnalysesCount > 1 ? 's' : '' }}</span>
    </button>
    @else
    <button id="btn-invalides" class="hidden" onclick="openInvalidesOverlay()"></button>
    @endif

    <div class="relative group cursor-pointer" tabindex="0">
        <button class="flex items-center gap-1.5 pl-1.5 sm:pl-2 pr-2 sm:pr-4 py-1 hover:bg-gray-50 rounded-full transition-all focus:outline-none border border-transparent group-focus-within:border-gray-200 pointer-events-none">
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-[#0F143A] text-white font-black text-xs uppercase flex items-center justify-center shadow-sm">
                {{ strtoupper(substr(Auth::user()->firstname, 0, 1) . substr(Auth::user()->name, 0, 1)) }}
            </div>
            <svg class="w-3 h-3 text-gray-400 hidden sm:block transition-transform duration-200 group-focus-within:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div class="absolute right-0 top-full mt-3 w-64 bg-white rounded-[2rem] border border-gray-100 shadow-[0_20px_40px_rgb(0,0,0,0.08)] p-3 z-50
                    invisible opacity-0 translate-y-2 transition-all duration-200 ease-out
                    group-focus-within:visible group-focus-within:opacity-100 group-focus-within:translate-y-0">
            <div class="px-4 py-3 bg-gray-50/50 rounded-2xl mb-3">
                <p class="font-grotesk font-black text-[#0F143A] text-sm truncate">{{ Auth::user()->firstname }} {{ Auth::user()->name }}</p>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate mt-0.5">{{ Auth::user()->email }}</p>
            </div>
            <div class="space-y-1">
                <a href="{{ route('profil.edit') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-50 hover:text-[#0F143A] transition-colors no-underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Modifier mon profil
                </a>
            </div>
            <div class="h-px bg-gray-100 my-2 mx-4"></div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold text-red-500 hover:bg-red-50 transition-colors cursor-pointer border-none outline-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Déconnexion
                </button>
            </form>
        </div>
    </div>

</header>

{{-- ═══════════════════════════════════════════════════════════
     OVERLAY CARROUSEL — ANALYSES INVALIDES
═══════════════════════════════════════════════════════════ --}}
<div id="invalides-overlay" class="fixed inset-0 z-[200] hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeInvalidesOverlay()"></div>

    <div class="absolute inset-0 flex items-center justify-center p-6 pointer-events-none">
        <div id="invalides-card" class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl pointer-events-auto overflow-hidden"
            style="max-height: calc(100vh - 3rem);">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-red-50 flex items-center justify-center">
                        <svg width="14" height="14" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">Analyse invalide</p>
                        <p id="invalides-counter" class="text-[10px] font-mono text-slate-400"></p>
                    </div>
                </div>
                <button onclick="closeInvalidesOverlay()" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition-colors">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div id="invalides-body" class="overflow-y-auto" style="max-height: calc(100vh - 12rem);">

                <div id="invalides-loading" class="flex items-center justify-center py-20">
                    <div class="flex gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-slate-300 animate-bounce" style="animation-delay:0s"></span>
                        <span class="w-2 h-2 rounded-full bg-slate-300 animate-bounce" style="animation-delay:.15s"></span>
                        <span class="w-2 h-2 rounded-full bg-slate-300 animate-bounce" style="animation-delay:.3s"></span>
                    </div>
                </div>

                <div id="invalides-content" class="hidden">

                    <div id="invalides-map" class="w-full h-44 bg-slate-100"></div>

                    <div class="p-6 space-y-5">

                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p id="inv-cours-eau" class="text-base font-bold text-[#222a60]"></p>
                                <p id="inv-meta" class="text-[11px] text-slate-400 font-mono mt-0.5"></p>
                            </div>
                            <span id="inv-qualite-badge" class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold"></span>
                        </div>

                        <div>
                            <p class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-2">Mesures</p>
                            <div id="inv-mesures" class="grid grid-cols-3 gap-2"></div>
                        </div>

                        <div id="inv-note-block" class="hidden">
                            <p class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-1">Note</p>
                            <p id="inv-note" class="text-sm text-slate-600 bg-slate-50 rounded-xl px-3 py-2.5"></p>
                        </div>

                        <div id="inv-photo-block" class="hidden">
                            <p class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-400 mb-2">Photo</p>
                            <img id="inv-photo" src="" alt="" class="rounded-2xl max-h-40 object-cover border border-slate-100 w-full">
                        </div>

                        <div class="bg-red-50 border border-red-100 rounded-2xl px-4 py-3">
                            <p class="text-xs font-semibold text-red-700 flex items-center gap-1.5">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01"/></svg>
                                Cette analyse contient des valeurs dépassant les seuils de validité.
                            </p>
                        </div>

                        <div class="flex items-center gap-3 pt-1">
                            <button id="btn-valider" onclick="validerCurrent()"
                                class="flex-1 flex items-center justify-center gap-2 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold transition-colors">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                Valider cette analyse
                            </button>
                            <button onclick="skipCurrent()"
                                class="px-4 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold transition-colors">
                                Passer
                            </button>
                        </div>

                    </div>
                </div>

                <div id="invalides-empty" class="hidden flex flex-col items-center justify-center py-16 text-center px-8">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-500 mb-4">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-slate-700">Tout est en ordre !</p>
                    <p class="text-xs text-slate-400 mt-1">Il n'y a plus d'analyses invalides à valider.</p>
                    <button onclick="closeInvalidesOverlay()" class="mt-5 px-5 py-2.5 rounded-xl bg-[#222a60] text-white text-sm font-semibold hover:bg-[#1a2050] transition-colors">
                        Fermer
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    window.__HEADER_CONFIG = {
        invalidesUrl: '{{ route("analyses.invalides") }}',
        csrfToken:    '{{ csrf_token() }}',
    };
</script>
