<?php

namespace App\Models;

use Database\Factories\LicenseCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseCategory extends Model
{
    /** @use HasFactory<LicenseCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'vehicle_type',
    ];

    public function licenses()
    {
        return $this->hasMany(
            related: License::class,
            foreignKey: 'category_id'
        );
    }
}
