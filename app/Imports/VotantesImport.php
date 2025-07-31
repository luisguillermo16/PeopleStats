<?php

namespace App\Imports;

use App\Models\Votante;
use App\Models\LugarVotacion;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VotantesImport implements ToModel, WithHeadingRow
{
    private $lider;
    private $cedulasVistas = [];
    public $saltados = 0;  
    public $importados = 0;
    public $errores = [];  // Aquí almacenamos los mensajes detallados

    public function __construct($lider)
    {
        $this->lider = $lider;
    }

    public function model(array $row)
    {
        $cedula = $row['cedula'] ?? 'desconocida';

        // Validar lugar de votacion
        $lugarNombre = trim($row['lugar_votacion'] ?? '');
        $lugar = LugarVotacion::where('nombre', $lugarNombre)->first();

        if (!$lugar) {
            $this->saltados++;
            $this->errores[] = "Cédula {$cedula}: Lugar de votación '{$lugarNombre}' no encontrado.";
            return null;
        }

        // Validar concejal si existe
        $concejalNombre = trim($row['concejal'] ?? '');
        $concejal = null;
        if ($concejalNombre !== '') {
            $concejal = User::where('name', $concejalNombre)->role('aspirante-concejo')->first();

            if (!$concejal) {
                $this->saltados++;
                $this->errores[] = "Cédula {$cedula}: Concejal '{$concejalNombre}' no existe o no tiene rol 'aspirante-concejo'.";
                return null;
            }
        }

        // Validar duplicados en el archivo
        if (in_array($cedula, $this->cedulasVistas)) {
            $this->saltados++;
            $this->errores[] = "Cédula {$cedula}: Duplicada en el archivo de importación.";
            return null;
        }
        $this->cedulasVistas[] = $cedula;

        // Validar duplicados en BD
        if (Votante::where('cedula', $cedula)->exists()) {
            $this->saltados++;
            $this->errores[] = "Cédula {$cedula}: Ya existe en la base de datos.";
            return null;
        }

        // Si pasa todas las validaciones, crear el votante
        $this->importados++;

        $votante = new Votante([
            'nombre'            => $row['nombre'] ?? null,
            'cedula'            => $cedula,
            'telefono'          => $row['telefono'] ?? null,
            'mesa'              => $row['mesa'] ?? null,
            'lugar_votacion_id' => $lugar->id,
        ]);

        $votante->lider_id = $this->lider->id;

        if ($this->lider->concejal_id) {
            $votante->concejal_id = $this->lider->concejal_id;

            if (isset($row['alcalde_id']) && intval($row['alcalde_id']) === 1) {
                $votante->alcalde_id = $this->lider->alcalde_id;
            }
        } elseif ($this->lider->alcalde_id) {
            $votante->alcalde_id = $this->lider->alcalde_id;

            if ($concejal) {
                $votante->concejal_id = $concejal->id;
            }
        }

        return $votante;
    }
}
