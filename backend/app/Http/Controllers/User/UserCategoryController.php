<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;

class UserCategoryController extends Controller
{
    /**
     * Display a listing of categories for users (read-only).
     */
    public function index()
    {
        $locale = app()->getLocale();
        $categories = Category::with('items')->get()->map(function ($category) use ($locale) {
            return [
                'id' => $category->id,
                'name' => $category->getLocalizedName($locale),
                'description' => $category->getLocalizedDescription($locale),
                'items_count' => $category->items->count(),
                'created_at' => $category->created_at,
            ];
        });
        
        return response()->json([
            'success' => true,
            'locale' => $locale,
            'data' => $categories
        ], 200);
    }

    /**
     * Display the specified category for users (read-only).
     */
    public function show(Category $category)
    {
        $locale = app()->getLocale();
        
        return response()->json([
            'success' => true,
            'locale' => $locale,
            'data' => [
                'id' => $category->id,
                'name' => $category->getLocalizedName($locale),
                'description' => $category->getLocalizedDescription($locale),
                'items' => $category->items->map(function ($item) use ($locale) {
                    return [
                        'id' => $item->id,
                        'name' => $item->getLocalizedName($locale),
                        'description' => $item->getLocalizedDescription($locale),
                        'price' => $item->price,
                        'image' => $item->image,
                    ];
                }),
            ]
        ], 200);
    }
}
