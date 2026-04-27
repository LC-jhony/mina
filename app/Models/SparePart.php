<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SparePart extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'code',
        'name',
        'description',
        'unit',
        'unit_price',
        'stock',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'unit_price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function scopeLowStock($query, int $threshold = 5)
    {
        return $query->where('stock', '<=', $threshold);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock <= 5; // Default threshold
    }

    /**
     * @return HasMany<MaintenanceOrderPart>
     */
    public function maintenanceOrderParts(): HasMany
    {
        return $this->hasMany(MaintenanceOrderPart::class);
    }
}
