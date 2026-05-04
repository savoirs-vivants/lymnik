<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionParticipant extends Model
{
    protected $fillable = ['id_session', 'id_groupe', 'pseudo'];

    public function campagne()
    {
        return $this->belongsTo(Campagne::class, 'id_session');
    }

    public function analyses()
    {
        return $this->hasMany(Analyse::class, 'participant_id');
    }
}
