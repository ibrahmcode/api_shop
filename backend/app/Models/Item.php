<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'name_ar',
        'name_en',
        'description',
        'description_ar',
        'description_en',
        'price',
        'image',
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * The users who have favorited this item.
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'user_favorite_items')
                    ->withTimestamps();
    }

    /**
     * Get reviews for this item
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get average rating
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get total reviews count
     */
    public function getTotalReviewsAttribute()
    {
        return $this->reviews()->count();
    }
}
