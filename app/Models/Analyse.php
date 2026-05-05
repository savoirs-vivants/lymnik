<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analyse extends Model
{
    protected $fillable = [
        'point_id',
        'type',
        'image',
        'mesures',
        'est_valide',
        'user_id',
        'qualite',
        'participant_id',
        'session_id',
        'nom',
    ];

    protected $casts = [
        'mesures' => 'array',
        'est_valide' => 'boolean',
    ];

    public function point()
    {
        return $this->belongsTo(Point::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function participant()
    {
        return $this->belongsTo(SessionParticipant::class, 'participant_id');
    }

    public function campagne()
    {
        return $this->belongsTo(Campagne::class, 'session_id');
    }
}
