<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'link_type',
        'link_id',
        'external_link',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'link_id' => 'integer'
    ];

    protected $appends = ['image_url', 'link'];

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    public function getLinkAttribute(): ?array
    {
        if ($this->link_type === 'none') {
            return null;
        }

        if ($this->link_type === 'external') {
            return [
                'type' => 'external',
                'url' => $this->external_link
            ];
        }

        return [
            'type' => $this->link_type,
            'id' => $this->link_id
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'link_id')->when($this->link_type === 'category');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'link_id')->when($this->link_type === 'item');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
