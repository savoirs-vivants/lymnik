<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouleeDeBoue extends Model
{
    protected $table = 'coulees_de_boue';
    protected $fillable = ['lat', 'lng', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
