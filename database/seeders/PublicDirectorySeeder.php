<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DirectoryProfile;

class PublicDirectorySeeder extends Seeder
{
    public function run()
    {
        // 1. Create a "Super Lawyer" profile for the Admin (You)
        $admin = User::first(); 
        if ($admin) {
            DirectoryProfile::updateOrCreate(
                ['user_id' => $admin->id],
                [
                    'headline' => 'Experto en Derecho Corporativo y Tech',
                    'bio' => "Abogado senior con más de 15 años de experiencia ayudando a Startups y Empresas SaaS a blindar sus operaciones legales. Director fundador de LegalCore.",
                    'specialties' => ['Corporativo', 'Propiedad Intelectual', 'Startups', 'Contratos'],
                    'city' => 'Ciudad de México',
                    'state' => 'Ciudad de México',
                    'professional_license' => 'CED-9876543',
                    'is_verified' => true,
                    'is_public' => true,
                    'whatsapp' => '5512345678',
                    'linkedin' => 'https://linkedin.com/in/admin',
                    'website' => 'https://legalcore.mx'
                ]
            );
        }

        // 2. Create 5 Dummy Lawyers
        $dummyData = [
            [
                'name' => 'Lic. Roberto M. Gtz',
                'headline' => 'Especialista en Divorcios Express',
                'city' => 'Monterrey',
                'state' => 'Nuevo León',
                'specialties' => ['Familiar', 'Divorcios', 'Pensiones']
            ],
            [
                'name' => 'Dra. Elena Vazquez',
                'headline' => 'Defensa Penal y Amparos',
                'city' => 'Guadalajara',
                'state' => 'Jalisco',
                'specialties' => ['Penal', 'Amparo', 'Derechos Humanos']
            ],
            [
                'name' => 'Lic. Carlos Slim (Homónimo)',
                'headline' => 'Litigio Mercantil y Cobranza',
                'city' => 'Mérida',
                'state' => 'Yucatán',
                'specialties' => ['Mercantil', 'Cobranza', 'Bancario']
            ],
             [
                'name' => 'Mtra. Sofía Altamirano',
                'headline' => 'Abogada Laboralista - Despidos',
                'city' => 'Xalapa',
                'state' => 'Veracruz',
                'specialties' => ['Laboral', 'Seguridad Social']
            ],
             [
                'name' => 'Lic. Fernando T. R.',
                'headline' => 'Asesoría Inmobiliaria y Notarial',
                'city' => 'Querétaro',
                'state' => 'Querétaro',
                'specialties' => ['Inmobiliario', 'Notarial', 'Tierras']
            ],
        ];

        foreach ($dummyData as $data) {
            // Create a fake user first
            $user = User::factory()->create([
                'name' => $data['name'],
                'email' => strtolower(str_replace(' ', '.', $data['name'])) . '@example.com',
                'password' => bcrypt('password'),
            ]);

            DirectoryProfile::create([
                'user_id' => $user->id,
                'headline' => $data['headline'],
                'bio' => "Abogado comprometido con la excelencia y la ética profesional en {$data['city']}. Ofrezco asesoría personalizada y resultados tangibles para mis clientes.",
                'specialties' => $data['specialties'],
                'city' => $data['city'],
                'state' => $data['state'],
                'professional_license' => 'CED-' . rand(100000, 999999),
                'is_verified' => true,
                'is_public' => true,
                'whatsapp' => '55' . rand(10000000, 99999999),
            ]);
        }
    }
}
