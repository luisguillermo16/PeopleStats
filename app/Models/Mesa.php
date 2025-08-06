<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    protected $fillable = [
        'numero',
        'lugar_votacion_id'
    ];

     public function lugarVotacion()
    {
        return $this->belongsTo(LugarVotacion::class, 'lugar_votacion_id');
    }
}