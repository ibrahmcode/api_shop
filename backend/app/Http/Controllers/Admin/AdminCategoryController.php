<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    /**
     * Display a listing of categories (Admin).
     */
    public function index()
    {
        $categories = Category::withCount('items')->get();
        
        return response()->json([
            'success' => true,
            'data' => $categories
        ], 200);
    }
    
    /**
     * Store a newly created category (Admin).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);
        
        $category = Category::create($data);
    
        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    } 

    /**
     * Display the specified category (Admin).
     */
    public function show(Category $category)
    {
        return response()->json([
            'success' => true,
            'data' => $category->load('items')
        ], 200);
    } 
    
    /**
     * Update the specified category (Admin).
     */
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category
        ], 200);
    }

    /**
     * Remove the specified category (Admin).
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ], 200);
    }
}
