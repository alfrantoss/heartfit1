<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageType extends Model
{
    use HasFactory;

    protected $table = 'package_types';

    protected $fillable = ['packageType', 'is_personal'];

    protected $casts = [
        'is_personal' => 'boolean',
    ];

    // Inverse relasi
    public function mealPackages()
    {
        return $this->hasMany(MealPackages::class, 'package_type_id');
    }
}
