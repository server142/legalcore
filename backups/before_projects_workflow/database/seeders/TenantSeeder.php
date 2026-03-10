<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Expediente;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'mendez-legal'],
            [
                'name' => 'Despacho Jurídico Méndez',
                'status' => 'active',
                'plan' => 'trial',
                'trial_ends_at' => now()->addDays(30),
                'is_active' => true,
            ]
        );

        // Put tenant_id in session for the trait to work during seeding
        session(['tenant_id' => $tenant->id]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@mendez.com'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Admin Méndez',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );
        $admin->assignRole('admin');

        $abogado = User::firstOrCreate(
            ['email' => 'juan@mendez.com'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Lic. Juan Pérez',
                'password' => bcrypt('password'),
                'role' => 'abogado',
            ]
        );
        $abogado->assignRole('abogado');

        $cliente = Cliente::firstOrCreate(
            ['email' => 'contacto@patito.com'],
            [
                'tenant_id' => $tenant->id,
                'nombre' => 'Empresa Patito S.A. de C.V.',
                'tipo' => 'persona_moral',
                'rfc' => 'EPA123456ABC',
            ]
        );

        Expediente::firstOrCreate(
            ['numero' => '123/2024'],
            [
                'tenant_id' => $tenant->id,
                'titulo' => 'Patito vs SAT',
                'materia' => 'Fiscal',
                'juzgado' => 'Juzgado Primero de Distrito',
                'cliente_id' => $cliente->id,
                'abogado_responsable_id' => $abogado->id,
                'estado_procesal' => 'En proceso',
                'fecha_inicio' => now(),
            ]
        );

        $userCliente = User::firstOrCreate(
            ['email' => 'contacto@patito.com'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Juan Patito',
                'password' => bcrypt('password'),
                'role' => 'cliente',
            ]
        );
        $userCliente->assignRole('cliente');
    }
}
