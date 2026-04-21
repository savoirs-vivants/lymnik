<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursDEau extends Model
{
    protected $casts = [
        'trace' => 'array',
    ];
}
