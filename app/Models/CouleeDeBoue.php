<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouleeDeBoue extends Model
{
    protected $table = 'coulees_de_boue';
    protected $fillable = ['lat', 'lng', 'type', 'date', 'image', 'user_id'];
    protected $casts = ['date' => 'date'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
