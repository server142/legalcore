<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DatabaseBackupController extends Controller
{
    public function download()
    {
        // 1. Verify Super Admin role just in case middleware fails (Double Check)
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Acceso denegado.');
        }

        // 2. Prepare file info
        $filename = 'backup-diogenes-' . Carbon::now()->format('Y-m-d-H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);
        
        // Ensure directory exists
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        // 3. Get DB credentials
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port');

        // 4. Construct Command
        // We use shell redirection to avoid loading the whole dump into PHP memory
        // Password shielding is tricky in raw shell, we use minimal environment variables approach if possible
        // 4. Construct mysqldump Command
        $dumpFile = $path;
        $zipFile = str_replace('.sql', '.zip', $path);
        
        // Adding --column-statistics=0 to prevent hangs on some mysql client versions
        $cmd = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s --single-transaction --quick --no-tablespaces --column-statistics=0 %s > %s',
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbName),
            escapeshellarg($dumpFile)
        );

        try {
            // STEP 1: Generate SQL Dump
            $process = Process::fromShellCommandline($cmd);
            $process->setTimeout(1200); // 20 minutes max
            $process->run();

            if (!$process->isSuccessful()) {
                Log::error('Backup process failed: ' . $process->getErrorOutput());
                throw new ProcessFailedException($process);
            }

            if (!file_exists($dumpFile) || filesize($dumpFile) === 0) {
                throw new \Exception('El archivo de respaldo SQL está vacío o no se creó.');
            }

            // STEP 2: Compress and Encrypt with ZIP
            // We use the same DB password for the ZIP file for simplicity and security consistency
            $zipCmd = sprintf(
                'zip -j -P %s %s %s',
                escapeshellarg($dbPass),
                escapeshellarg($zipFile),
                escapeshellarg($dumpFile)
            );

            $zipProcess = Process::fromShellCommandline($zipCmd);
            $zipProcess->setTimeout(600);
            $zipProcess->run();

            // Verify ZIP creation
            if (!$zipProcess->isSuccessful() || !file_exists($zipFile)) {
                // Determine if 'zip' command is missing
                $error = $zipProcess->getErrorOutput();
                Log::warning("ZIP compression failed (sending SQL instead): " . $error);
                
                // Fallback: Send SQL file if ZIP fails (e.g., zip not installed)
                Log::info("Backup generado (SQL sin comprimir) por Super Admin: " . auth()->user()->email);
                return response()->download($dumpFile)->deleteFileAfterSend(true);
            }

            // ZIP Successful
            Log::info("Backup generado (ZIP cifrado) por Super Admin: " . auth()->user()->email);
            
            // Cleanup SQL file immediately, download ZIP and delete after send
            unlink($dumpFile);

            return response()->download($zipFile)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            // Cleanup in case of error
            if (file_exists($dumpFile)) unlink($dumpFile);
            if (isset($zipFile) && file_exists($zipFile)) unlink($zipFile);

            Log::error('Database Backup Failed: ' . $e->getMessage());
            return back()->with('error', 'Error generando el respaldo: ' . $e->getMessage());
        }
    }
}
