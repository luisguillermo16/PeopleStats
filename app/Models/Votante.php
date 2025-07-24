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
        'mesa',
        'user_id',
        'lider_id',
        'concejal_id',
        'alcalde_id',
     
    ];
    public static function validarVotanteUnico($cedula, $user_id)
    {
        return self::where('cedula', $cedula)
                   ->where('user_id', $user_id)
                   ->exists();
    }

    protected $casts = [
        'tambien_vota_alcalde' => 'boolean',
    ];

    // Relación con el usuario que registró al votante
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con el líder (opcional, si tienes modelo)
    public function lider()
    {
        return $this->belongsTo(User::class, 'lider_id');
    }

    // Relación con concejal
    public function concejal()
    {
        return $this->belongsTo(Concejal::class);
    }

    // Relación con alcalde
    public function alcalde()
    {
        return $this->belongsTo(Alcalde::class);
    }
}
