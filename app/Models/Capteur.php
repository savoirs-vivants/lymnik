<?php

namespace App\Models;

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
}
