<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\AuditLog;
use App\Models\AiUsageLog;

class CleanOldLogs extends Command
{
    protected $signature = 'clean:logs {--dry-run : Mostrar qué se eliminará sin hacerlo} {--force : Forzar eliminación sin confirmación}';
    protected $description = 'Limpia logs antiguos para reducir tamaño de BD y mejorar performance';

    public function handle()
    {
        $this->info('🧹 Iniciando limpieza de logs antiguos...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        // 1. Audit Logs > 90 días
        $this->cleanAuditLogs($dryRun, $force);
        
        // 2. AI Usage Logs > 60 días
        $this->cleanAiLogs($dryRun, $force);
        
        // 3. Cache antiguo
        $this->cleanCache($dryRun);

        $this->newLine();
        $this->info('✅ Limpieza completada!');
    }

    protected function cleanAuditLogs($dryRun, $force)
    {
        $cutoffDate = now()->subDays(90);
        $count = AuditLog::where('created_at', '<', $cutoffDate)->count();

        if ($count === 0) {
            $this->line('  • Audit Logs: No hay registros antiguos para limpiar');
            return;
        }

        $this->warn("  • Audit Logs: {$count} registros > 90 días");

        if ($dryRun) {
            $this->line('    [DRY RUN] Se eliminarían pero no se hace nada');
            return;
        }

        if (!$force && !$this->confirm('    ¿Eliminar estos audit logs?', true)) {
            $this->line('    Cancelado por usuario');
            return;
        }

        // Opción 1: Eliminar directamente (más rápido)
        $deleted = AuditLog::where('created_at', '<', $cutoffDate)->delete();
        
        $this->info("    ✓ Eliminados {$deleted} audit logs");
        
        // Calcular espacio liberado aproximado
        $spaceSaved = $deleted * 500; // ~500 bytes por log
        $this->line("    💾 Espacio liberado: ~" . $this->formatBytes($spaceSaved));
    }

    protected function cleanAiLogs($dryRun, $force)
    {
        $cutoffDate = now()->subDays(60);
        $count = AiUsageLog::where('created_at', '<', $cutoffDate)->count();

        if ($count === 0) {
            $this->line('  • AI Usage Logs: No hay registros antiguos para limpiar');
            return;
        }

        $this->warn("  • AI Usage Logs: {$count} registros > 60 días");

        if ($dryRun) {
            $this->line('    [DRY RUN] Se eliminarían pero no se hace nada');
            return;
        }

        if (!$force && !$this->confirm('    ¿Eliminar estos AI logs?', true)) {
            $this->line('    Cancelado por usuario');
            return;
        }

        $deleted = AiUsageLog::where('created_at', '<', $cutoffDate)->delete();
        
        $this->info("    ✓ Eliminados {$deleted} AI usage logs");
    }

    protected function cleanCache($dryRun)
    {
        $this->line('  • Cache: Limpiando cache antiguo...');
        
        if ($dryRun) {
            $this->line('    [DRY RUN] Se limpiaría cache pero no se hace nada');
            return;
        }

        \Artisan::call('cache:clear');
        \Artisan::call('view:clear');
        \Artisan::call('route:clear');
        
        $this->info('    ✓ Cache limpiado');
    }

    protected function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
