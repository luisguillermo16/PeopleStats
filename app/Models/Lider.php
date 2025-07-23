<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Concejal;
use App\Models\Alcalde;

class Lider extends Model
{
    use HasFactory;
    
    protected $table = 'lideres';

    protected $fillable = [
        'user_id',
        'concejal_id',
        'alcalde_id',
      
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el concejal que lo creó
     */
   public function concejal()
{
    return $this->belongsTo(User::class, 'concejal_id');
}

public function alcalde()
{
    return $this->belongsTo(User::class, 'alcalde_id');
}

    /**
     * Scope para filtrar por zona de influencia
     */
    public function scopeByZona($query, $zona)
    {
        return $query->where('zona_influencia', $zona);
    }

    /**
     * Scope para filtrar por afiliación política
     */
    public function scopeByAfiliacion($query, $afiliacion)
    {
        return $query->where('afiliacion_politica', $afiliacion);
    }

    /**
     * Accessor para obtener el nombre del creador
     */
    public function getCreadoPorAttribute()
    {
        if ($this->concejal) {
            return $this->concejal->user->name ?? $this->concejal->name ?? 'Concejal';
        }
        
        if ($this->alcalde) {
            return $this->alcalde->user->name ?? $this->alcalde->name ?? 'Alcalde';
        }
        
        return 'Sin información';
    }

    /**
     * Accessor para obtener el tipo de creador
     */
    public function getTipoCreadoPorAttribute()
    {
        if ($this->concejal) {
            return 'Concejal';
        }
        
        if ($this->alcalde) {
            return 'Alcalde';
        }
        
        return null;
    }
}