<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Get all favorite items for the authenticated user.
     */
    public function index()
    {
        $user = Auth::user();
        $favoriteItems = $user->favoriteItems()->with('category')->get();

        return response()->json([
            'success' => true,
            'data' => $favoriteItems
        ], 200);
    }

    /**
     * Add an item to favorites.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id'
        ]);

        $user = Auth::user();
        $itemId = $request->item_id;

        // Check if already favorited
        if ($user->favoriteItems()->where('item_id', $itemId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Item is already in your favorites'
            ], 400);
        }

        // Add to favorites
        $user->favoriteItems()->attach($itemId);

        $item = Item::with('category')->find($itemId);

        return response()->json([
            'success' => true,
            'message' => 'Item added to favorites successfully',
            'data' => $item
        ], 201);
    }

    /**
     * Remove an item from favorites.
     */
    public function destroy($itemId)
    {
        $user = Auth::user();

        // Check if the item exists in favorites
        if (!$user->favoriteItems()->where('item_id', $itemId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in your favorites'
            ], 404);
        }

        // Remove from favorites
        $user->favoriteItems()->detach($itemId);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from favorites successfully'
        ], 200);
    }

    /**
     * Check if an item is favorited by the authenticated user.
     */
    public function check($itemId)
    {
        $user = Auth::user();
        $isFavorited = $user->favoriteItems()->where('item_id', $itemId)->exists();

        return response()->json([
            'success' => true,
            'is_favorited' => $isFavorited
        ], 200);
    }

    /**
     * Toggle favorite status for an item.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id'
        ]);

        $user = Auth::user();
        $itemId = $request->item_id;

        $isFavorited = $user->favoriteItems()->where('item_id', $itemId)->exists();

        if ($isFavorited) {
            // Remove from favorites
            $user->favoriteItems()->detach($itemId);
            $message = 'Item removed from favorites';
            $action = 'removed';
        } else {
            // Add to favorites
            $user->favoriteItems()->attach($itemId);
            $message = 'Item added to favorites';
            $action = 'added';
        }

        $item = Item::with('category')->find($itemId);

        return response()->json([
            'success' => true,
            'message' => $message,
            'action' => $action,
            'is_favorited' => !$isFavorited,
            'data' => $item
        ], 200);
    }
}
