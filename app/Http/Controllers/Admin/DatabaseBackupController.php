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
        // but for compatibility we will build a command string carefully.
        
        $dumpFile = $path;
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

        // Security Note: The password is visible in process list for the duration of the dump.
        // In a shared hosting environment this is bad. On a private VPS it is acceptable risk for this feature.

        try {
            $process = Process::fromShellCommandline($cmd);
            $process->setTimeout(1200); // 20 minutes max
            $process->run();

            if (!$process->isSuccessful()) {
                Log::error('Backup process failed: ' . $process->getErrorOutput());
                throw new ProcessFailedException($process);
            }

            if (!file_exists($dumpFile) || filesize($dumpFile) === 0) {
                throw new \Exception('El archivo de respaldo está vacío o no se creó.');
            }

            Log::info("Backup generado por Super Admin: " . auth()->user()->email);

            return response()->download($dumpFile)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Database Backup Failed: ' . $e->getMessage());
            return back()->with('error', 'Error generando el respaldo: ' . $e->getMessage());
        }
    }
}
