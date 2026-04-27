<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Capteur extends Model
{
    protected $fillable = [
        'lat',
        'long',
        'turbidite',
        'conductivite',
        'temp_eau',
        'hauteur',
        'debit',
    ];

    public function mesures()
    {
        return $this->hasMany(Mesure::class);
    }
}
