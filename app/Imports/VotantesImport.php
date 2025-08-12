<?php

namespace App\Imports;

use App\Models\Votante;
use App\Models\LugarVotacion;
use App\Models\User;
use App\Models\Barrio;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VotantesImport implements ToModel, WithHeadingRow
{
    private $lider;
    private $cedulasVistas = [];

    public $saltados = 0;  
    public $importados = 0;
    public $errores = [];
    public $importadosDetalle = []; // ✅ Lista de importados correctos

    public function __construct($lider)
    {
        $this->lider = $lider;
    }

    public function model(array $row)
    {
        $cedula = $row['cedula'] ?? 'desconocida';
        $nombre = $row['nombre'] ?? '';

        // =============================
        // Validar Lugar de Votación
        // =============================
        $lugarNombre = trim($row['lugar_votacion'] ?? '');
        $lugar = LugarVotacion::where('nombre', $lugarNombre)->first();

        if (!$lugar) {
            $this->registrarError($cedula, $nombre, "Lugar de votación '{$lugarNombre}' no encontrado.");
            return null;
        }

        // =============================
        // Validar Barrio
        // =============================
        $barrioNombre = trim($row['barrio'] ?? '');
        $barrio = null;

        if ($barrioNombre !== '') {
            $barrio = Barrio::where('nombre', $barrioNombre)
                ->where('alcalde_id', $this->lider->alcalde_id)
                ->first();

            if (!$barrio) {
                $this->registrarError($cedula, $nombre, "Barrio '{$barrioNombre}' no encontrado o no pertenece al alcalde asignado.");
                return null;
            }
        } else {
            $this->registrarError($cedula, $nombre, "El campo 'barrio' es obligatorio.");
            return null;
        }

        // =============================
        // Validar Concejal
        // =============================
        $concejalNombre = trim($row['concejal'] ?? '');
        $concejal = null;

        if ($concejalNombre !== '') {
            $concejal = User::where('name', $concejalNombre)
                ->role('aspirante-concejo')
                ->first();

            if (!$concejal) {
                $this->registrarError($cedula, $nombre, "Concejal '{$concejalNombre}' no existe o no tiene rol 'aspirante-concejo'.");
                return null;
            }
        }

        // =============================
        // Validar duplicados en archivo
        // =============================
        if (in_array($cedula, $this->cedulasVistas)) {
            $this->registrarError($cedula, $nombre, "Duplicada en el archivo de importación.");
            return null;
        }
        $this->cedulasVistas[] = $cedula;

        // =============================
        // Validar duplicados en la campaña (por alcalde)
        // =============================
        $alcaldeId = $this->lider->alcalde_id 
            ?? optional(User::find($this->lider->concejal_id))->alcalde_id;

        if ($alcaldeId && Votante::where('cedula', $cedula)
            ->where('alcalde_id', $alcaldeId)
            ->exists()) {
            $this->registrarError($cedula, $nombre, "Ya fue registrada en esta campaña.");
            return null;
        }
        // =============================
        // Crear Votante
        // =============================
        $this->importados++;
        $this->importadosDetalle[] = "{$cedula} - {$nombre}"; // ✅ Guardamos para mostrar

        $votante = new Votante([
            'nombre'            => $nombre,
            'cedula'            => $cedula,
            'telefono'          => $row['telefono'] ?? null,
            'mesa'              => $row['mesa'] ?? null,
            'lugar_votacion_id' => $lugar->id,
            'barrio_id'         => $barrio->id,
        ]);

        $votante->lider_id = $this->lider->id;

        // Asignar concejal/alcalde según jerarquía
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

    /**
     * Guarda un error en la lista y aumenta contador de saltados
     */
    private function registrarError($cedula, $nombre, $mensaje)
    {
        $this->saltados++;
        $this->errores[] = "{$cedula} - {$nombre}: {$mensaje}";
    }
}
