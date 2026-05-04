<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Campagne extends Model
{
    protected $fillable = ['nom', 'id_gestionnaire', 'nb_groupes'];

    protected static function booted()
    {
        static::creating(function ($campagne) {
            $campagne->code = strtoupper(Str::random(8));
        });
    }

    public function participants()
    {
        return $this->hasMany(SessionParticipant::class, 'id_session');
    }

    public function analyses()
    {
        return $this->hasMany(Analyse::class, 'session_id');
    }
}
