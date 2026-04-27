<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\DriverFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    /** @use HasFactory<DriverFactory> */
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'first_name',
        'last_name',
        'dni',
        'phone',
        'birth_date',
        'is_active',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'full_name',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * @return HasMany<License>
     */
    public function licenses(): HasMany
    {
        return $this->hasMany(
            related: License::class,
            foreignKey: 'driver_id'
        );
    }

    /**
     * @return HasMany<TripDriver>
     */
    public function tripDrivers(): HasMany
    {
        return $this->hasMany(TripDriver::class);
    }

    /**
     * @return BelongsToMany<Trip>
     */
    public function trips(): BelongsToMany
    {
        return $this->belongsToMany(Trip::class, 'trip_drivers')
            ->withPivot('leg', 'position');
    }

    /**
     * Devuelve la licencia activa para un tipo de vehículo dado.
     */
    public function activeLicenseFor(string $vehicleType): ?License
    {
        return $this->licenses()
            ->whereHas('category', fn ($query) => $query->where('vehicle_type', $vehicleType))
            ->where('status', 'active')
            ->where('expiry_date', '>=', now())
            ->first();
    }

    /**
     * Scope para filtrar solo choferes activos.
     *
     * @param  Builder<Driver>  $query
     * @return Builder<Driver>
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
