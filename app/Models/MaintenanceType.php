<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceType extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'description',
        'interval_km',
        'interval_days',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'interval_km' => 'integer',
        'interval_days' => 'integer',
    ];

    /**
     * @return HasMany<MaintenanceOrder>
     */
    public function maintenanceOrders(): HasMany
    {
        return $this->hasMany(MaintenanceOrder::class);
    }
}
