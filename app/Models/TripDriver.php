<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripDriver extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'trip_id',
        'driver_id',
        'leg',
        'position',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'position' => 'integer',
    ];

    /**
     * @return BelongsTo<Trip, TripDriver>
     */
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * @return BelongsTo<Driver, TripDriver>
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
