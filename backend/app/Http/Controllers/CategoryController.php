<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;


class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::with('items')->get());
    }
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|String|max:255',
            'description' => 'nullable|String',
        ]);
        
        $category = Category::create($data);
    
        return response()->json($category, 201);
    } 

    public function show(Category $category)
    {
        return response()->json($category->load('items'));
    } 
    
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($data);

        return response()->json($category);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json("Category deleted successfully");
    }
}
