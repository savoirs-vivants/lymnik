<?php

namespace App\Enums;

enum Qualite: string
{
    case TresBon  = 'tres_bon';
    case Bon      = 'bon';
    case Passable = 'passable';
    case Mediocre = 'mediocre';
    case Mauvais  = 'mauvais';

    public function label(): string
    {
        return match ($this) {
            self::TresBon  => 'Très bon',
            self::Bon      => 'Bon',
            self::Passable => 'Passable',
            self::Mediocre => 'Médiocre',
            self::Mauvais  => 'Mauvais',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TresBon  => '#22c55e',
            self::Bon      => '#84cc16',
            self::Passable => '#eab308',
            self::Mediocre => '#f97316',
            self::Mauvais  => '#ef4444',
        };
    }

    public function tailwindBg(): string
    {
        return match ($this) {
            self::TresBon  => 'bg-green-100 text-green-800',
            self::Bon      => 'bg-lime-100 text-lime-800',
            self::Passable => 'bg-yellow-100 text-yellow-800',
            self::Mediocre => 'bg-orange-100 text-orange-800',
            self::Mauvais  => 'bg-red-100 text-red-800',
        };
    }

    /** Ordre de sévérité (plus grand = pire) */
    public function severity(): int
    {
        return match ($this) {
            self::TresBon  => 0,
            self::Bon      => 1,
            self::Passable => 2,
            self::Mediocre => 3,
            self::Mauvais  => 4,
        };
    }

    /** Retourne la qualite la plus sévère parmi une collection de valeurs string */
    public static function worst(array $values): self
    {
        $worst = self::TresBon;
        foreach ($values as $v) {
            $q = self::tryFrom($v);
            if ($q && $q->severity() > $worst->severity()) {
                $worst = $q;
            }
        }
        return $worst;
    }
}
