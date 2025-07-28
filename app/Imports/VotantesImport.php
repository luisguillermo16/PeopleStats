<?php

namespace App\Imports;

use App\Models\Votante;
use App\Models\LugarVotacion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VotantesImport implements ToModel, WithHeadingRow
{
    private $lider;
    private $cedulasVistas = [];
    public $saltados = 0;  // Contador de duplicados
    public $importados = 0; // Contador de importados correctos

    public function __construct($lider)
    {
        $this->lider = $lider;
    }

    public function model(array $row)
    {
        // 1. Validar que el lugar existe
        $lugar = LugarVotacion::find($row['lugar_votacion_id']);
        if (!$lugar) {
            $this->saltados++;
            return null;
        }

        // 2. Verificar duplicado en el mismo archivo
        if (in_array($row['cedula'], $this->cedulasVistas)) {
            $this->saltados++;
            return null;
        }
        $this->cedulasVistas[] = $row['cedula'];

        // 3. Verificar duplicado en base de datos
        $existe = Votante::where('cedula', $row['cedula'])->exists();
        if ($existe) {
            $this->saltados++;
            return null;
        }

        // 4. Crear el votante base
        $this->importados++;

        $votante = new Votante([
            'nombre'   => $row['nombre'],
            'cedula'   => $row['cedula'],
            'telefono' => $row['telefono'],
            'mesa'     => $row['mesa'],
            'lugar_votacion_id' => $row['lugar_votacion_id'],
        ]);

        // Asignar líder
        $votante->lider_id = $this->lider->id;

        // ---------------------------
        // Jerarquía de asignación
        // ---------------------------

        // Caso 1: El líder es de un concejal
        if ($this->lider->concejal_id) {
            $votante->concejal_id = $this->lider->concejal_id;

            // Solo asigna alcalde si el Excel trae 1 explícito
            if (isset($row['alcalde_id']) && intval($row['alcalde_id']) === 1) {
                $votante->alcalde_id = $this->lider->alcalde_id;
            }

        // Caso 2: El líder es de un alcalde
        } elseif ($this->lider->alcalde_id) {
            $votante->alcalde_id = $this->lider->alcalde_id;

            // Solo asigna concejal si el Excel trae un valor válido distinto de 0
            if (isset($row['concejal_id']) && intval($row['concejal_id']) !== 0) {
                $votante->concejal_id = $row['concejal_id'];
            }
        }

        return $votante;
    }
}
