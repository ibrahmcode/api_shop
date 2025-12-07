<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;

class UserItemController extends Controller
{
    /**
     * Display a listing of items in a category for users with search and filter.
     */
    public function index(Request $request, Category $category)
    {
        $query = $category->items()->with('reviews');

        // Search by name or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'price') {
            $query->orderBy('price', $sortOrder);
        } elseif ($sortBy === 'name') {
            $query->orderBy('name', $sortOrder);
        } elseif ($sortBy === 'rating') {
            $query->withAvg('reviews', 'rating')
                  ->orderBy('reviews_avg_rating', $sortOrder);
        } else {
            $query->orderBy('created_at', $sortOrder);
        }

        $items = $query->paginate($request->get('per_page', 15));

        // Add review stats to each item
        $items->getCollection()->transform(function($item) {
            $item->average_rating = round($item->reviews()->avg('rating') ?? 0, 1);
            $item->total_reviews = $item->reviews()->count();
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $items
        ], 200);
    }

    /**
     * Display the specified item for users (read-only).
     */
    public function show(Category $category, Item $item)
    {
        if ($item->category_id !== $category->id) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in this category'
            ], 404);
        }

        $item->load(['category', 'reviews.user:id,name,avatar']);
        $item->average_rating = round($item->reviews()->avg('rating') ?? 0, 1);
        $item->total_reviews = $item->reviews()->count();

        return response()->json([
            'success' => true,
            'data' => $item
        ], 200);
    }

    /**
     * Search items across all categories
     */
    public function search(Request $request)
    {
        $query = Item::with(['category', 'reviews']);

        // Search keyword
        if ($request->has('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by minimum rating
        if ($request->has('min_rating')) {
            $query->whereHas('reviews', function($q) use ($request) {
                $q->havingRaw('AVG(rating) >= ?', [$request->min_rating]);
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'price') {
            $query->orderBy('price', $sortOrder);
        } elseif ($sortBy === 'name') {
            $query->orderBy('name', $sortOrder);
        } elseif ($sortBy === 'rating') {
            $query->withAvg('reviews', 'rating')
                  ->orderBy('reviews_avg_rating', $sortOrder);
        } else {
            $query->orderBy('created_at', $sortOrder);
        }

        $items = $query->paginate($request->get('per_page', 20));

        // Add review stats
        $items->getCollection()->transform(function($item) {
            $item->average_rating = round($item->reviews()->avg('rating') ?? 0, 1);
            $item->total_reviews = $item->reviews()->count();
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $items
        ], 200);
    }
}
