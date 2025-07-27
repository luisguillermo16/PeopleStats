<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LugarVotacion extends Model
{
    use HasFactory;

    protected $table = 'lugares_votacion';

    protected $fillable = [
        'nombre',
        'direccion',
        'alcalde_id',
        'concejal_id',
    ];

    public function alcalde()
    {
        return $this->belongsTo(User::class, 'alcalde_id');
    }

    public function concejal()
    {
        return $this->belongsTo(User::class, 'concejal_id');
    }
    public function votantes()
    {
        return $this->hasMany(Votante::class, 'lugar_votacion_id');
    }
     public function mesas()
    {
        return $this->hasMany(Mesa::class);
    }
}
