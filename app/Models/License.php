<?php

namespace App\Models;

use Database\Factories\LicenseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class License extends Model
{
    /** @use HasFactory<LicenseFactory> */
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'category_id',
        'license_number',
        'issue_date',
        'expiry_date',
        'status',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(
            related: Driver::class,
            foreignKey: 'driver_id'
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            related: LicenseCategory::class,
            foreignKey: 'category_id'
        );
    }
}
