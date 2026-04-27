<header class="flex-shrink-0 h-16 bg-white border-b border-slate-100 flex items-center px-8 gap-4">

    <div class="flex-1 min-w-0">
        <h1 class="text-[15px] font-bold text-[#222a60] truncate">@yield('page-title', 'Backoffice')</h1>
        @hasSection('page-subtitle')
            <p class="text-[11px] text-slate-400 font-grotesk truncate mt-0.5">@yield('page-subtitle')</p>
        @endif
    </div>

    <div class="relative group cursor-pointer" tabindex="0">

        <button
            class="flex items-center gap-1.5 pl-1.5 sm:pl-2 pr-2 sm:pr-4 py-1 hover:bg-gray-50 rounded-full transition-all focus:outline-none border border-transparent group-focus-within:border-gray-200 pointer-events-none">
            <div
                class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-[#0F143A] text-white font-black text-xs uppercase flex items-center justify-center shadow-sm">
                {{ strtoupper(substr(Auth::user()->firstname, 0, 1) . substr(Auth::user()->name, 0, 1)) }}
            </div>
            <svg class="w-3 h-3 text-gray-400 hidden sm:block transition-transform duration-200 group-focus-within:rotate-180"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div
            class="absolute right-0 top-full mt-3 w-64 bg-white rounded-[2rem] border border-gray-100 shadow-[0_20px_40px_rgb(0,0,0,0.08)] p-3 z-50
                    invisible opacity-0 translate-y-2 transition-all duration-200 ease-out
                    group-focus-within:visible group-focus-within:opacity-100 group-focus-within:translate-y-0">

            <div class="px-4 py-3 bg-gray-50/50 rounded-2xl mb-3">
                <p class="font-grotesk font-black text-[#0F143A] text-sm truncate">
                    {{ Auth::user()->firstname }} {{ Auth::user()->name }}
                </p>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate mt-0.5">
                    {{ Auth::user()->email }}
                </p>
            </div>

            <div class="space-y-1">
                <a href="{{ route('desktop.profile') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-50 hover:text-[#0F143A] transition-colors no-underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Modifier mon profil
                </a>
            </div>

            <div class="h-px bg-gray-100 my-2 mx-4"></div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold text-red-500 hover:bg-red-50 transition-colors cursor-pointer border-none outline-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Déconnexion
                </button>
            </form>

        </div>
    </div>

</header>
