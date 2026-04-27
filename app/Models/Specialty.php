<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialty extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * @return HasMany<Mechanic>
     */
    public function mechanics(): HasMany
    {
        return $this->hasMany(Mechanic::class);
    }
}
