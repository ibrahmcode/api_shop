<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'name_en',
        'description',
        'description_ar',
        'description_en',
    ];

    // Get name based on locale
    public function getLocalizedName($locale = 'ku')
    {
        return match($locale) {
            'ar' => $this->name_ar ?? $this->name,
            'en' => $this->name_en ?? $this->name,
            default => $this->name,
        };
    }

    // Get description based on locale
    public function getLocalizedDescription($locale = 'ku')
    {
        return match($locale) {
            'ar' => $this->description_ar ?? $this->description,
            'en' => $this->description_en ?? $this->description,
            default => $this->description,
        };
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
