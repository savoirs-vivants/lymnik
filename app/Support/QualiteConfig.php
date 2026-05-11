<?php

namespace App\Support;

class QualiteConfig
{
    private const CONFIG = [
        'tres_bon' => [
            'label'  => 'Très bon',
            'bg'     => 'bg-emerald-100',
            'text'   => 'text-emerald-700',
            'dot'    => 'bg-emerald-500',
            'border' => 'border-emerald-200',
            'chart'  => '#10b981',
        ],
        'bon' => [
            'label'  => 'Bon',
            'bg'     => 'bg-teal-100',
            'text'   => 'text-teal-700',
            'dot'    => 'bg-teal-500',
            'border' => 'border-teal-200',
            'chart'  => '#14b8a6',
        ],
        'passable' => [
            'label'  => 'Passable',
            'bg'     => 'bg-yellow-100',
            'text'   => 'text-yellow-700',
            'dot'    => 'bg-yellow-400',
            'border' => 'border-yellow-200',
            'chart'  => '#eab308',
        ],
        'mediocre' => [
            'label'  => 'Médiocre',
            'bg'     => 'bg-orange-100',
            'text'   => 'text-orange-700',
            'dot'    => 'bg-orange-400',
            'border' => 'border-orange-200',
            'chart'  => '#f97316',
        ],
        'mauvais' => [
            'label'  => 'Mauvais',
            'bg'     => 'bg-red-100',
            'text'   => 'text-red-700',
            'dot'    => 'bg-red-500',
            'border' => 'border-red-200',
            'chart'  => '#ef4444',
        ],
    ];

    public static function get(string $qualite): array
    {
        return self::CONFIG[$qualite] ?? self::CONFIG['tres_bon'];
    }

    public static function all(): array
    {
        return self::CONFIG;
    }
}
