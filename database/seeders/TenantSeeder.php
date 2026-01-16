<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Expediente;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::create([
            'name' => 'Despacho Jurídico Méndez',
            'slug' => 'mendez-legal',
            'status' => 'active',
            'plan' => 'trial',
            'trial_ends_at' => now()->addDays(30),
            'is_active' => true,
        ]);

        // Put tenant_id in session for the trait to work during seeding
        session(['tenant_id' => $tenant->id]);

        $admin = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin Méndez',
            'email' => 'admin@mendez.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $admin->assignRole('admin');

        $abogado = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Lic. Juan Pérez',
            'email' => 'juan@mendez.com',
            'password' => bcrypt('password'),
            'role' => 'abogado',
        ]);
        $abogado->assignRole('abogado');

        $cliente = Cliente::create([
            'tenant_id' => $tenant->id,
            'nombre' => 'Empresa Patito S.A. de C.V.',
            'tipo' => 'persona_moral',
            'rfc' => 'EPA123456ABC',
            'email' => 'contacto@patito.com',
        ]);

        Expediente::create([
            'tenant_id' => $tenant->id,
            'numero' => '123/2024',
            'titulo' => 'Patito vs SAT',
            'materia' => 'Fiscal',
            'juzgado' => 'Juzgado Primero de Distrito',
            'cliente_id' => $cliente->id,
            'abogado_responsable_id' => $abogado->id,
            'estado_procesal' => 'En proceso',
            'fecha_inicio' => now(),
        ]);

        $userCliente = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Juan Patito',
            'email' => 'contacto@patito.com',
            'password' => bcrypt('password'),
            'role' => 'cliente',
        ]);
        $userCliente->assignRole('cliente');
    }
}
