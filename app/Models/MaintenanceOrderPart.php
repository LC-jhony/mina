<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceOrderPart extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'maintenance_order_id',
        'spare_part_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * @return BelongsTo<MaintenanceOrder, MaintenanceOrderPart>
     */
    public function maintenanceOrder(): BelongsTo
    {
        return $this->belongsTo(MaintenanceOrder::class);
    }

    /**
     * @return BelongsTo<SparePart, MaintenanceOrderPart>
     */
    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class);
    }
}
