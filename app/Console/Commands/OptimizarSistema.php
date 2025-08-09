<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class OptimizarSistema extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'sistema:optimizar {--force : Forzar optimización sin confirmación}';

    /**
     * The console command description.
     */
    protected $description = 'Optimiza el sistema limpiando caché, analizando tablas y realizando mantenimiento';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('¿Estás seguro de que quieres optimizar el sistema?')) {
            $this->info('Operación cancelada.');
            return 0;
        }

        $this->info('🚀 Iniciando optimización del sistema...');

        try {
            // 1. Limpiar caché
            $this->limpiarCache();

            // 2. Limpiar archivos temporales
            $this->limpiarArchivosTemporales();

            // 3. Analizar tablas de base de datos
            $this->analizarTablas();

            // 4. Limpiar sesiones expiradas
            $this->limpiarSesionesExpiradas();

            // 5. Optimizar consultas frecuentes
            $this->optimizarConsultas();

            $this->info('✅ Optimización completada exitosamente!');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error durante la optimización: ' . $e->getMessage());
            Log::error('Error en comando de optimización: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Limpiar caché del sistema
     */
    private function limpiarCache()
    {
        $this->info('🧹 Limpiando caché...');
        
        Cache::flush();
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        
        $this->info('   ✅ Caché limpiado');
    }

    /**
     * Limpiar archivos temporales
     */
    private function limpiarArchivosTemporales()
    {
        $this->info('🗂️  Limpiando archivos temporales...');
        
        $tempPath = storage_path('app/temp');
        if (is_dir($tempPath)) {
            $archivos = glob($tempPath . '/*');
            $contador = 0;
            
            foreach ($archivos as $archivo) {
                if (is_file($archivo) && (time() - filemtime($archivo)) > 86400) { // 24 horas
                    unlink($archivo);
                    $contador++;
                }
            }
            
            $this->info("   ✅ {$contador} archivos temporales eliminados");
        } else {
            $this->info('   ℹ️  No hay directorio de archivos temporales');
        }
    }

    /**
     * Analizar tablas de base de datos
     */
    private function analizarTablas()
    {
        $this->info('📊 Analizando tablas de base de datos...');
        
        $tablas = ['votantes', 'users', 'barrios', 'lugares_votacion', 'mesas'];
        
        foreach ($tablas as $tabla) {
            if (Schema::hasTable($tabla)) {
                try {
                    DB::statement("ANALYZE TABLE {$tabla}");
                    $this->info("   ✅ Tabla {$tabla} analizada");
                } catch (\Exception $e) {
                    $this->warn("   ⚠️  No se pudo analizar la tabla {$tabla}: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Limpiar sesiones expiradas
     */
    private function limpiarSesionesExpiradas()
    {
        $this->info('🔐 Limpiando sesiones expiradas...');
        
        try {
            if (Schema::hasTable('sessions')) {
                $sesionesExpiradas = DB::table('sessions')
                    ->where('last_activity', '<', now()->subMinutes(config('session.lifetime', 120)))
                    ->delete();
                
                $this->info("   ✅ {$sesionesExpiradas} sesiones expiradas eliminadas");
            } else {
                $this->info('   ℹ️  No hay tabla de sesiones');
            }
        } catch (\Exception $e) {
            $this->warn("   ⚠️  Error limpiando sesiones: " . $e->getMessage());
        }
    }

    /**
     * Optimizar consultas frecuentes
     */
    private function optimizarConsultas()
    {
        $this->info('⚡ Optimizando consultas frecuentes...');
        
        try {
            // Precalcular estadísticas globales y cachearlas
            $totalUsuarios = DB::table('users')->count();
            $totalVotantes = DB::table('votantes')->count();
            $totalBarrios = DB::table('barrios')->count();
            $totalLugares = DB::table('lugares_votacion')->count();
            
            Cache::put('stats_globales', [
                'total_usuarios' => $totalUsuarios,
                'total_votantes' => $totalVotantes,
                'total_barrios' => $totalBarrios,
                'total_lugares' => $totalLugares,
                'actualizado' => now()->toISOString()
            ], 3600); // 1 hora
            
            $this->info("   ✅ Estadísticas globales cacheadas");
            
        } catch (\Exception $e) {
            $this->warn("   ⚠️  Error optimizando consultas: " . $e->getMessage());
        }
    }
}
