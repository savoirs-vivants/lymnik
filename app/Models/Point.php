<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    protected $fillable = [
        'cours_d_eau_id',
        'latitude',
        'longitude',
    ];

    public function coursDEau()
    {
        return $this->belongsTo(CoursDEau::class);
    }

    public function analyses()
    {
        return $this->hasMany(Analyse::class);
    }
}
