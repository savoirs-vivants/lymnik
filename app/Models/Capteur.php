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

    // ==========================================
    // ACCESSEUR : TURBIDITÉ (Conversion Volt -> NTU)
    // ==========================================
    protected function turbidite(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value === null) return null;

                // Calcul : -1120.4 * x² + 5742.3 * x - 4352.9
                $calc = -1120.4 * pow($value, 2) + 5742.3 * $value - 4352.9;

                // On met un max(0, $calc) car une turbidité ne peut pas être négative physiquement
                // (ça arrive si le capteur renvoie un très léger bruit électrique)
                return round(max(0, $calc), 2);
            }
        );
    }

    // ==========================================
    // ACCESSEUR : CONDUCTIVITÉ (Avec compensation de température)
    // ==========================================
    protected function conductivite(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value === null) return null;

                // 1. Calcul de base selon l'image
                $ecValue = (133.42 * pow($value, 3)) - (255.86 * pow($value, 2)) + (857.39 * $value);

                // 2. Récupération de la température (si absente, on simule 25°C par défaut)
                $temp = $this->temp_eau ?? 25.0;

                // 3. Coefficient de compensation
                $compCoef = 1.0 + 0.02 * ($temp - 25.0);

                // 4. Valeur compensée
                $ecValueCompensated = $ecValue / $compCoef;

                return round(max(0, $ecValueCompensated), 2);
            }
        );
    }
}
