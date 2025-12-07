<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'shipping_address',
        'phone',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the items for the order through order_items.
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'order_items')
                    ->withPivot('quantity', 'price', 'subtotal')
                    ->withTimestamps();
    }

    /**
     * Get the tracking history for the order.
     */
    public function tracking()
    {
        return $this->hasMany(OrderTracking::class)->orderBy('created_at', 'asc');
    }

    /**
     * Add tracking entry when status changes.
     */
    public function addTracking($status, $note = null)
    {
        return $this->tracking()->create([
            'status' => $status,
            'note' => $note,
            'created_at' => now()
        ]);
    }

    /**
     * Get the address relationship.
     */
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
}
