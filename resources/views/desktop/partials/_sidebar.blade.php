<button id="mobile-menu-btn"
    class="md:hidden fixed top-4 left-4 z-50 p-2 bg-[#222a60] text-white rounded-lg shadow-md focus:outline-none">
    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</button>

<div id="mobile-menu-overlay"
    class="md:hidden fixed inset-0 bg-black/50 z-40 hidden transition-opacity duration-300 opacity-0"></div>

<aside id="sidebar"
    class="
        fixed md:relative
        z-50 md:z-auto
        w-64 md:w-16 lg:w-64
        flex-shrink-0 bg-[#222a60] flex flex-col h-full
        transition-all duration-300 ease-in-out
        -translate-x-full md:translate-x-0
        md:overflow-visible
    ">

    <div
        class="flex items-center justify-between px-4 md:px-0 md:justify-center lg:justify-between lg:px-6 py-5 border-b border-white/10 min-h-[64px]">
        <span class="text-white font-bold text-lg tracking-tight block md:hidden lg:block">Lymnik</span> <span
            class="text-white font-black text-xl hidden md:block lg:hidden select-none">L</span>
        <button id="close-menu-btn" class="md:hidden text-white/70 hover:text-white focus:outline-none">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="flex-1 px-2 py-4 space-y-0.5 overflow-y-auto [overflow-x:visible]">

        @php
            $nav = [
                [
                    'label' => 'Dashboard',
                    'route' => 'dashboard',
                    'active_pattern' => 'dashboard*',
                    'icon' =>
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>',
                ],
                [
                    'label' => 'Carte',
                    'route' => 'map',
                    'active_pattern' => 'carte*',
                    'icon' =>
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7"/>',
                ],
                [
                    'label' => 'Analyses',
                    'route' => 'analyses.index',
                    'active_pattern' => 'analyses*',
                    'icon' =>
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
                    'sub' => [
                        [
                            'label' => 'Participants',
                            'route' => 'analyses.index',
                            'params' => ['mode' => 'participants'],
                        ],
                        ['label' => 'Campagnes', 'route' => 'analyses.index', 'params' => ['mode' => 'campagnes']],
                    ],
                ],
                [
                    'label' => 'Capteurs',
                    'route' => 'capteurs.index',
                    'active_pattern' => 'capteurs*',
                    'icon' =>
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>',
                ],
                [
                    'label' => 'Statistiques',
                    'route' => 'statistiques.index',
                    'active_pattern' => 'statistiques*',
                    'icon' =>
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>',
                ],
                [
                    'label' => 'Backoffice',
                    'route' => 'backoffice.index',
                    'active_pattern' => 'backoffice*',
                    'icon' =>
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
                ],
            ];
        @endphp

        @foreach ($nav as $item)
            @php $active = request()->is($item['active_pattern']); @endphp

            @if (isset($item['sub']))
                <div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="group/nav flex flex-col">

                    <button type="button" @click="open = !open" title="{{ $item['label'] }}"
                        class="w-full relative flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                           {{ $active ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/8 hover:text-white/90' }}
                           md:justify-center md:px-0 lg:justify-between lg:px-3 focus:outline-none">

                        <div class="flex items-center gap-3">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24" class="flex-shrink-0">
                                {!! $item['icon'] !!}
                            </svg>
                            <span class="md:hidden lg:block truncate">{{ $item['label'] }}</span>
                        </div>

                        <svg :class="open ? 'rotate-180' : ''"
                            class="w-4 h-4 text-white/50 transition-transform duration-200 md:hidden lg:block"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-200 origin-top"
                        x-transition:enter-start="opacity-0 scale-y-95 -translate-y-2"
                        x-transition:enter-end="opacity-100 scale-y-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150 origin-top"
                        x-transition:leave-start="opacity-100 scale-y-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-y-95 -translate-y-2"
                        class="md:hidden lg:block mt-1 origin-top">

                        <div class="pl-11 pr-3 py-1.5 space-y-1">
                            @foreach ($item['sub'] as $subItem)
                                @php
                                    $isSubActive =
                                        $active &&
                                        (request('mode') === $subItem['params']['mode'] ||
                                            (request('mode') === null &&
                                                $subItem['params']['mode'] === 'participants'));
                                @endphp
                                <a href="{{ route($subItem['route'], $subItem['params']) }}"
                                    class="block px-3 py-2 rounded-lg text-[13px] font-medium transition-colors
                                      {{ $isSubActive ? 'bg-white/10 text-white font-bold' : 'text-white/50 hover:bg-white/5 hover:text-white' }}">
                                    {{ $subItem['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route($item['route']) }}" title="{{ $item['label'] }}"
                    class="relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors group
                      {{ $active ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/8 hover:text-white/90' }}
                      md:justify-center md:px-0 lg:justify-start lg:px-3">

                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" class="flex-shrink-0">
                        {!! $item['icon'] !!}
                    </svg>

                    <span class="md:hidden lg:block truncate">{{ $item['label'] }}</span>

                    <span
                        class="hidden md:block lg:hidden absolute left-full ml-3 px-2.5 py-1.5 bg-[#0f143a] text-white text-xs font-semibold rounded-lg whitespace-nowrap pointer-events-none z-[60] opacity-0 group-hover:opacity-100 translate-x-1 group-hover:translate-x-0 transition-all duration-150 shadow-lg">
                        {{ $item['label'] }}
                    </span>
                </a>
            @endif
        @endforeach

    </nav>

</aside>
