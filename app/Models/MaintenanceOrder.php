<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\MaintenanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceOrder extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'vehicle_id',
        'trip_id',
        'mechanic_id',
        'maintenance_type_id',
        'start_date',
        'end_date',
        'mileage_at_service',
        'description',
        'status',
        'total_cost',
        'attachment_path',
        'attachment_name',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'mileage_at_service' => 'integer',
        'status' => MaintenanceStatus::class,
        'total_cost' => 'decimal:2',
    ];

    public function isOpen(): bool
    {
        return in_array($this->status, [
            MaintenanceStatus::PENDING,
            MaintenanceStatus::IN_PROGRESS,
        ]);
    }

    /**
     * @return BelongsTo<Vehicle, MaintenanceOrder>
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return BelongsTo<Trip, MaintenanceOrder>
     */
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * @return BelongsTo<Mechanic, MaintenanceOrder>
     */
    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(Mechanic::class);
    }

    /**
     * @return BelongsTo<MaintenanceType, MaintenanceOrder>
     */
    public function maintenanceType(): BelongsTo
    {
        return $this->belongsTo(MaintenanceType::class, 'maintenance_type_id');
    }

    /**
     * @return HasMany<MaintenanceOrderPart>
     */
    public function parts(): HasMany
    {
        return $this->hasMany(MaintenanceOrderPart::class);
    }

    /**
     * @return HasMany<MaintenanceInspection>
     */
    public function inspections(): HasMany
    {
        return $this->hasMany(MaintenanceInspection::class);
    }
}
