<?php

namespace Database\Seeders;

use App\Models\LicenseCategory;
use App\Models\MaintenanceType;
use App\Models\Mine;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialSetupSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin Users
        User::updateOrCreate(
            ['email' => 'admin@mvms.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'supervisor@mvms.com'],
            [
                'name' => 'Maintenance Supervisor',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Mines (Realistic)
        $mines = [
            ['name' => 'Antamina', 'region' => 'Ancash', 'company' => 'Cia. Minera Antamina', 'address' => 'Huari, Ancash', 'latitude' => -9.5222, 'longitude' => -77.0500],
            ['name' => 'Yanacocha', 'region' => 'Cajamarca', 'company' => 'Minera Yanacocha', 'address' => 'Cajamarca, Cajamarca', 'latitude' => -6.9833, 'longitude' => -78.5167],
            ['name' => 'Las Bambas', 'region' => 'Apurímac', 'company' => 'MMG Limited', 'address' => 'Challhuahuacho, Apurímac', 'latitude' => -14.1667, 'longitude' => -72.3333],
            ['name' => 'Cerro Verde', 'region' => 'Arequipa', 'company' => 'Freeport-McMoRan', 'address' => 'Uchumayo, Arequipa', 'latitude' => -16.5167, 'longitude' => -71.5833],
        ];

        foreach ($mines as $mine) {
            Mine::updateOrCreate(['name' => $mine['name']], $mine);
        }

        // 3. Specialties
        $specialties = [
            ['name' => 'Mecánica General', 'description' => 'Mantenimiento preventivo y correctivo básico'],
            ['name' => 'Electricidad Automotriz', 'description' => 'Sistemas eléctricos y electrónicos'],
            ['name' => 'Sistemas Hidráulicos', 'description' => 'Maquinaria pesada y sistemas de levante'],
            ['name' => 'Llantas y Frenos', 'description' => 'Especialista en sistemas de rodamiento y frenado'],
            ['name' => 'Motores Diésel', 'description' => 'Reparación y ajuste de motores de gran potencia'],
        ];

        foreach ($specialties as $specialty) {
            Specialty::updateOrCreate(['name' => $specialty['name']], $specialty);
        }

        // 4. Maintenance Types
        $maintenanceTypes = [
            ['name' => 'Preventivo 5K', 'interval_km' => 5000, 'interval_days' => 90, 'description' => 'Revisión básica cada 5,000 km'],
            ['name' => 'Preventivo 10K', 'interval_km' => 10000, 'interval_days' => 180, 'description' => 'Revisión intermedia cada 10,000 km'],
            ['name' => 'Preventivo 20K', 'interval_km' => 20000, 'interval_days' => 360, 'description' => 'Revisión mayor cada 20,000 km'],
            ['name' => 'Correctivo', 'interval_km' => 0, 'interval_days' => 0, 'description' => 'Reparación por falla no programada'],
            ['name' => 'Inspección de Seguridad', 'interval_km' => 0, 'interval_days' => 30, 'description' => 'Revisión técnica de seguridad'],
        ];

        foreach ($maintenanceTypes as $type) {
            MaintenanceType::updateOrCreate(['name' => $type['name']], $type);
        }

        // 5. License Categories (Perú Standard)
        $categories = [
            ['code' => 'A-I', 'description' => 'Vehículos particulares sedán, SUV, etc.', 'vehicle_type' => 'Car'],
            ['code' => 'A-IIa', 'description' => 'Taxi, ambulancia, transporte de pasajeros.', 'vehicle_type' => 'Car'],
            ['code' => 'A-IIb', 'description' => 'Camionetas, microbuses de hasta 6 toneladas.', 'vehicle_type' => 'Truck'],
            ['code' => 'A-IIIa', 'description' => 'Ómnibus de más de 6 toneladas.', 'vehicle_type' => 'Truck'],
            ['code' => 'A-IIIb', 'description' => 'Camiones de gran tonelaje, trailers.', 'vehicle_type' => 'Truck'],
            ['code' => 'A-IIIc', 'description' => 'Todo tipo de vehículos de las categorías anteriores.', 'vehicle_type' => 'Truck'],
        ];

        foreach ($categories as $category) {
            LicenseCategory::updateOrCreate(['code' => $category['code']], $category);
        }
    }
}
