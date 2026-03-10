<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\DirectoryProfile;

class SeedDirectoryProfiles extends Command
{
    protected $signature   = 'directory:seed-profiles
                              {--dry-run : Solo muestra cuántos usuarios necesitan perfil, sin crear nada}';

    protected $description = 'Crea DirectoryProfile vacío para todos los usuarios que no tienen uno todavía (útil para usuarios registrados antes de que existiera el directorio).';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $usersWithoutProfile = User::doesntHave('directoryProfile')->get();
        $count = $usersWithoutProfile->count();

        if ($count === 0) {
            $this->info('✅ Todos los usuarios ya tienen DirectoryProfile.');
            return self::SUCCESS;
        }

        $this->info("Usuarios sin DirectoryProfile: <fg=yellow>{$count}</>");

        if ($isDryRun) {
            $this->table(
                ['ID', 'Nombre', 'Email'],
                $usersWithoutProfile->map(fn($u) => [$u->id, $u->name, $u->email])->toArray()
            );
            $this->comment('No se creó nada (modo --dry-run).');
            return self::SUCCESS;
        }

        if (!$this->confirm("¿Crear {$count} perfiles vacíos ahora?", true)) {
            $this->warn('Operación cancelada.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $created = 0;
        foreach ($usersWithoutProfile as $user) {
            DirectoryProfile::firstOrCreate(['user_id' => $user->id]);
            $created++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ {$created} perfiles creados correctamente.");

        return self::SUCCESS;
    }
}
