<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Votante;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class VerificarDuplicadosVotantes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'votantes:verificar-duplicados {--fix : Corregir duplicados automáticamente} {--alcalde-id= : Filtrar por alcalde específico}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica y reporta duplicados de votantes por campaña electoral';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando duplicados de votantes...');
        
        $alcaldeId = $this->option('alcalde-id');
        $fix = $this->option('fix');
        
        // Construir query base
        $query = Votante::select('cedula', 'alcalde_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('alcalde_id')
            ->groupBy('cedula', 'alcalde_id')
            ->having('total', '>', 1);
            
        if ($alcaldeId) {
            $query->where('alcalde_id', $alcaldeId);
        }
        
        $duplicados = $query->get();
        
        if ($duplicados->isEmpty()) {
            $this->info('✅ No se encontraron duplicados de votantes.');
            return 0;
        }
        
        $this->warn("⚠️  Se encontraron {$duplicados->count()} grupos de duplicados:");
        $this->newLine();
        
        $totalDuplicados = 0;
        $duplicadosCorregidos = 0;
        
        foreach ($duplicados as $duplicado) {
            $this->info("📋 Cédula: {$duplicado->cedula} | Alcalde ID: {$duplicado->alcalde_id} | Total: {$duplicado->total}");
            
            // Obtener detalles de los votantes duplicados
            $votantes = Votante::where('cedula', $duplicado->cedula)
                ->where('alcalde_id', $duplicado->alcalde_id)
                ->orderBy('created_at')
                ->get();
            
            $totalDuplicados += $duplicado->total;
            
            foreach ($votantes as $index => $votante) {
                $lider = User::find($votante->lider_id);
                $liderNombre = $lider ? $votante->lider_id . ' (' . $lider->name . ')' : $votante->lider_id;
                
                $status = $index === 0 ? '✅ ORIGINAL' : '❌ DUPLICADO';
                $this->line("   {$status} | ID: {$votante->id} | Líder: {$liderNombre} | Creado: {$votante->created_at}");
                
                // Si es modo fix y es duplicado, eliminar
                if ($fix && $index > 0) {
                    $votante->delete();
                    $duplicadosCorregidos++;
                    $this->line("   🗑️  Eliminado duplicado ID: {$votante->id}");
                }
            }
            
            $this->newLine();
        }
        
        $this->info("📊 Resumen:");
        $this->info("   - Grupos de duplicados: {$duplicados->count()}");
        $this->info("   - Total de registros duplicados: {$totalDuplicados}");
        
        if ($fix) {
            $this->info("   - Duplicados corregidos: {$duplicadosCorregidos}");
            $this->info("✅ Proceso de corrección completado.");
        } else {
            $this->warn("💡 Para corregir automáticamente, ejecuta: php artisan votantes:verificar-duplicados --fix");
        }
        
        return 0;
    }
}
