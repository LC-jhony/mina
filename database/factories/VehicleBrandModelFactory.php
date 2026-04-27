<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleBrandModelFactory extends Factory
{
    private const BASE_MODELS = [
        ['brand' => 'Toyota', 'model' => 'Hilux', 'vehicle_type' => 'Truck', 'passenger_capacity' => 5],
        ['brand' => 'Toyota', 'model' => 'Corolla', 'vehicle_type' => 'Car', 'passenger_capacity' => 5],
        ['brand' => 'Toyota', 'model' => 'RAV4', 'vehicle_type' => 'Car', 'passenger_capacity' => 5],
        ['brand' => 'Toyota', 'model' => 'Land Cruiser', 'vehicle_type' => 'Truck', 'passenger_capacity' => 5],
        ['brand' => 'Ford', 'model' => 'Ranger', 'vehicle_type' => 'Truck', 'passenger_capacity' => 5],
        ['brand' => 'Ford', 'model' => 'Fiesta', 'vehicle_type' => 'Car', 'passenger_capacity' => 5],
        ['brand' => 'Ford', 'model' => 'Explorer', 'vehicle_type' => 'Car', 'passenger_capacity' => 7],
        ['brand' => 'Ford', 'model' => 'F-150', 'vehicle_type' => 'Truck', 'passenger_capacity' => 6],
        ['brand' => 'Chevrolet', 'model' => 'Silverado', 'vehicle_type' => 'Truck', 'passenger_capacity' => 5],
        ['brand' => 'Chevrolet', 'model' => 'Spark', 'vehicle_type' => 'Car', 'passenger_capacity' => 4],
        ['brand' => 'Chevrolet', 'model' => 'Equinox', 'vehicle_type' => 'Car', 'passenger_capacity' => 5],
        ['brand' => 'Chevrolet', 'model' => 'Tahoe', 'vehicle_type' => 'Truck', 'passenger_capacity' => 8],
        ['brand' => 'Honda', 'model' => 'Civic', 'vehicle_type' => 'Car', 'passenger_capacity' => 5],
        ['brand' => 'Honda', 'model' => 'CR-V', 'vehicle_type' => 'Car', 'passenger_capacity' => 5],
        ['brand' => 'Honda', 'model' => 'HR-V', 'vehicle_type' => 'Car', 'passenger_capacity' => 5],
        ['brand' => 'Honda', 'model' => 'Pilot', 'vehicle_type' => 'Truck', 'passenger_capacity' => 8],
        ['brand' => 'Nissan', 'model' => 'Frontier', 'vehicle_type' => 'Truck', 'passenger_capacity' => 5],
        ['brand' => 'Nissan', 'model' => 'Sentra', 'vehicle_type' => 'Car', 'passenger_capacity' => 5],
        ['brand' => 'Nissan', 'model' => 'Kicks', 'vehicle_type' => 'Car', 'passenger_capacity' => 5],
        ['brand' => 'Nissan', 'model' => 'Armada', 'vehicle_type' => 'Truck', 'passenger_capacity' => 8],
        ['brand' => 'Mitsubishi', 'model' => 'L200', 'vehicle_type' => 'Truck', 'passenger_capacity' => 5],
        ['brand' => 'Mitsubishi', 'model' => 'Outlander', 'vehicle_type' => 'Car', 'passenger_capacity' => 5],
        ['brand' => 'Mitsubishi', 'model' => 'Eclipse', 'vehicle_type' => 'Car', 'passenger_capacity' => 4],
        ['brand' => 'Mitsubishi', 'model' => 'Montero', 'vehicle_type' => 'Truck', 'passenger_capacity' => 7],
        ['brand' => 'Isuzu', 'model' => 'N-Series', 'vehicle_type' => 'Truck', 'passenger_capacity' => 3],
        ['brand' => 'Isuzu', 'model' => 'D-Max', 'vehicle_type' => 'Truck', 'passenger_capacity' => 5],
        ['brand' => 'Isuzu', 'model' => 'MUX', 'vehicle_type' => 'Truck', 'passenger_capacity' => 7],
        ['brand' => 'Mercedes', 'model' => 'Sprinter', 'vehicle_type' => 'Bus', 'passenger_capacity' => 12],
        ['brand' => 'Mercedes', 'model' => 'Vito', 'vehicle_type' => 'Bus', 'passenger_capacity' => 8],
        ['brand' => 'Mercedes', 'model' => 'GLE', 'vehicle_type' => 'Car', 'passenger_capacity' => 5],
        ['brand' => 'Volvo', 'model' => 'FH16', 'vehicle_type' => 'Truck', 'passenger_capacity' => 2],
        ['brand' => 'Volvo', 'model' => 'VNL', 'vehicle_type' => 'Truck', 'passenger_capacity' => 2],
        ['brand' => 'Volvo', 'model' => 'XC90', 'vehicle_type' => 'Car', 'passenger_capacity' => 7],
        ['brand' => 'Volvo', 'model' => 'XC60', 'vehicle_type' => 'Car', 'passenger_capacity' => 5],
        ['brand' => 'Hyundai', 'model' => 'Tucson', 'vehicle_type' => 'Car', 'passenger_capacity' => 5],
        ['brand' => 'Hyundai', 'model' => 'Santa Fe', 'vehicle_type' => 'Car', 'passenger_capacity' => 7],
        ['brand' => 'Hyundai', 'model' => 'Creta', 'vehicle_type' => 'Car', 'passenger_capacity' => 5],
        ['brand' => 'Kia', 'model' => 'Sportage', 'vehicle_type' => 'Car', 'passenger_capacity' => 5],
        ['brand' => 'Kia', 'model' => 'Sorento', 'vehicle_type' => 'Car', 'passenger_capacity' => 7],
        ['brand' => 'Kia', 'model' => 'Seltos', 'vehicle_type' => 'Car', 'passenger_capacity' => 5],
        ['brand' => 'Mazda', 'model' => 'CX-5', 'vehicle_type' => 'Car', 'passenger_capacity' => 5],
        ['brand' => 'Mazda', 'model' => 'CX-9', 'vehicle_type' => 'Car', 'passenger_capacity' => 7],
        ['brand' => 'Mazda', 'model' => 'Mazda3', 'vehicle_type' => 'Car', 'passenger_capacity' => 5],
        ['brand' => 'Subaru', 'model' => 'Forester', 'vehicle_type' => 'Car', 'passenger_capacity' => 5],
        ['brand' => 'Subaru', 'model' => 'Outback', 'vehicle_type' => 'Car', 'passenger_capacity' => 5],
        ['brand' => 'Suzuki', 'model' => 'Vitara', 'vehicle_type' => 'Car', 'passenger_capacity' => 5],
        ['brand' => 'Suzuki', 'model' => 'Jimny', 'vehicle_type' => 'Truck', 'passenger_capacity' => 4],
    ];

    private static int $index = 0;

    public function definition(): array
    {
        $baseIndex = self::$index % count(self::BASE_MODELS);
        $variant = intdiv(self::$index, count(self::BASE_MODELS));
        $vehicle = self::BASE_MODELS[$baseIndex];
        self::$index++;

        $modelName = $variant > 0 ? $vehicle['model'].' '.(2020 + $variant) : $vehicle['model'];

        return [
            'brand' => $vehicle['brand'],
            'model' => $modelName,
            'vehicle_type' => $vehicle['vehicle_type'],
            'passenger_capacity' => $vehicle['passenger_capacity'],
        ];
    }
}
