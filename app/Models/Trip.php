<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\TripStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'vehicle_id',
        'mine_id',
        'departure_date',
        'return_date',
        'origin',
        'cluster',
        'observations',
        'status',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'departure_date' => 'datetime',
        'return_date' => 'datetime',
        'status' => TripStatus::class,
    ];

    /**
     * @return BelongsTo<Vehicle, Trip>
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return BelongsTo<Mine, Trip>
     */
    public function mine(): BelongsTo
    {
        return $this->belongsTo(Mine::class);
    }

    /**
     * @return HasMany<TripDriver>
     */
    public function tripDrivers(): HasMany
    {
        return $this->hasMany(TripDriver::class);
    }

    /**
     * @return BelongsToMany<Driver>
     */
    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(Driver::class, 'trip_drivers')
            ->withPivot('leg', 'position');
    }

    /**
     * @return HasMany<MaintenanceOrder>
     */
    public function maintenanceOrders(): HasMany
    {
        return $this->hasMany(MaintenanceOrder::class);
    }

    /**
     * @return HasMany<TripDriver>
     */
    public function outboundDrivers(): HasMany
    {
        return $this->hasMany(TripDriver::class)->where('leg', 'outbound')->orderBy('position');
    }

    /**
     * @return HasMany<TripDriver>
     */
    public function returnDrivers(): HasMany
    {
        return $this->hasMany(TripDriver::class)->where('leg', 'return')->orderBy('position');
    }

    public function isEditable(): bool
    {
        return $this->status === TripStatus::SCHEDULED;
    }

    public function canStart(): bool
    {
        return $this->status === TripStatus::SCHEDULED && $this->vehicle->isAvailable();
    }
}
