<?php

namespace App\Enums;

enum AnalyseType: string
{
    case Bandelette = 'bandelette';
    case Photometre = 'photometre';
    case LesDeux    = 'les_deux';

    public function label(): string
    {
        return match ($this) {
            self::Bandelette => 'Bandelette',
            self::Photometre => 'Photomètre',
            self::LesDeux    => 'Bandelette + Photomètre',
        };
    }

    public function hasBandelette(): bool
    {
        return $this === self::Bandelette || $this === self::LesDeux;
    }

    public function hasPhotometre(): bool
    {
        return $this === self::Photometre || $this === self::LesDeux;
    }
}
