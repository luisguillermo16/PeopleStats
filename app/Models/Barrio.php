<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barrio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'alcalde_id',
    ];

    /**
     * Relación con los votantes que pertenecen a este barrio.
     */
    public function votantes()
    {
        return $this->hasMany(Votante::class);
    }

    /**
     * Relación con el alcalde que creó el barrio (usuario).
     */
    public function alcalde()
    {
        return $this->belongsTo(User::class, 'alcalde_id');
    }
}
