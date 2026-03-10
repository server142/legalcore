<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\LegalContentService;

class LegalDocumentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LegalContentService::createGlobalDefaults();

        // Seed tenant defaults for existing tenants
        \App\Models\Tenant::all()->each(function ($tenant) {
            LegalContentService::createTenantDefaults($tenant->id);
        });
    }
}
