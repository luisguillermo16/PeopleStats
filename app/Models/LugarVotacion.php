<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class LugarVotacion extends Model
{
    use HasFactory;

    protected $table = 'lugares_votacion'; // O el nombre de tu tabla
    
    protected $fillable = [
        'nombre',
        'direccion',
        'alcalde_id',
        'concejal_id'
    ];

    public function mesas()
    {
        return $this->hasMany(Mesa::class, 'lugar_votacion_id');
    }
}
