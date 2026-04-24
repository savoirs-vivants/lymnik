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
}
