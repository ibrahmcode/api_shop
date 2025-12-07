<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Get reviews for an item
     */
    public function index(Item $item)
    {
        $reviews = Review::with('user:id,name,avatar')
                        ->where('item_id', $item->id)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        $stats = [
            'average_rating' => round($item->reviews()->avg('rating'), 1),
            'total_reviews' => $item->reviews()->count(),
            'rating_distribution' => [
                '5' => $item->reviews()->where('rating', 5)->count(),
                '4' => $item->reviews()->where('rating', 4)->count(),
                '3' => $item->reviews()->where('rating', 3)->count(),
                '2' => $item->reviews()->where('rating', 2)->count(),
                '1' => $item->reviews()->where('rating', 1)->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'reviews' => $reviews,
                'stats' => $stats
            ]
        ], 200);
    }

    /**
     * Create a review
     */
    public function store(Request $request, Item $item)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        // Check if user already reviewed this item
        $existingReview = Review::where('user_id', $request->user()->id)
                                ->where('item_id', $item->id)
                                ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this item'
            ], 400);
        }

        $review = Review::create([
            'user_id' => $request->user()->id,
            'item_id' => $item->id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        $review->load('user:id,name,avatar');

        return response()->json([
            'success' => true,
            'message' => 'Review added successfully',
            'data' => $review
        ], 201);
    }

    /**
     * Update a review
     */
    public function update(Request $request, Review $review)
    {
        // Check if review belongs to user
        if ($review->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $review->update($request->only(['rating', 'comment']));

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'data' => $review
        ], 200);
    }

    /**
     * Delete a review
     */
    public function destroy(Request $request, Review $review)
    {
        // Check if review belongs to user
        if ($review->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully'
        ], 200);
    }

    /**
     * Get user's reviews
     */
    public function myReviews(Request $request)
    {
        $reviews = Review::with('item')
                        ->where('user_id', $request->user()->id)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $reviews
        ], 200);
    }
}
