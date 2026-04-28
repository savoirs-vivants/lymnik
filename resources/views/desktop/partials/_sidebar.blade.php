<aside class="w-64 flex-shrink-0 bg-[#222a60] flex flex-col h-full">

    <div class="gap-3 px-6 py-5 border-b border-white/10">
        <span class="text-white font-bold text-lg tracking-tight">Lymnik</span>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

        @php
            $nav = [
                [
                    'label' => 'Dashboard',
                    'route' => 'dashboard',
                    'icon' =>
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>',
                ],
                [
                    'label' => 'Carte',
                    'route' => 'map',
                    'icon' =>
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7"/>',
                ],
                [
                    'label' => 'Analyses',
                    'route' => '#',
                    'icon' =>
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
                ],
                [
                    'label' => 'Capteurs',
                    'route' => 'capteurs.index',
                    'icon' =>
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>',
                ],
                [
                    'label' => 'Statistiques',
                    'route' => '#',
                    'icon' =>
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 001-1 1H5a1 1 0 01-1-1V4z"/>',
                ],
                [
                    'label' => 'Backoffice',
                    'route' => 'backoffice',
                    'icon' =>
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
                ],
            ];
        @endphp


        @foreach ($nav as $item)
            @php $active = request()->routeIs($item['route'] . '*'); @endphp
            <a href="{{ $item['route'] === '#' ? 'javascript:void(0)' : route($item['route']) }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ $active ? 'bg-white/15 text-white' : 'text-white/55 hover:bg-white/8 hover:text-white/90' }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                    class="flex-shrink-0">
                    {!! $item['icon'] !!}
                </svg>
                {{ $item['label'] }}
            </a>
        @endforeach

    </nav>

</aside>
