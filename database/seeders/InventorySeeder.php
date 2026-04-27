<?php

namespace Database\Seeders;

use App\Models\SparePart;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $parts = [
            ['name' => 'Filtro de Aceite (Toyota Hilux)', 'code' => 'FL-HIL-01', 'stock' => 50, 'unit_price' => 45.00],
            ['name' => 'Pastillas de Freno Delanteras (Hilux)', 'code' => 'BR-HIL-02', 'stock' => 20, 'unit_price' => 120.00],
            ['name' => 'Aceite de Motor 15W40 (Galón)', 'code' => 'OIL-15W40-G', 'stock' => 100, 'unit_price' => 85.00],
            ['name' => 'Filtro de Aire Motor (Universal Truck)', 'code' => 'AF-TRK-05', 'stock' => 15, 'unit_price' => 65.00],
            ['name' => 'Refrigerante Rojo (Galón)', 'code' => 'CL-RED-G', 'stock' => 40, 'unit_price' => 35.00],
            ['name' => 'Amortiguador Delantero (SUV)', 'code' => 'SH-SUV-10', 'stock' => 12, 'unit_price' => 250.00],
            ['name' => 'Kit de Distribución (Hilux 2.4)', 'code' => 'KT-HIL-24', 'stock' => 8, 'unit_price' => 450.00],
            ['name' => 'Batería 13 Placas 12V', 'code' => 'BT-12V-13P', 'stock' => 10, 'unit_price' => 320.00],
        ];

        foreach ($parts as $part) {
            SparePart::updateOrCreate(['code' => $part['code']], $part);
        }

        // Add some random parts to fill up
        SparePart::factory()->count(20)->create();
    }
}
