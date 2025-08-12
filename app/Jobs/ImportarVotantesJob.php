<?php

namespace App\Jobs;

use App\Models\Votante;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ImportarVotantesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $userId;

    // Para almacenar resultados de la importación
    protected $successCount = 0;
    protected $errorCount = 0;
    protected $errorMessages = [];

    /**
     * Create a new job instance.
     */
    public function __construct($filePath, $userId)
    {
        $this->filePath = $filePath;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $path = storage_path('app/' . $this->filePath);

            // Leer Excel como colección
            $rows = Excel::toCollection(null, $path)[0];

            foreach ($rows as $index => $row) {
                if ($index === 0) {
                    // Saltar encabezado
                    continue;
                }

                // Normalizar datos
                $cedula = trim($row[0] ?? '');
                $nombre = trim($row[1] ?? '');
                $telefono = trim($row[2] ?? '');

                // Validaciones
                $validator = Validator::make([
                    'cedula' => $cedula,
                    'nombre' => $nombre,
                    'telefono' => $telefono
                ], [
                    'cedula' => 'required|numeric|unique:votantes,cedula',
                    'nombre' => 'required|string|max:255',
                    'telefono' => 'nullable|string|max:20',
                ]);

                if ($validator->fails()) {
                    $this->errorCount++;
                    $this->errorMessages[] = [
                        'fila' => $index + 1,
                        'errores' => $validator->errors()->all()
                    ];
                    continue;
                }

                // Insertar
                Votante::create([
                    'cedula' => $cedula,
                    'nombre' => $nombre,
                    'telefono' => $telefono,
                    'registrado_por' => $this->userId
                ]);

                $this->successCount++;
            }

            // Guardar resumen en logs o en base de datos (opcional)
            Log::info("Importación completada", [
                'correctos' => $this->successCount,
                'errores' => $this->errorCount,
                'detalles_errores' => $this->errorMessages
            ]);

            // Aquí podrías notificar al usuario por correo o guardar en tabla
            // Notification::send(User::find($this->userId), new ImportacionCompletada($this->successCount, $this->errorMessages));

        } catch (\Exception $e) {
            Log::error("Error al importar votantes: " . $e->getMessage());
        }
    }
}
