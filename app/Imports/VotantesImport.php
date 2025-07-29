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
    public $saltados = 0;  // Contador de duplicados
    public $importados = 0; // Contador de importados correctos

    public function __construct($lider)
    {
        $this->lider = $lider;
    }

    public function model(array $row)
    {
        // Buscar el lugar de votación por nombre
        $lugar = LugarVotacion::where('nombre', trim($row['lugar_votacion']))->first();
        if (!$lugar) {
            $this->saltados++;
            return null;
        }

        // Buscar concejal (usuario con rol 'aspirante-concejo') por nombre si está presente
        $concejal = null;
        if (!empty($row['concejal'])) {
            $concejal = User::where('name', trim($row['concejal']))
                ->role('aspirante-concejo') // Rol corregido
                ->first();

            if (!$concejal) {
                $this->saltados++;
                return null;
            }
        }

        // Verificar duplicado en el mismo archivo
        if (in_array($row['cedula'], $this->cedulasVistas)) {
            $this->saltados++;
            return null;
        }
        $this->cedulasVistas[] = $row['cedula'];

        // Verificar duplicado en base de datos
        if (Votante::where('cedula', $row['cedula'])->exists()) {
            $this->saltados++;
            return null;
        }

        // Crear el votante base
        $this->importados++;

        $votante = new Votante([
            'nombre'             => $row['nombre'],
            'cedula'             => $row['cedula'],
            'telefono'           => $row['telefono'],
            'mesa'               => $row['mesa'],
            'lugar_votacion_id'  => $lugar->id,
        ]);

        $votante->lider_id = $this->lider->id;

        // Jerarquía de asignación

        // Caso 1: El líder es de un concejal
        if ($this->lider->concejal_id) {
            $votante->concejal_id = $this->lider->concejal_id;

            if (isset($row['alcalde_id']) && intval($row['alcalde_id']) === 1) {
                $votante->alcalde_id = $this->lider->alcalde_id;
            }

        // Caso 2: El líder es de un alcalde
        } elseif ($this->lider->alcalde_id) {
            $votante->alcalde_id = $this->lider->alcalde_id;

            if ($concejal) {
                $votante->concejal_id = $concejal->id;
            }
        }

        return $votante;
    }
}
