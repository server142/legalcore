<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListPendingGoogleUsers extends Command
{
    protected $signature = 'google:list-pending-users';
    protected $description = 'Lista usuarios que necesitan ser agregados como Test Users en Google Cloud';

    public function handle()
    {
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('  USUARIOS PENDIENTES PARA GOOGLE CLOUD CONSOLE');
        $this->info('═══════════════════════════════════════════════════════');
        $this->newLine();

        // Usuarios que NO tienen Google conectado
        $pendingUsers = User::whereNull('google_access_token')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($pendingUsers->isEmpty()) {
            $this->warn('✓ Todos los usuarios ya tienen Google Calendar conectado.');
            return 0;
        }

        $this->info("Total de usuarios sin Google Calendar: {$pendingUsers->count()}");
        $this->newLine();

        // Mostrar tabla
        $this->table(
            ['#', 'Email', 'Nombre', 'Registrado hace'],
            $pendingUsers->map(function ($user, $index) {
                return [
                    $index + 1,
                    $user->email,
                    $user->name,
                    $user->created_at->diffForHumans(),
                ];
            })
        );

        $this->newLine();
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('  CORREOS PARA COPIAR Y PEGAR');
        $this->info('═══════════════════════════════════════════════════════');
        $this->newLine();

        foreach ($pendingUsers as $user) {
            $this->line($user->email);
        }

        $this->newLine();
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('  INSTRUCCIONES');
        $this->info('═══════════════════════════════════════════════════════');
        $this->newLine();
        $this->line('1. Copia los correos de arriba');
        $this->line('2. Ve a: https://console.cloud.google.com/apis/credentials/consent');
        $this->line('3. Selecciona tu proyecto: diogenes-485019');
        $this->line('4. Baja a la sección "Usuarios de prueba"');
        $this->line('5. Haz clic en "+ AGREGAR USUARIOS"');
        $this->line('6. Pega los correos (uno por línea)');
        $this->line('7. Haz clic en "Guardar"');
        $this->newLine();
        $this->info("Límite actual: 100 usuarios de prueba");
        $this->warn("Usuarios agregados hasta ahora: " . User::whereNotNull('google_access_token')->count());
        $this->warn("Espacios disponibles: " . (100 - User::count()));
        $this->newLine();

        return 0;
    }
}
