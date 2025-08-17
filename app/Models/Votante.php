<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Votante extends Model
{
    use HasFactory;

    protected $table = 'votantes';

    protected $fillable = [
        'nombre',
        'cedula',
        'telefono',
        'mesa_id',
        'user_id',
        'lider_id',
        'concejal_id',
        'alcalde_id',
        'lugar_votacion_id', 
        'barrio_id',
        
    ];

   

    public static function validarVotanteUnico($cedula, $liderId)
    {
        return self::where('cedula', $cedula)
                   ->where('lider_id', $liderId)
                   ->exists();
    }

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lider()
    {
        return $this->belongsTo(User::class, 'lider_id');
    }

    public function concejal()
    {
        return $this->belongsTo(User::class, 'concejal_id');
    }

    public function alcalde()
    {
        return $this->belongsTo(User::class, 'alcalde_id');
    }

    public function lugarVotacion()
    {
        return $this->belongsTo(LugarVotacion::class, 'lugar_votacion_id');
    }
        public function barrio()
    {
        return $this->belongsTo(Barrio::class, 'barrio_id');
    }
    public function mesa()
{
    return $this->belongsTo(Mesa::class, 'mesa_id');
}
}
