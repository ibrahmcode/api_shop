<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Item;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get user's cart
     */
    public function index(Request $request)
    {
        $cartItems = Cart::with('item.category')
                        ->where('user_id', $request->user()->id)
                        ->get();

        $total = $cartItems->sum('subtotal');

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $cartItems,
                'total' => $total,
                'count' => $cartItems->count()
            ]
        ], 200);
    }

    /**
     * Add item to cart
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $item = Item::findOrFail($request->item_id);

        // Check if item already in cart
        $cartItem = Cart::where('user_id', $request->user()->id)
                       ->where('item_id', $item->id)
                       ->first();

        if ($cartItem) {
            // Update quantity
            $cartItem->update([
                'quantity' => $cartItem->quantity + $request->quantity
            ]);
        } else {
            // Create new cart item
            $cartItem = Cart::create([
                'user_id' => $request->user()->id,
                'item_id' => $item->id,
                'quantity' => $request->quantity,
                'price' => $item->price
            ]);
        }

        $cartItem->load('item');

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully',
            'data' => $cartItem
        ], 201);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, Cart $cart)
    {
        // Check if cart belongs to user
        if ($cart->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart->update([
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'data' => $cart
        ], 200);
    }

    /**
     * Remove item from cart
     */
    public function destroy(Request $request, Cart $cart)
    {
        // Check if cart belongs to user
        if ($cart->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart'
        ], 200);
    }

    /**
     * Clear all cart items
     */
    public function clear(Request $request)
    {
        Cart::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully'
        ], 200);
    }

    /**
     * Get cart count
     */
    public function count(Request $request)
    {
        $count = Cart::where('user_id', $request->user()->id)->count();

        return response()->json([
            'success' => true,
            'data' => ['count' => $count]
        ], 200);
    }
}
