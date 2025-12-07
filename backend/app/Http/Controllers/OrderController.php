<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Get all orders for the authenticated user.
     */
    public function index()
    {
        $user = Auth::user();
        $orders = $user->orders()
                      ->with(['orderItems.item.category'])
                      ->orderBy('created_at', 'desc')
                      ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ], 200);
    }

    /**
     * Get a specific order by ID.
     */
    public function show($id)
    {
        $user = Auth::user();
        $order = $user->orders()
                     ->with(['orderItems.item.category'])
                     ->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ], 200);
    }

    /**
     * Create a new order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'nullable|string',
            'phone' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();

        try {
            DB::beginTransaction();

            // Calculate total amount
            $totalAmount = 0;
            $orderItemsData = [];

            foreach ($request->items as $itemData) {
                $item = Item::find($itemData['item_id']);
                
                if (!$item) {
                    throw new \Exception("Item with ID {$itemData['item_id']} not found");
                }

                $quantity = $itemData['quantity'];
                $price = $item->price;
                $subtotal = $price * $quantity;
                $totalAmount += $subtotal;

                $orderItemsData[] = [
                    'item_id' => $item->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'item' => $item
                ];
            }

            // Create the order
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'shipping_address' => $request->shipping_address,
                'phone' => $request->phone,
                'notes' => $request->notes,
            ]);

            // Create initial tracking entry
            $order->addTracking('pending', 'Order created');

            // Create order items
            foreach ($orderItemsData as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                    'subtotal' => $itemData['subtotal'],
                ]);
            }

            DB::commit();

            // Load relationships for response
            $order->load(['orderItems.item.category']);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $user = Auth::user();
        $order = $user->orders()->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $order->update([
            'status' => $request->status
        ]);

        // Add tracking entry
        $order->addTracking($request->status, $request->note ?? null);

        $order->load(['orderItems.item.category', 'tracking']);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => $order
        ], 200);
    }

    /**
     * Get tracking history for an order.
     */
    public function tracking($id)
    {
        $user = Auth::user();
        $order = $user->orders()->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $tracking = $order->tracking()->get()->map(function ($track) {
            return [
                'id' => $track->id,
                'status' => $track->status,
                'status_label' => OrderTracking::getStatusLabel($track->status, app()->getLocale()),
                'note' => $track->note,
                'created_at' => $track->created_at->format('Y-m-d H:i:s'),
                'created_at_human' => $track->created_at->diffForHumans()
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $order->id,
                'current_status' => $order->status,
                'tracking_history' => $tracking
            ]
        ], 200);
    }

    /**
     * Cancel an order (only if status is pending).
     */
    public function cancel($id)
    {
        $user = Auth::user();
        $order = $user->orders()->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending orders can be cancelled'
            ], 400);
        }

        $order->update([
            'status' => 'cancelled'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => $order
        ], 200);
    }

    /**
     * Delete an order (only if cancelled).
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $order = $user->orders()->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        if ($order->status !== 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Only cancelled orders can be deleted'
            ], 400);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully'
        ], 200);
    }
}
