<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesure extends Model
{
    use HasFactory;

    protected $fillable = [
        'capteur_id',
        'turbidite',
        'conductivite',
        'temp_eau',
        'hauteur',
        'debit',
        'quali_air',
        'date_mesure_bluetooth',
    ];

    public function capteur()
    {
        return $this->belongsTo(Capteur::class);
    }

    // Même logique de conversion que Capteur::turbidite(). Les accesseurs sont dupliqués
    // sur les deux modèles car Mesure stocke l'historique horodaté et Capteur la dernière
    // valeur connue — les deux doivent renvoyer la valeur convertie pour cohérence d'affichage.
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
