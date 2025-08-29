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
    protected $signature = 'votantes:verificar-duplicados {--alcalde-id= : Filtrar por alcalde específico}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica duplicados de votantes por campaña electoral';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando duplicados de votantes...');
        
        $alcaldeId = $this->option('alcalde-id');
        
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
        
        foreach ($duplicados as $duplicado) {
            $this->info("📋 Cédula: {$duplicado->cedula} | Alcalde ID: {$duplicado->alcalde_id} | Total: {$duplicado->total}");
            
            // Obtener detalles de los votantes duplicados
            $votantes = Votante::where('cedula', $duplicado->cedula)
                ->where('alcalde_id', $duplicado->alcalde_id)
                ->orderBy('created_at')
                ->get();
            
            foreach ($votantes as $index => $votante) {
                $lider = User::find($votante->lider_id);
                $liderNombre = $lider ? $lider->name : 'Desconocido';
                
                $status = $index === 0 ? '✅ ORIGINAL' : '❌ DUPLICADO';
                $this->line("   {$status} | ID: {$votante->id} | Líder: {$liderNombre} | Creado: {$votante->created_at}");
            }
            
            $this->newLine();
        }
        
        $this->info("📊 Total de grupos de duplicados: {$duplicados->count()}");
        
        return 0;
    }
}
