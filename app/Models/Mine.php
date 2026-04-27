<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mine extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'region',
        'company',
        'address',
        'latitude',
        'longitude',
        'is_active',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
    ];

    /**
     * @return HasMany<Trip>
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }
}
