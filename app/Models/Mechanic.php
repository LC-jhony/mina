<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\MaintenanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mechanic extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'specialty_id',
        'first_name',
        'last_name',
        'phone',
        'is_active',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function hasActiveOrder(): bool
    {
        return $this->maintenanceOrders()
            ->whereIn('status', [
                MaintenanceStatus::PENDING,
                MaintenanceStatus::IN_PROGRESS,
            ])
            ->exists();
    }

    /**
     * @return BelongsTo<Specialty, Mechanic>
     */
    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    /**
     * @return HasMany<MaintenanceOrder>
     */
    public function maintenanceOrders(): HasMany
    {
        return $this->hasMany(MaintenanceOrder::class);
    }
}
