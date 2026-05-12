<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Capteur extends Model
{
    protected $fillable = [
        'lat',
        'long',
        'cours_d_eau_id',
        'turbidite',
        'conductivite',
        'temp_eau',
        'hauteur',
        'debit',
        'devEUI',
        'UID',
        'quali_air',
    ];

    public function coursDEau()
    {
        return $this->belongsTo(CoursDEau::class);
    }

    public function mesures()
    {
        return $this->hasMany(Mesure::class);
    }

    public function latestMesure()
    {
        return $this->hasOne(Mesure::class)->latestOfMany();
    }

    // Courbe de calibration polynomiale du fabricant (tension → NTU).
    // max(0, ...) car la courbe peut descendre en négatif pour de très faibles tensions
    // (bruit électrique en eau claire) — physiquement, une turbidité ne peut pas être négative.
    protected function turbidite(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value === null) return null;
                $calc = -1120.4 * pow($value, 2) + 5742.3 * $value - 4352.9;
                return round(max(0, $calc), 2);
            }
        );
    }

    // Courbe de calibration cubique (tension → µS/cm) avec compensation thermique.
    // La conductivité augmente ~2 % par °C, donc sans correction une eau froide
    // semblerait moins chargée en ions qu'une eau chaude de même composition.
    // 25°C est la référence ISO standard ; on l'utilise comme repli si temp_eau est absent.
    protected function conductivite(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value === null) return null;
                $ecValue            = (133.42 * pow($value, 3)) - (255.86 * pow($value, 2)) + (857.39 * $value);
                $temp               = $this->temp_eau ?? 25.0;
                $compCoef           = 1.0 + 0.02 * ($temp - 25.0);
                $ecValueCompensated = $ecValue / $compCoef;
                return round(max(0, $ecValueCompensated), 2);
            }
        );
    }
}
