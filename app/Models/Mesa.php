<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    use HasFactory;

    protected $fillable = ['numero', 'lugar_votacion_id'];

    public function lugar()
    {
        return $this->belongsTo(LugarVotacion::class, 'lugar_votacion_id');
    }
}
