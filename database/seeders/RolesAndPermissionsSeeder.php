<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'manage tenants',
            'view global metrics',
            'manage settings',
            'manage users',
            'view all expedientes',
            'manage billing',
            'manage own expedientes',
            'upload documents',
            'view own expedientes',
            'view agenda',
            'view terminos',
            'view all terminos',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([
            'manage users', 
            'view all expedientes', 
            'manage billing', 
            'upload documents', 
            'manage settings',
            'view agenda',
            'view terminos',
            'view all terminos',
        ]);

        $abogado = Role::firstOrCreate(['name' => 'abogado']);
        $abogado->syncPermissions([
            'manage own expedientes', 
            'upload documents',
            'view agenda',
            'view terminos',
        ]);

        $asistente = Role::firstOrCreate(['name' => 'asistente']);
        $asistente->syncPermissions(['upload documents', 'view agenda', 'view terminos']);

        $cliente = Role::firstOrCreate(['name' => 'cliente']);
        $cliente->syncPermissions(['view own expedientes']);

        $contable = Role::firstOrCreate(['name' => 'contable']);
        $contable->syncPermissions(['manage billing', 'view all expedientes', 'view all terminos']);

        // Ensure Super Admin user exists and has role
        $user = User::where('email', 'admin@legalcore.com')->first();
        if ($user) {
            $user->assignRole($superAdmin);
        }
    }
}
