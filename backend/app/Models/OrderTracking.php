<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTracking extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'status',
        'note',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Status labels in Kurdish
    public static function getStatusLabel($status, $locale = 'ku')
    {
        $labels = [
            'ku' => [
                'pending' => 'چاوەڕوانی',
                'confirmed' => 'پەسەندکراوە',
                'processing' => 'لە ئامادەکردندایە',
                'shipped' => 'نێردراوە',
                'delivered' => 'گەیشتووە',
                'cancelled' => 'هەڵوەشێندراوەتەوە'
            ],
            'ar' => [
                'pending' => 'قيد الانتظار',
                'confirmed' => 'مؤكد',
                'processing' => 'قيد المعالجة',
                'shipped' => 'تم الشحن',
                'delivered' => 'تم التسليم',
                'cancelled' => 'ملغى'
            ],
            'en' => [
                'pending' => 'Pending',
                'confirmed' => 'Confirmed',
                'processing' => 'Processing',
                'shipped' => 'Shipped',
                'delivered' => 'Delivered',
                'cancelled' => 'Cancelled'
            ]
        ];

        return $labels[$locale][$status] ?? $status;
    }
}
