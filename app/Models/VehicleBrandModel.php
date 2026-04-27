<?php

namespace App\Models;

use Database\Factories\VehicleBrandModelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleBrandModel extends Model
{
    /** @use HasFactory<VehicleBrandModelFactory> */
    use HasFactory;

    protected $fillable = [
        'brand',
        'model',
        'vehicle_type',
        'passenger_capacity',
    ];

    protected $casts = [
        'passenger_capacity' => 'integer',
    ];

    protected $appends = [
        'full_name',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->brand} {$this->model}";
    }
}
