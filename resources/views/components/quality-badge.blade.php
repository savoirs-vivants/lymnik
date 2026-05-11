@php
    use App\Support\QualiteConfig;
    $cfg = QualiteConfig::get($qualite ?? 'tres_bon');
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider {$cfg['bg']} {$cfg['text']}"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $cfg['dot'] }}"></span>
    {{ $cfg['label'] }}
</span>
