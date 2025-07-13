<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Votante extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'cedula',
        'telefono',
        'direccion',
        'alcalde_id',
        'concejal_id',
        'registrado_por'
    ];

    // Relaciones
    public function alcalde(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alcalde_id');
    }

    public function concejal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'concejal_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    // Scopes para consultas
    public function scopeByAlcalde($query, $alcaldeId)
    {
        return $query->where('alcalde_id', $alcaldeId);
    }

    public function scopeByConcejal($query, $concejalId)
    {
        return $query->where('concejal_id', $concejalId);
    }

    public function scopeByCedula($query, $cedula)
    {
        return $query->where('cedula', $cedula);
    }

    // Validaciones personalizadas
    public static function validarVotanteUnico($cedula, $alcaldeId = null, $concejalId = null, $idExcluir = null)
    {
        $query = self::where('cedula', $cedula);
        
        if ($idExcluir) {
            $query->where('id', '!=', $idExcluir);
        }

        // Validar que no exista con otro concejal del mismo alcalde
        if ($concejalId && $alcaldeId) {
            $concejalDelMismoAlcalde = User::where('id', '!=', $concejalId)
                ->where('alcalde_id', $alcaldeId)
                ->where(function($q) {
                    $q->whereHas('roles', function($role) {
                        $role->where('name', 'concejal');
                    });
                })
                ->pluck('id');

            $existeConOtroConcejal = $query->whereIn('concejal_id', $concejalDelMismoAlcalde)->exists();
            
            if ($existeConOtroConcejal) {
                return [
                    'valido' => false,
                    'mensaje' => 'Este votante ya está registrado con otro concejal del mismo alcalde.'
                ];
            }
        }

        // Validar que no exista ya con el mismo alcalde (para caso B)
        if ($alcaldeId && !$concejalId) {
            $existeConAlcalde = $query->where('alcalde_id', $alcaldeId)->exists();
            
            if ($existeConAlcalde) {
                return [
                    'valido' => false,
                    'mensaje' => 'Este votante ya está registrado con este alcalde.',
                    'permitir_modificacion' => true
                ];
            }
        }

        return ['valido' => true];
    }

    // Método para obtener votantes por alcalde incluyendo sus concejales
    public static function votantesPorAlcalde($alcaldeId)
    {
        $concejalIds = User::where('alcalde_id', $alcaldeId)
            ->whereHas('roles', function($q) {
                $q->where('name', 'concejal');
            })
            ->pluck('id');

        return self::where(function($query) use ($alcaldeId, $concejalIds) {
            $query->where('alcalde_id', $alcaldeId)
                  ->orWhereIn('concejal_id', $concejalIds);
        });
    }
}