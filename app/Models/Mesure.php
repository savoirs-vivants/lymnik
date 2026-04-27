<?php

namespace App\Models;

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
    ];

    public function capteur()
    {
        return $this->belongsTo(Capteur::class);
    }
}
