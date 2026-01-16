<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Materia;
use App\Models\Tenant;

class MateriaSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        $materias = [
            'Civil',
            'Penal',
            'Familiar',
            'Mercantil',
            'Laboral',
            'Administrativo',
            'Fiscal',
            'Amparo',
            'Constitucional',
            'Agrario',
        ];

        foreach ($tenants as $tenant) {
            foreach ($materias as $materia) {
                Materia::create([
                    'tenant_id' => $tenant->id,
                    'nombre' => $materia,
                ]);
            }
        }
    }
}
