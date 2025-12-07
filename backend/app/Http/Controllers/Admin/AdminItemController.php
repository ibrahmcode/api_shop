<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminItemController extends Controller
{
    /**
     * Display a listing of items in a category (Admin).
     */
    public function index(Category $category)
    {
        $items = $category->items()->get();
        
        return response()->json([
            'success' => true,
            'data' => $items
        ], 200);
    }

    /**
     * Store a newly created item (Admin).
     */
    public function store(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $data['category_id'] = $category->id;

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            // Manual validation
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $extension = strtolower($file->getClientOriginalExtension());
            
            if (!in_array($extension, $allowedExtensions)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image format. Allowed: jpg, jpeg, png, gif, webp'
                ], 422);
            }
            
            if ($file->getSize() > 2048000) { // 2MB
                return response()->json([
                    'success' => false,
                    'message' => 'Image size must be less than 2MB'
                ], 422);
            }
            
            try {
                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $extension;
                $destinationPath = storage_path('app/public/items');
                
                // Create directory if not exists
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                // Move file
                $file->move($destinationPath, $filename);
                $data['image'] = 'items/' . $filename;
                
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload image: ' . $e->getMessage()
                ], 500);
            }
        }

        $item = Item::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Item created successfully',
            'data' => $item->load('category')
        ], 201);
    }

    /**
     * Display the specified item (Admin).
     */
    public function show(Category $category, Item $item)
    {
        if ($item->category_id !== $category->id) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in this category'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $item->load('category')
        ], 200);
    }

    /**
     * Update the specified item (Admin).
     */
    public function update(Request $request, Category $category, Item $item)
    {
        if ($item->category_id !== $category->id) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in this category'
            ], 404);
        }

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            // Manual validation
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $extension = strtolower($file->getClientOriginalExtension());
            
            if (!in_array($extension, $allowedExtensions)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image format. Allowed: jpg, jpeg, png, gif, webp'
                ], 422);
            }
            
            if ($file->getSize() > 2048000) { // 2MB
                return response()->json([
                    'success' => false,
                    'message' => 'Image size must be less than 2MB'
                ], 422);
            }
            
            // Delete old image if exists
            if ($item->image) {
                $oldImagePath = storage_path('app/public/' . $item->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            
            // Generate unique filename and save
            $filename = time() . '_' . uniqid() . '.' . $extension;
            $destinationPath = storage_path('app/public/items');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $file->move($destinationPath, $filename);
            $data['image'] = 'items/' . $filename;
        }

        $item->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully',
            'data' => $item->load('category')
        ], 200);
    }

    /**
     * Remove the specified item (Admin).
     */
    public function destroy(Category $category, Item $item)
    {
        if ($item->category_id !== $category->id) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in this category'
            ], 404);
        }

        // Delete image if exists
        if ($item->image) {
            $imagePath = storage_path('app/public/' . $item->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item deleted successfully'
        ], 200);
    }
}
