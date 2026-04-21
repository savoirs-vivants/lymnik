<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analyse extends Model
{
    protected $casts = [
        'mesures' => 'array',
        'est_valide' => 'boolean',
    ];
}
