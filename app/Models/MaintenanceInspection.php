<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\InspectionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceInspection extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'maintenance_order_id',
        'category',
        'item_key',
        'item_label',
        'value',
        'status',
        'notes',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'status' => InspectionStatus::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (MaintenanceInspection $inspection) {
            if (empty($inspection->item_key)) {
                $inspection->item_key = str($inspection->item_label)->slug()->toString();
            }
        });
    }

    /**
     * @return BelongsTo<MaintenanceOrder, MaintenanceInspection>
     */
    public function maintenanceOrder(): BelongsTo
    {
        return $this->belongsTo(MaintenanceOrder::class);
    }
}
