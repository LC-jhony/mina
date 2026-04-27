<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\VehicleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_model_id',
        'plate',
        'year',
        'mileage',
        'status',
    ];

    protected $casts = [
        'year' => 'integer',
        'mileage' => 'integer',
        'status' => VehicleStatus::class,
    ];

    public function brandModel(): BelongsTo
    {
        return $this->belongsTo(VehicleBrandModel::class, 'brand_model_id');
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function maintenanceOrders(): HasMany
    {
        return $this->hasMany(MaintenanceOrder::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === VehicleStatus::Available;
    }

    public function isOnTrip(): bool
    {
        return $this->status === VehicleStatus::OnTrip;
    }

    public function isInMaintenance(): bool
    {
        return $this->status === VehicleStatus::InMaintenance;
    }
}
