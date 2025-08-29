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
    protected $description = 'Verifica duplicados de votantes globales (cédula única en todo el sistema)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando duplicados de votantes...');
        
        $alcaldeId = $this->option('alcalde-id');
        
        // Construir query base - buscar duplicados globales por cédula
        $query = Votante::select('cedula', DB::raw('COUNT(*) as total'))
            ->groupBy('cedula')
            ->having('total', '>', 1);
            
        if ($alcaldeId) {
            // Si se especifica alcalde, filtrar solo duplicados de esa campaña
            $query = Votante::select('cedula', DB::raw('COUNT(*) as total'))
                ->where('alcalde_id', $alcaldeId)
                ->groupBy('cedula')
                ->having('total', '>', 1);
        }
        
        $duplicados = $query->get();
        
        if ($duplicados->isEmpty()) {
            $this->info('✅ No se encontraron duplicados de votantes.');
            return 0;
        }
        
        $this->warn("⚠️  Se encontraron {$duplicados->count()} grupos de duplicados:");
        $this->newLine();
        
        foreach ($duplicados as $duplicado) {
            $this->info("📋 Cédula: {$duplicado->cedula} | Total duplicados: {$duplicado->total}");
            
            // Obtener detalles de todos los votantes con esta cédula
            $votantes = Votante::where('cedula', $duplicado->cedula)
                ->orderBy('created_at')
                ->get();
            
            foreach ($votantes as $index => $votante) {
                $lider = User::find($votante->lider_id);
                $liderNombre = $lider ? $lider->name : 'Desconocido';
                
                $alcalde = User::find($votante->alcalde_id);
                $alcaldeNombre = $alcalde ? $alcalde->name : 'Desconocido';
                
                $concejalInfo = '';
                if ($votante->concejal_id) {
                    $concejal = User::find($votante->concejal_id);
                    $concejalNombre = $concejal ? $concejal->name : 'Desconocido';
                    $concejalInfo = " | Concejal: {$concejalNombre}";
                }
                
                $status = $index === 0 ? '✅ ORIGINAL' : '❌ DUPLICADO';
                $this->line("   {$status} | ID: {$votante->id} | Líder: {$liderNombre} | Alcalde: {$alcaldeNombre}{$concejalInfo} | Creado: {$votante->created_at}");
            }
            
            $this->newLine();
        }
        
        $this->info("📊 Total de grupos de duplicados: {$duplicados->count()}");
        
        return 0;
    }
}
