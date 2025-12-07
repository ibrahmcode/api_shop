<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'phone',
        'city',
        'area',
        'street_address',
        'additional_info',
        'postal_code',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Get formatted address
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->street_address,
            $this->area,
            $this->city,
            $this->postal_code
        ]);
        
        return implode(', ', $parts);
    }

    // Set as default and unset others
    public function setAsDefault()
    {
        // Unset all other default addresses for this user
        self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);
        
        $this->update(['is_default' => true]);
    }
}
