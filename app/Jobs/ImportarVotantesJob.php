<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\VotantesImport;
use App\Models\User;
use App\Models\Votante;
use Illuminate\Support\Facades\DB;

class ImportarVotantesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800; // 30 minutos máximo
    public $tries = 3; // Reintentos en caso de fallo
    
    protected $filePath;
    protected $liderId;
    protected $userId;
    protected $notifyEmail;

    /**
     * Create a new job instance.
     */
    public function __construct($filePath, $liderId, $userId, $notifyEmail = null)
    {
        $this->filePath = $filePath;
        $this->liderId = $liderId;
        $this->userId = $userId;
        $this->notifyEmail = $notifyEmail;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            Log::info("Iniciando importación de votantes para líder ID: {$this->liderId}");
            
            // Verificar que el archivo existe
            if (!Storage::exists($this->filePath)) {
                throw new \Exception("Archivo no encontrado: {$this->filePath}");
            }

            // Obtener el líder
            $lider = User::find($this->liderId);
            if (!$lider) {
                throw new \Exception("Líder no encontrado");
            }

            // Contador de registros procesados
            $totalProcesados = 0;
            $totalExitosos = 0;
            $totalErrores = 0;
            $errores = [];

            // Procesar en lotes para evitar problemas de memoria
            Excel::import(new class($this->liderId, $totalProcesados, $totalExitosos, $totalErrores, $errores) {
                private $liderId;
                private $totalProcesados;
                private $totalExitosos;
                private $totalErrores;
                private $errores;

                public function __construct($liderId, &$totalProcesados, &$totalExitosos, &$totalErrores, &$errores)
                {
                    $this->liderId = $liderId;
                    $this->totalProcesados = &$totalProcesados;
                    $this->totalExitosos = &$totalExitosos;
                    $this->totalErrores = &$totalErrores;
                    $this->errores = &$errores;
                }

                public function model(array $row)
                {
                    $this->totalProcesados++;
                    
                    try {
                        // Validar datos básicos
                        if (empty($row['cedula']) || empty($row['nombre'])) {
                            $this->totalErrores++;
                            $this->errores[] = "Fila {$this->totalProcesados}: Cédula o nombre vacío";
                            return null;
                        }

                        // Verificar si ya existe
                        $existe = Votante::where('cedula', $row['cedula'])
                                        ->where('lider_id', $this->liderId)
                                        ->exists();
                        
                        if ($existe) {
                            $this->totalErrores++;
                            $this->errores[] = "Fila {$this->totalProcesados}: Cédula {$row['cedula']} ya existe";
                            return null;
                        }

                        // Crear votante
                        $votante = new Votante();
                        $votante->cedula = $row['cedula'];
                        $votante->nombre = $row['nombre'];
                        $votante->telefono = $row['telefono'] ?? '';
                        $votante->mesa = $row['mesa'] ?? '';
                        $votante->lider_id = $this->liderId;
                        $votante->barrio_id = $row['barrio_id'] ?? null;
                        $votante->lugar_votacion_id = $row['lugar_votacion_id'] ?? null;
                        $votante->concejal_id = $row['concejal_id'] ?? null;
                        $votante->alcalde_id = $row['alcalde_id'] ?? null;
                        $votante->tambien_vota_alcalde = $row['tambien_vota_alcalde'] ?? 0;
                        
                        $votante->save();
                        $this->totalExitosos++;

                    } catch (\Exception $e) {
                        $this->totalErrores++;
                        $this->errores[] = "Fila {$this->totalProcesados}: " . $e->getMessage();
                    }

                    return null; // No crear modelo automáticamente
                }
            }, $this->filePath);

            // Limpiar archivo temporal
            Storage::delete($this->filePath);

            // Guardar resultados en caché para consulta posterior
            $resultadoKey = "import_result_{$this->userId}";
            \Cache::put($resultadoKey, [
                'total_procesados' => $totalProcesados,
                'total_exitosos' => $totalExitosos,
                'total_errores' => $totalErrores,
                'errores' => $errores,
                'fecha' => now()->toISOString(),
                'estado' => 'completado'
            ], 3600); // 1 hora

            Log::info("Importación completada. Procesados: {$totalProcesados}, Exitosos: {$totalExitosos}, Errores: {$totalErrores}");

        } catch (\Exception $e) {
            Log::error("Error en importación: " . $e->getMessage());
            
            // Guardar error en caché
            $resultadoKey = "import_result_{$this->userId}";
            \Cache::put($resultadoKey, [
                'error' => $e->getMessage(),
                'fecha' => now()->toISOString(),
                'estado' => 'error'
            ], 3600);

            // Limpiar archivo en caso de error
            if (Storage::exists($this->filePath)) {
                Storage::delete($this->filePath);
            }

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error("Job de importación falló: " . $exception->getMessage());
        
        // Limpiar archivo
        if (Storage::exists($this->filePath)) {
            Storage::delete($this->filePath);
        }

        // Notificar al usuario del fallo
        $resultadoKey = "import_result_{$this->userId}";
        \Cache::put($resultadoKey, [
            'error' => 'La importación falló: ' . $exception->getMessage(),
            'fecha' => now()->toISOString(),
            'estado' => 'fallo'
        ], 3600);
    }
}
