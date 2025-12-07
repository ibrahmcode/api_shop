<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Validate coupon code
     */
    public function validate(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'order_amount' => 'required|numeric|min:0'
        ]);

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code'
            ], 404);
        }

        $validation = $coupon->isValid($request->order_amount);

        if (!$validation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $validation['message']
            ], 400);
        }

        $discountAmount = $coupon->calculateDiscount($request->order_amount);
        $finalAmount = $request->order_amount - $discountAmount;

        return response()->json([
            'success' => true,
            'message' => 'Coupon is valid',
            'data' => [
                'coupon' => $coupon,
                'order_amount' => $request->order_amount,
                'discount_amount' => round($discountAmount, 2),
                'final_amount' => round($finalAmount, 2)
            ]
        ], 200);
    }
}
