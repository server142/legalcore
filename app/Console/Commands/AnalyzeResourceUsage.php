<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Tenant;
use App\Models\Documento;
use App\Models\AiUsageLog;

class AnalyzeResourceUsage extends Command
{
    protected $signature = 'analyze:resources {--export}';
    protected $description = 'Analiza el uso de recursos del sistema para optimizar costos';

    public function handle()
    {
        $this->info('🔍 Analizando uso de recursos de LegalCore...');
        $this->newLine();

        // 1. Análisis de Base de Datos
        $this->analyzeDatabase();
        
        // 2. Análisis de Almacenamiento
        $this->analyzeStorage();
        
        // 3. Análisis de Uso de IA
        $this->analyzeAIUsage();
        
        // 4. Análisis de Tenants
        $this->analyzeTenants();
        
        // 5. Recomendaciones
        $this->showRecommendations();

        if ($this->option('export')) {
            $this->exportReport();
        }
    }

    protected function analyzeDatabase()
    {
        $this->info('📊 ANÁLISIS DE BASE DE DATOS');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        // Define models to check instead of executing count immediately
        $models = [
            'expedientes' => \App\Models\Expediente::class,
            'documentos' => \App\Models\Documento::class,
            'actuaciones' => \App\Models\Actuacion::class,
            'eventos' => \App\Models\Evento::class,
            'ai_chat_messages' => \App\Models\AiChatMessage::class,
            'ai_usage_logs' => \App\Models\AiUsageLog::class,
            'audit_logs' => \App\Models\AuditLog::class,
            'users' => \App\Models\User::class,
            'tenants' => \App\Models\Tenant::class,
        ];


        foreach ($models as $name => $modelClass) {
            try {
                $count = $modelClass::count();
                $this->line("  • {$name}: " . number_format($count) . " registros");
            } catch (\Exception $e) {
                $this->warn("  • {$name}: Tabla no encontrada o error (0 asumido)");
            }
        }

        // Detectar tablas grandes que necesitan limpieza
        $this->newLine();
        $this->warn('⚠️  Tablas que requieren limpieza:');
        
        try {
            $oldLogs = DB::table('audit_logs')
                ->where('created_at', '<', now()->subMonths(3))
                ->count();
            
            if ($oldLogs > 1000) {
                $this->line("  • audit_logs: {$oldLogs} registros > 3 meses (considerar archivar)");
            }
        } catch (\Exception $e) {}

        try {
            $oldAiLogs = DB::table('ai_usage_logs')
                ->where('created_at', '<', now()->subMonths(1))
                ->count();
            
            if ($oldAiLogs > 500) {
                $this->line("  • ai_usage_logs: {$oldAiLogs} registros > 1 mes (considerar archivar)");
            }
        } catch (\Exception $e) {}

        $this->newLine();
    }

    protected function analyzeStorage()
    {
        $this->info('💾 ANÁLISIS DE ALMACENAMIENTO');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $storagePath = storage_path('app');
        $totalSize = 0;
        $fileCount = 0;

        if (is_dir($storagePath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($storagePath)
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $totalSize += $file->getSize();
                    $fileCount++;
                }
            }
        }

        $this->line("  • Total de archivos: " . number_format($fileCount));
        $this->line("  • Espacio usado: " . $this->formatBytes($totalSize));
        
        // Análisis por tenant
        $documentos = Documento::selectRaw('tenant_id, COUNT(*) as count, SUM(file_size) as total_size')
            ->groupBy('tenant_id')
            ->orderByDesc('total_size')
            ->get();

        $this->newLine();
        $this->line('  Top 5 Tenants por almacenamiento:');
        
        foreach ($documentos->take(5) as $doc) {
            $tenant = Tenant::find($doc->tenant_id);
            $tenantName = $tenant ? $tenant->name : "Tenant #{$doc->tenant_id}";
            $this->line("    • {$tenantName}: " . $this->formatBytes($doc->total_size ?? 0) . " ({$doc->count} archivos)");
        }

        $this->newLine();
    }

    protected function analyzeAIUsage()
    {
        $this->info('🤖 ANÁLISIS DE USO DE IA');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Últimos 30 días
        $aiUsage = AiUsageLog::where('created_at', '>=', now()->subDays(30))
            ->selectRaw('
                COUNT(*) as total_requests,
                SUM(input_tokens + output_tokens) as total_tokens,
                SUM(cost) as total_cost,
                provider,
                model
            ')
            ->groupBy('provider', 'model')
            ->get();

        if ($aiUsage->isEmpty()) {
            $this->line('  • No hay registros de uso de IA en los últimos 30 días');
        } else {
            foreach ($aiUsage as $usage) {
                $this->line("  • {$usage->provider} ({$usage->model}):");
                $this->line("    - Requests: " . number_format($usage->total_requests));
                $this->line("    - Tokens: " . number_format($usage->total_tokens));
                $this->line("    - Costo estimado: $" . number_format($usage->total_cost, 2));
            }

            $totalCost = $aiUsage->sum('total_cost');
            $this->newLine();
            $this->warn("  💰 COSTO TOTAL IA (30 días): $" . number_format($totalCost, 2) . " USD");
        }

        $this->newLine();
    }

    protected function analyzeTenants()
    {
        $this->info('👥 ANÁLISIS DE TENANTS');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $tenants = Tenant::with(['users'])->get();

        $active = $tenants->where('is_active', true)->count();
        $trial = $tenants->where('subscription_status', 'trial')->count();
        $paid = $tenants->where('subscription_status', 'active')->count();
        $expired = $tenants->where('subscription_status', 'expired')->count();

        $this->line("  • Total de tenants: {$tenants->count()}");
        $this->line("  • Activos: {$active}");
        $this->line("  • En trial: {$trial}");
        $this->line("  • Pagados: {$paid}");
        $this->line("  • Expirados: {$expired}");

        $this->newLine();
        $this->line('  Tenants con más actividad:');
        
        foreach ($tenants->take(5) as $tenant) {
            $expedientes = \App\Models\Expediente::where('tenant_id', $tenant->id)->count();
            $documentos = \App\Models\Documento::where('tenant_id', $tenant->id)->count();
            $this->line("    • {$tenant->name}: {$expedientes} expedientes, {$documentos} documentos");
        }

        $this->newLine();
    }

    protected function showRecommendations()
    {
        $this->info('💡 RECOMENDACIONES PARA REDUCIR COSTOS');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $recommendations = [];

        // Check old logs
        $oldLogs = DB::table('audit_logs')->where('created_at', '<', now()->subMonths(3))->count();
        if ($oldLogs > 1000) {
            $recommendations[] = "🗑️  Archivar {$oldLogs} audit logs antiguos (ahorro: ~" . $this->formatBytes($oldLogs * 500) . ")";
        }

        // Check AI usage
        $aiCost = AiUsageLog::where('created_at', '>=', now()->subDays(30))
            ->sum('cost');
        
        if ($aiCost > 20) {
            $recommendations[] = "🤖 Uso de IA alto ($" . number_format($aiCost, 2) . "/mes). Considera:";
            $recommendations[] = "   - Usar modelos más baratos (gpt-4o-mini en vez de gpt-4)";
            $recommendations[] = "   - Implementar caché de respuestas frecuentes";
            $recommendations[] = "   - Limitar requests por tenant";
        }

        // Check storage
        $totalDocs = Documento::count();
        if ($totalDocs > 1000) {
            $recommendations[] = "💾 Migrar archivos antiguos a S3 Glacier (más barato que storage local)";
        }

        // Check inactive tenants
        $inactiveTenants = Tenant::where('is_active', false)->count();
        if ($inactiveTenants > 0) {
            $recommendations[] = "👥 Eliminar datos de {$inactiveTenants} tenants inactivos";
        }

        if (empty($recommendations)) {
            $this->line('  ✅ ¡Todo optimizado! No hay recomendaciones urgentes.');
        } else {
            foreach ($recommendations as $rec) {
                $this->line("  {$rec}");
            }
        }

        $this->newLine();
    }

    protected function exportReport()
    {
        $report = [
            'generated_at' => now()->toDateTimeString(),
            'database' => [
                'expedientes' => \App\Models\Expediente::count(),
                'documentos' => \App\Models\Documento::count(),
                'users' => \App\Models\User::count(),
            ],
            'ai_usage_30d' => AiUsageLog::where('created_at', '>=', now()->subDays(30))
                ->sum('estimated_cost'),
            'tenants' => [
                'total' => Tenant::count(),
                'active' => Tenant::where('is_active', true)->count(),
                'trial' => Tenant::where('subscription_status', 'trial')->count(),
            ],
        ];

        $filename = 'resource_analysis_' . now()->format('Y-m-d_His') . '.json';
        Storage::put($filename, json_encode($report, JSON_PRETTY_PRINT));
        
        $this->info("📄 Reporte exportado: storage/app/{$filename}");
    }

    protected function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
