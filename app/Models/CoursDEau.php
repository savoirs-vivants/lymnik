<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursDEau extends Model
{
    protected $table    = 'cours_d_eaus';
    protected $fillable = [
        'nom',
        'type_cours',
        'trace',
        'bbox_min_lng',
        'bbox_min_lat',
        'bbox_max_lng',
        'bbox_max_lat',
    ];

    public function points()
    {
        return $this->hasMany(Point::class);
    }
}
